<?php

namespace App\Http\Controllers;

use App\Models\SiteVisit;
use App\Models\Lead;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiteVisitController extends Controller
{
    public function index(Request $request)
    {
        $query = SiteVisit::forUser(Auth::user())->with('lead', 'visitedBy');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('customer_name', 'like', "%$s%")
                                      ->orWhere('visit_id', 'like', "%$s%"));
        }
        if ($request->filled('date_from')) $query->whereDate('visit_date', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('visit_date', '<=', $request->date_to);

        $visits = $query->latest('visit_date')->paginate(20)->withQueryString();
        return view('site_visits.index', compact('visits'));
    }

    public function create(Request $request)
    {
        $leads = Lead::forUser(Auth::user())->whereNotIn('status', ['won', 'lost'])->get();
        $users = User::where('is_active', true)
            ->when(Auth::user()->isExperienceCenterUser(), fn($q) => $q->where('experience_center_id', Auth::user()->experience_center_id))
            ->get();
        $selectedLead = $request->lead_id ? Lead::forUser(Auth::user())->find($request->lead_id) : null;
        return view('site_visits.create', compact('leads', 'users', 'selectedLead'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lead_id'                 => 'nullable|exists:tbl_leads,id',
            'customer_name'           => 'required|string',
            'visit_date'              => 'required|date',
            'visited_by'              => 'nullable|exists:tbl_users,id',
            'location'                => 'nullable|string',
            'requirement_summary'     => 'nullable|string',
            'products_required'       => 'nullable|array',
            'estimated_project_value' => 'nullable|numeric',
            'notes'                   => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $visit = SiteVisit::create($validated);

        // Upload attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store("documents/site_visits/{$visit->id}", 'local');
                Document::create([
                    'documentable_type' => SiteVisit::class,
                    'documentable_id'   => $visit->id,
                    'category'          => 'site_photos',
                    'file_name'         => basename($path),
                    'original_name'     => $file->getClientOriginalName(),
                    'file_path'         => $path,
                    'file_size'         => $file->getSize(),
                    'mime_type'         => $file->getMimeType(),
                    'uploaded_by'       => Auth::id(),
                ]);
            }
        }

        // Update lead status
        if ($validated['lead_id'] ?? false) {
            Lead::find($validated['lead_id'])->update(['status' => 'site_visit_completed']);
        }

        return redirect()->route('site-visits.show', $visit)
            ->with('success', "Site visit {$visit->visit_id} recorded.");
    }

    public function show(SiteVisit $siteVisit)
    {
        abort_unless(SiteVisit::forUser(Auth::user())->where('id', $siteVisit->id)->exists(), 403);
        $siteVisit->load('lead', 'visitedBy', 'documents');
        return view('site_visits.show', compact('siteVisit'));
    }

    public function edit(SiteVisit $siteVisit)
    {
        abort_unless(SiteVisit::forUser(Auth::user())->where('id', $siteVisit->id)->exists(), 403);
        $leads = Lead::forUser(Auth::user())->whereNotIn('status', ['won', 'lost'])->get();
        $users = User::where('is_active', true)
            ->when(Auth::user()->isExperienceCenterUser(), fn($q) => $q->where('experience_center_id', Auth::user()->experience_center_id))
            ->get();
        return view('site_visits.edit', compact('siteVisit', 'leads', 'users'));
    }

    public function update(Request $request, SiteVisit $siteVisit)
    {
        abort_unless(SiteVisit::forUser(Auth::user())->where('id', $siteVisit->id)->exists(), 403);
        $validated = $request->validate([
            'customer_name'           => 'required|string',
            'visit_date'              => 'required|date',
            'visited_by'              => 'nullable|exists:tbl_users,id',
            'location'                => 'nullable|string',
            'requirement_summary'     => 'nullable|string',
            'products_required'       => 'nullable|array',
            'estimated_project_value' => 'nullable|numeric',
            'notes'                   => 'nullable|string',
        ]);

        $siteVisit->update($validated);
        return redirect()->route('site-visits.show', $siteVisit)->with('success', 'Site visit updated.');
    }

    public function destroy(SiteVisit $siteVisit)
    {
        abort_unless(SiteVisit::forUser(Auth::user())->where('id', $siteVisit->id)->exists(), 403);
        $siteVisit->delete();
        return redirect()->route('site-visits.index')->with('success', 'Site visit deleted.');
    }
}
