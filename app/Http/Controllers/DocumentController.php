<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::forUser(Auth::user())->with('uploadedBy')->latest()->paginate(20);
        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file'               => 'required|file|max:25600|mimes:pdf,docx,xlsx,jpg,jpeg,png',
            'category'           => 'required|string',
            'documentable_type'  => 'required|string',
            'documentable_id'    => 'required|integer',
            'notes'              => 'nullable|string',
        ]);

        $file = $request->file('file');
        $path = $file->store('documents/' . $request->category, 'local');

        Document::create([
            'documentable_type' => $request->documentable_type,
            'documentable_id'   => $request->documentable_id,
            'category'          => $request->category,
            'file_name'         => basename($path),
            'original_name'     => $file->getClientOriginalName(),
            'file_path'         => $path,
            'file_size'         => $file->getSize(),
            'mime_type'         => $file->getMimeType(),
            'uploaded_by'       => Auth::id(),
            'notes'             => $request->notes,
        ]);

        $redirectUrl = route('documents.index');
        if ($request->documentable_type === 'App\Models\Customer') {
            $redirectUrl = route('customers.show', $request->documentable_id);
        } elseif ($request->documentable_type === 'App\Models\Lead') {
            $redirectUrl = route('leads.show', $request->documentable_id);
        } elseif ($request->documentable_type === 'App\Models\Opportunity') {
            $redirectUrl = route('opportunities.show', $request->documentable_id);
        } elseif ($request->documentable_type === 'App\Models\Quotation') {
            $redirectUrl = route('quotations.show', $request->documentable_id);
        }

        return redirect($redirectUrl)->with('success', 'Document uploaded successfully.');
    }

    public function show(Document $document)
    {
        abort_unless(Document::forUser(Auth::user())->where('id', $document->id)->exists(), 403);
        return view('documents.show', compact('document'));
    }

    public function download(Document $document)
    {
        abort_unless(Document::forUser(Auth::user())->where('id', $document->id)->exists(), 403);
        abort_unless(Storage::disk('local')->exists($document->file_path), 404);
        return Storage::disk('local')->download($document->file_path, $document->original_name);
    }

    public function destroy(Document $document)
    {
        abort_unless(Document::forUser(Auth::user())->where('id', $document->id)->exists(), 403);
        Storage::disk('local')->delete($document->file_path);
        $document->delete();
        return back()->with('success', 'Document deleted.');
    }
}
