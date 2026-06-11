<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Models\ExperienceCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Lead::forUser($user)->with('assignedTo', 'experienceCenter');

        // Filters
        if ($request->filled('status'))     $query->where('status', $request->status);
        if ($request->filled('source'))     $query->where('lead_source', $request->source);
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($q2) => $q2->where('customer_name', 'like', "%$q%")
                                        ->orWhere('mobile_number', 'like', "%$q%")
                                        ->orWhere('lead_number', 'like', "%$q%"));
        }
        if ($request->filled('center'))     $query->where('experience_center_id', $request->center);
        if ($request->filled('date_from'))  $query->whereDate('lead_creation_date', '>=', $request->date_from);
        if ($request->filled('date_to'))    $query->whereDate('lead_creation_date', '<=', $request->date_to);

        $leads   = $query->latest()->paginate(20)->withQueryString();
        $centers = ExperienceCenter::active()
            ->when($user->isExperienceCenterUser(), fn($q) => $q->where('id', $user->experience_center_id))
            ->get();

        return view('leads.index', compact('leads', 'centers'));
    }

    public function create()
    {
        $isCenterUser = Auth::user()->isExperienceCenterUser();
        $centerId = Auth::user()->experience_center_id;

        $centers = ExperienceCenter::where('status', 'active')
            ->when($isCenterUser, fn($q) => $q->where('id', $centerId))
            ->get();

        $executives = User::where('role', 'sales-executive')
            ->where('is_active', true)
            ->when($isCenterUser, fn($q) => $q->where('experience_center_id', $centerId))
            ->get();

        return view('leads.create', compact('centers', 'executives'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'      => 'required|string|max:255',
            'mobile_number'      => 'required|string|max:15',
            'email'              => 'nullable|email',
            'lead_source'        => 'nullable|string',
            'project_type'       => 'nullable|string',
            'construction_stage' => 'nullable|string',
            'estimated_budget'   => 'nullable|numeric',
            'city'               => 'nullable|string',
            'state'              => 'nullable|string',
            'country'            => 'nullable|string',
            'project_address'    => 'nullable|string',
            'remarks'            => 'nullable|string',
            'assigned_to'        => 'nullable|exists:tbl_users,id',
            'experience_center_id' => 'nullable|exists:tbl_experience_centers,id',
            'next_followup_date' => 'nullable|date',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status']     = 'new';

        $lead = Lead::create($validated);

        // Notify sales manager
        $this->notifySalesManager($lead, 'created');

        return redirect()->route('leads.show', $lead)
            ->with('success', "Lead {$lead->lead_number} created successfully.");
    }

    public function show(Lead $lead)
    {
        $this->authorizeLeadAccess($lead);
        $lead->load('assignedTo', 'experienceCenter', 'siteVisits', 'opportunities.quotations', 'documents');
        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $this->authorizeLeadAccess($lead);
        $isCenterUser = Auth::user()->isExperienceCenterUser();
        $centerId = Auth::user()->experience_center_id;

        $centers = ExperienceCenter::where('status', 'active')
            ->when($isCenterUser, fn($q) => $q->where('id', $centerId))
            ->get();

        $executives = User::where('role', 'sales-executive')
            ->where('is_active', true)
            ->when($isCenterUser, fn($q) => $q->where('experience_center_id', $centerId))
            ->get();

        return view('leads.edit', compact('lead', 'centers', 'executives'));
    }

    public function update(Request $request, Lead $lead)
    {
        $this->authorizeLeadAccess($lead);

        $validated = $request->validate([
            'customer_name'      => 'required|string|max:255',
            'mobile_number'      => 'required|string|max:15',
            'email'              => 'nullable|email',
            'lead_source'        => 'nullable|string',
            'project_type'       => 'nullable|string',
            'construction_stage' => 'nullable|string',
            'estimated_budget'   => 'nullable|numeric',
            'city'               => 'nullable|string',
            'state'              => 'nullable|string',
            'remarks'            => 'nullable|string',
            'next_followup_date' => 'nullable|date',
        ]);

        $lead->update($validated);

        return redirect()->route('leads.show', $lead)->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $user = Auth::user();
        abort_unless($user->isSuperAdmin() || $user->isSalesManager(), 403);

        $lead->delete();
        return redirect()->route('leads.index')->with('success', 'Lead deleted.');
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $this->authorizeLeadAccess($lead);

        $request->validate([
            'status'      => 'required|in:' . implode(',', array_keys(Lead::statuses())),
            'lost_reason' => 'nullable|string',
        ]);

        $lead->update([
            'status'      => $request->status,
            'lost_reason' => $request->lost_reason,
        ]);

        return back()->with('success', 'Lead status updated.');
    }

    public function assign(Request $request, Lead $lead)
    {
        $user = Auth::user();
        abort_unless($user->isSuperAdmin() || $user->isSalesManager(), 403);

        $request->validate([
            'assigned_to'          => 'nullable|exists:tbl_users,id',
            'experience_center_id' => 'nullable|exists:tbl_experience_centers,id',
        ]);

        $lead->update($request->only('assigned_to', 'experience_center_id'));

        // Notify experience center user
        if ($request->experience_center_id) {
            $this->notifyExperienceCenter($lead);
        }

        return back()->with('success', 'Lead assigned successfully.');
    }

    private function authorizeLeadAccess(Lead $lead): void
    {
        $user = Auth::user();
        if ($user->isSuperAdmin() || $user->isSalesManager()) return;
        if ($user->isExperienceCenterUser() && $lead->experience_center_id === $user->experience_center_id) return;
        if ($lead->assigned_to === $user->id) return;
        abort(403);
    }

    private function notifySalesManager(Lead $lead, string $event): void
    {
        // Email automation - Automation 1
        $managers = User::where('role', 'sales-manager')->where('is_active', true)->get();
        foreach ($managers as $manager) {
            // Mail::to($manager->email)->queue(new \App\Mail\LeadCreatedMail($lead, $manager));
        }
    }

    private function notifyExperienceCenter(Lead $lead): void
    {
        // Email automation - Automation 2
        if ($lead->experience_center_id) {
            $centerUsers = User::where('experience_center_id', $lead->experience_center_id)
                ->where('is_active', true)->get();
            foreach ($centerUsers as $u) {
                // Mail::to($u->email)->queue(new \App\Mail\LeadAssignedMail($lead, $u));
            }
        }
    }
}
