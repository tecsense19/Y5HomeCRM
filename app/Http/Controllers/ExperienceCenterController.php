<?php

namespace App\Http\Controllers;

use App\Models\ExperienceCenter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExperienceCenterController extends Controller
{
    public function index()
    {
        $query = ExperienceCenter::withCount('leads');
        if (Auth::user()->isExperienceCenterUser()) {
            $query->where('id', Auth::user()->experience_center_id);
        }
        $centers = $query->paginate(15);
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
}
