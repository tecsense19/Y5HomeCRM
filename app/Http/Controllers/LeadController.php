<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Models\ExperienceCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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
            'mobile_number'      => [
                'required', 'string', 'max:15',
                function ($attribute, $value, $fail) {
                    $lockedLead = \App\Models\Lead::where('mobile_number', $value)
                        ->whereNotNull('locked_until')
                        ->where('locked_until', '>', now())
                        ->first();
                    if ($lockedLead) {
                        $fail("A lead with this mobile number is currently locked until {$lockedLead->locked_until->format('d M Y')}.");
                    }
                }
            ],
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

    public function showImport()
    {
        return view('leads.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();
        
        $handle = fopen($filePath, 'r');
        $header = fgetcsv($handle); 
        
        // Sanitize headers (lowercase, replace spaces with underscores)
        $header = array_map(function($col) {
            return strtolower(trim(str_replace(' ', '_', $col)));
        }, $header);

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            $rowNumber = 2; 
            while (($row = fgetcsv($handle)) !== false) {
                if (count($header) !== count($row)) {
                    $errors[] = "Row $rowNumber: Column count doesn't match header.";
                    $errorCount++;
                    $rowNumber++;
                    continue;
                }
                
                $data = array_combine($header, $row);
                
                // Convert empty strings to null to prevent MySQL strict mode errors
                foreach ($data as $key => $value) {
                    if (trim($value) === '') {
                        $data[$key] = null;
                    }
                }
                
                // Parse Excel dates like 1/1/2025 into YYYY-MM-DD
                if (!empty($data['next_followup_date'])) {
                    try {
                        $dateStr = str_replace('/', '-', $data['next_followup_date']);
                        $data['next_followup_date'] = \Carbon\Carbon::parse($dateStr)->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Validator will catch if it remains invalid
                    }
                }
                
                $validator = Validator::make($data, [
                    'customer_name'      => 'required|string|max:255',
                    'mobile_number'      => [
                        'required', 'string', 'max:15',
                        function ($attribute, $value, $fail) {
                            $lockedLead = \App\Models\Lead::where('mobile_number', $value)
                                ->whereNotNull('locked_until')
                                ->where('locked_until', '>', now())
                                ->first();
                            if ($lockedLead) {
                                $fail("A lead with this mobile number is currently locked until {$lockedLead->locked_until->format('d M Y')}.");
                            }
                        }
                    ],
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

                if ($validator->fails()) {
                    $errors[] = "Row $rowNumber: " . implode(', ', $validator->errors()->all());
                    $errorCount++;
                    $rowNumber++;
                    continue;
                }

                $leadData = $validator->validated();
                $leadData['created_by'] = Auth::id();
                $leadData['status'] = 'new';
                
                Lead::create($leadData);
                $successCount++;
                $rowNumber++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return back()->with('error', 'Failed to import leads: ' . $e->getMessage());
        }

        fclose($handle);

        $message = "Import completed. Successfully imported $successCount leads.";
        if ($errorCount > 0) {
            $message .= " Failed $errorCount rows.";
            return back()->with('success', $message)->with('import_errors', $errors);
        }

        return redirect()->route('leads.index')->with('success', $message);
    }
    
    public function downloadTemplate()
    {
        $headers = [
            'customer_name', 'mobile_number', 'email', 'lead_source', 'project_type',
            'construction_stage', 'estimated_budget', 'city', 'state', 'country',
            'project_address', 'remarks', 'assigned_to', 'experience_center_id', 'next_followup_date'
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            // Optional: add a sample row
            fputcsv($file, ['John Doe', '9876543210', 'john@example.com', 'website', 'apartment', 'planning', '5000000', 'Mumbai', 'Maharashtra', 'India', '123 Street', 'Sample lead', '', '', '2025-01-01']);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=leads_import_template.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }

    public function lock(Request $request, Lead $lead)
    {
        $this->authorizeLeadAccess($lead);
        
        $request->validate([
            'lock_days' => 'required|integer|min:1|max:365',
        ]);

        $lead->update([
            'locked_until' => now()->addDays((int) $request->lock_days),
            'locked_from'  => now(),
            'locked_by'    => Auth::id(),
        ]);

        return back()->with('success', "Lead locked for {$request->lock_days} days.");
    }

    public function unlock(Request $request, Lead $lead)
    {
        $this->authorizeLeadAccess($lead);
        $user = Auth::user();
        
        if (!$user->isSuperAdmin() && !$user->isSalesManager() && $lead->locked_by !== $user->id) {
            return back()->with('error', 'You are not authorized to unlock this lead.');
        }

        $lead->update([
            'locked_until' => null,
            'locked_from'  => null,
            'locked_by'    => null,
        ]);

        return back()->with('success', 'Lead unlocked successfully.');
    }

    public function destroy(Lead $lead)
    {
        $this->authorizeLeadAccess($lead);
        
        $user = Auth::user();
        if (!$user->isSuperAdmin() && !$user->isSalesManager()) {
            return back()->with('error', 'You are not authorized to delete leads.');
        }

        $lead->delete();
        return back()->with('success', 'Lead deleted successfully.');
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
