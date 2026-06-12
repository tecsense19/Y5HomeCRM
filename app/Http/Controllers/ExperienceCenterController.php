<?php

namespace App\Http\Controllers;

use App\Models\ExperienceCenter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExperienceCenterController extends Controller
{
    public function index()
    {
        $query = ExperienceCenter::withCount('leads');
        if (Auth::user()->isExperienceCenterUser()) {
            $query->where('id', Auth::user()->experience_center_id);
        }
        $centers = $query->latest()->paginate(15);
        return view('experience_centers.index', compact('centers'));
    }

    public function create()
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);
        return view('experience_centers.create');
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);
        $validated = $request->validate([
            'center_name'             => 'required|string|max:255',
            'owner_name'              => 'required|string|max:255',
            'company_name'            => 'nullable|string',
            'gst_number'              => 'nullable|string|max:20',
            'mobile_number'           => 'required|string|max:15',
            'email'                   => 'nullable|email',
            'address'                 => 'nullable|string',
            'city'                    => 'required|string',
            'state'                   => 'required|string',
            'country'                 => 'nullable|string',
            'agreement_start_date'    => 'nullable|date',
            'agreement_end_date'      => 'nullable|date|after:agreement_start_date',
            'security_deposit_amount' => 'nullable|numeric|min:0',
        ]);

        ExperienceCenter::create($validated);

        return redirect()->route('experience-centers.index')
            ->with('success', 'Experience Center created successfully.');
    }

    public function show(ExperienceCenter $experienceCenter)
    {
        if (Auth::user()->isExperienceCenterUser()) {
            abort_unless($experienceCenter->id === Auth::user()->experience_center_id, 403);
        }
        $experienceCenter->load('leads', 'users', 'documents');
        return view('experience_centers.show', compact('experienceCenter'));
    }

    public function edit(ExperienceCenter $experienceCenter)
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);
        return view('experience_centers.edit', compact('experienceCenter'));
    }

    public function update(Request $request, ExperienceCenter $experienceCenter)
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);
        $validated = $request->validate([
            'center_name'             => 'required|string|max:255',
            'owner_name'              => 'required|string|max:255',
            'company_name'            => 'nullable|string',
            'gst_number'              => 'nullable|string|max:20',
            'mobile_number'           => 'required|string|max:15',
            'email'                   => 'nullable|email',
            'address'                 => 'nullable|string',
            'city'                    => 'required|string',
            'state'                   => 'required|string',
            'agreement_start_date'    => 'nullable|date',
            'agreement_end_date'      => 'nullable|date',
            'security_deposit_amount' => 'nullable|numeric|min:0',
        ]);

        $experienceCenter->update($validated);

        return redirect()->route('experience-centers.show', $experienceCenter)
            ->with('success', 'Experience Center updated.');
    }

    public function destroy(ExperienceCenter $experienceCenter)
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);
        $experienceCenter->delete();
        return redirect()->route('experience-centers.index')->with('success', 'Experience Center deleted.');
    }

    public function updateStatus(Request $request, ExperienceCenter $experienceCenter)
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);
        $request->validate(['status' => 'required|in:active,inactive,suspended']);
        $experienceCenter->update(['status' => $request->status]);
        return back()->with('success', 'Status updated.');
    }

    public function addStaff(Request $request, ExperienceCenter $experienceCenter)
    {
        $currentUser = Auth::user();
        if ($currentUser->isSuperAdmin()) {
            // Authorized
        } elseif ($currentUser->isExperienceCenterUser() && $currentUser->experience_center_id === $experienceCenter->id) {
            // Authorized
        } else {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:tbl_users,email',
            'password' => 'required|min:8|confirmed',
            'mobile'   => 'nullable|string|max:15',
            'role'     => 'nullable|in:sales-executive,experience-center',
        ]);

        $role = $validated['role'] ?? 'sales-executive';

        $user = User::create([
            'name'                 => $validated['name'],
            'email'                => $validated['email'],
            'password'             => bcrypt($validated['password']),
            'mobile'               => $validated['mobile'],
            'role'                 => $role,
            'experience_center_id' => $experienceCenter->id,
            'is_active'            => true,
        ]);
        $user->assignRole($role);

        return redirect()->route('experience-centers.show', $experienceCenter)->with('success', 'Staff user created successfully.');
    }

    public function removeStaff(ExperienceCenter $experienceCenter, User $user)
    {
        $currentUser = Auth::user();
        if ($currentUser->isSuperAdmin()) {
            // Authorized
        } elseif ($currentUser->isExperienceCenterUser() && $currentUser->experience_center_id === $experienceCenter->id) {
            // Authorized
        } else {
            abort(403, 'Unauthorized.');
        }

        if ($user->experience_center_id === $experienceCenter->id) {
            $user->delete();
            return redirect()->route('experience-centers.show', $experienceCenter)->with('success', 'Staff user deleted successfully.');
        }
        return redirect()->route('experience-centers.show', $experienceCenter)->with('error', 'User does not belong to this center.');
    }

    public function showImport()
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);
        return view('experience_centers.import');
    }

    public function import(Request $request)
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);
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
                
                // Parse dates if necessary
                $dateFields = ['agreement_start_date', 'agreement_end_date'];
                foreach($dateFields as $df) {
                    if (!empty($data[$df])) {
                        try {
                            $dateStr = str_replace('/', '-', $data[$df]);
                            $data[$df] = \Carbon\Carbon::parse($dateStr)->format('Y-m-d');
                        } catch (\Exception $e) {
                            // Validator will catch if it remains invalid
                        }
                    }
                }
                
                $validator = Validator::make($data, [
                    'center_name'             => 'required|string|max:255',
                    'owner_name'              => 'required|string|max:255',
                    'company_name'            => 'nullable|string',
                    'gst_number'              => 'nullable|string|max:20',
                    'mobile_number'           => 'required|string|max:15',
                    'email'                   => 'nullable|email',
                    'address'                 => 'nullable|string',
                    'city'                    => 'required|string',
                    'state'                   => 'required|string',
                    'country'                 => 'nullable|string',
                    'agreement_start_date'    => 'nullable|date',
                    'agreement_end_date'      => 'nullable|date|after_or_equal:agreement_start_date',
                    'security_deposit_amount' => 'nullable|numeric|min:0',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Row $rowNumber: " . implode(', ', $validator->errors()->all());
                    $errorCount++;
                    $rowNumber++;
                    continue;
                }

                $validatedData = $validator->validated();
                ExperienceCenter::create($validatedData);
                $successCount++;
                $rowNumber++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return back()->with('error', 'Failed to import experience centers: ' . $e->getMessage());
        }

        fclose($handle);

        $message = "Import completed. Successfully imported $successCount centers.";
        if ($errorCount > 0) {
            $message .= " Failed $errorCount rows.";
            return back()->with('success', $message)->with('import_errors', $errors);
        }

        return redirect()->route('experience-centers.index')->with('success', $message);
    }
    
    public function downloadTemplate()
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);
        $headers = [
            'center_name', 'owner_name', 'company_name', 'gst_number', 'mobile_number',
            'email', 'address', 'city', 'state', 'country',
            'agreement_start_date', 'agreement_end_date', 'security_deposit_amount'
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            // Optional: add a sample row
            fputcsv($file, ['Elite Center Mumbai', 'John Doe', 'Elite Solutions Pvt Ltd', '27AAAAA1234A1Z5', '9876543210', 'mumbai@elitecenter.com', '123, Business Park', 'Mumbai', 'Maharashtra', 'India', '2025-01-01', '2026-01-01', '500000']);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=experience_centers_import_template.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }
}
