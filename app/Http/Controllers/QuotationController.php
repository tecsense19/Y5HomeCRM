<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Document;
use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $query = Quotation::forUser(Auth::user())->with('opportunity', 'preparedBy');

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('customer_name', 'like', "%$s%")
                                      ->orWhere('quotation_number', 'like', "%$s%"));
        }

        $quotations = $query->latest()->paginate(20)->withQueryString();
        return view('quotations.index', compact('quotations'));
    }

    public function create()
    {
        $opportunities = Opportunity::forUser(Auth::user())->whereNotIn('stage', ['won', 'lost'])->get();
        return view('quotations.create', compact('opportunities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'    => 'required|string',
            'opportunity_id'   => 'nullable|exists:tbl_opportunities,id',
            'quotation_date'   => 'required|date',
            'quotation_value'  => 'nullable|numeric',
            'status'           => 'required|in:draft,sent,approved,rejected',
            'notes'            => 'nullable|string',
            'items'            => 'nullable|string',
        ]);

        $validated['prepared_by'] = Auth::id();
        $validated['created_by']  = Auth::id();

        // Auto-calculate from items if present
        $items = null;
        if ($request->filled('items')) {
            $items = json_decode($request->items, true);
        }

        if (is_array($items) && count($items) > 0) {
            $totalVal = 0;
            foreach ($items as $item) {
                $qty = floatval($item['quantity'] ?? 0);
                $rate = floatval($item['rate'] ?? 0);
                $disc = floatval($item['discount'] ?? 0);
                $gst = floatval($item['gst_rate'] ?? 0);
                
                $gross = $qty * $rate;
                $net = $gross - ($gross * $disc / 100);
                $total = $net + ($net * $gst / 100);
                $totalVal += $total;
            }
            $validated['quotation_value'] = round($totalVal, 2);
            $validated['items'] = $items;
        } else {
            $validated['items'] = null;
        }

        if ($request->filled('opportunity_id')) {
            $opp = Opportunity::find($request->opportunity_id);
            if ($opp) {
                $validated['customer_id'] = $opp->customer_id;
            }
        }

        $quotation = Quotation::create($validated);

        // Handle file uploads
        if ($request->hasFile('quotation_pdf')) {
            $this->uploadDocument($request->file('quotation_pdf'), $quotation, 'quotation_pdf');
        }
        if ($request->hasFile('boq_pdf')) {
            $this->uploadDocument($request->file('boq_pdf'), $quotation, 'boq_pdf');
        }
        if ($request->hasFile('drawings')) {
            foreach ($request->file('drawings') as $file) {
                $this->uploadDocument($file, $quotation, 'drawings');
            }
        }

        return redirect()->route('quotations.show', $quotation)
            ->with('success', "Quotation {$quotation->quotation_number} created.");
    }

    public function show(Quotation $quotation)
    {
        abort_unless(Quotation::forUser(Auth::user())->where('id', $quotation->id)->exists(), 403);
        $quotation->load('opportunity', 'preparedBy', 'documents');
        return view('quotations.show', compact('quotation'));
    }

    public function print(Quotation $quotation)
    {
        abort_unless(Quotation::forUser(Auth::user())->where('id', $quotation->id)->exists(), 403);
        $quotation->load('opportunity.lead.experienceCenter', 'preparedBy');
        return view('quotations.print', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        abort_unless(Quotation::forUser(Auth::user())->where('id', $quotation->id)->exists(), 403);
        $opportunities = Opportunity::forUser(Auth::user())->whereNotIn('stage', ['won', 'lost'])->get();
        $quotation->load('documents');
        return view('quotations.edit', compact('quotation', 'opportunities'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        abort_unless(Quotation::forUser(Auth::user())->where('id', $quotation->id)->exists(), 403);
        $validated = $request->validate([
            'customer_name'  => 'required|string',
            'quotation_date' => 'required|date',
            'quotation_value'=> 'nullable|numeric',
            'status'         => 'required|in:draft,sent,approved,rejected',
            'notes'          => 'nullable|string',
            'items'          => 'nullable|string',
        ]);

        // Auto-calculate from items if present
        $items = null;
        if ($request->filled('items')) {
            $items = json_decode($request->items, true);
        }

        if (is_array($items) && count($items) > 0) {
            $totalVal = 0;
            foreach ($items as $item) {
                $qty = floatval($item['quantity'] ?? 0);
                $rate = floatval($item['rate'] ?? 0);
                $disc = floatval($item['discount'] ?? 0);
                $gst = floatval($item['gst_rate'] ?? 0);
                
                $gross = $qty * $rate;
                $net = $gross - ($gross * $disc / 100);
                $total = $net + ($net * $gst / 100);
                $totalVal += $total;
            }
            $validated['quotation_value'] = round($totalVal, 2);
            $validated['items'] = $items;
        } else {
            $validated['items'] = null;
        }

        $quotation->update($validated);

        // Handle new file uploads
        if ($request->hasFile('quotation_pdf')) {
            $this->uploadDocument($request->file('quotation_pdf'), $quotation, 'quotation_pdf');
        }
        if ($request->hasFile('boq_pdf')) {
            $this->uploadDocument($request->file('boq_pdf'), $quotation, 'boq_pdf');
        }
        if ($request->hasFile('drawings')) {
            foreach ($request->file('drawings') as $file) {
                $this->uploadDocument($file, $quotation, 'drawings');
            }
        }

        return redirect()->route('quotations.show', $quotation)->with('success', 'Quotation updated.');
    }

    public function destroy(Quotation $quotation)
    {
        abort_unless(Quotation::forUser(Auth::user())->where('id', $quotation->id)->exists(), 403);
        $quotation->delete();
        return redirect()->route('quotations.index')->with('success', 'Quotation deleted.');
    }

    public function updateStatus(Request $request, Quotation $quotation)
    {
        abort_unless(Quotation::forUser(Auth::user())->where('id', $quotation->id)->exists(), 403);
        $request->validate(['status' => 'required|in:draft,sent,approved,rejected']);
        $quotation->update(['status' => $request->status]);
        return back()->with('success', 'Status updated.');
    }

    public function destroyDocument(Quotation $quotation, Document $document)
    {
        abort_unless(Quotation::forUser(Auth::user())->where('id', $quotation->id)->exists(), 403);
        Storage::disk('local')->delete($document->file_path);
        $document->delete();
        return back()->with('success', 'Document deleted.');
    }

    private function uploadDocument($file, $model, string $category): void
    {
        $path = $file->store('documents/' . class_basename($model) . '/' . $model->id, 'local');
        Document::create([
            'documentable_type' => get_class($model),
            'documentable_id'   => $model->id,
            'category'          => $category,
            'file_name'         => basename($path),
            'original_name'     => $file->getClientOriginalName(),
            'file_path'         => $path,
            'file_size'         => $file->getSize(),
            'mime_type'         => $file->getMimeType(),
            'uploaded_by'       => Auth::id(),
        ]);
    }
}
