<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ExperienceCenter;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->isSalesManager(), 403);
        $users = User::with('experienceCenter')->paginate(20);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->isSalesManager(), 403);
        $centers = ExperienceCenter::where('status', 'active')->get();
        return view('users.create', compact('centers'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->isSalesManager(), 403);

        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'email'                => 'required|email|unique:tbl_users,email',
            'password'             => 'required|min:8|confirmed',
            'mobile'               => 'nullable|string|max:15',
            'role'                 => 'required|in:super-admin,sales-manager,sales-executive,experience-center',
            'experience_center_id' => 'nullable|exists:tbl_experience_centers,id',
            'is_active'            => 'boolean',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $user = User::create($validated);
        $user->assignRole($validated['role']);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $currentUser = auth()->user();
        if ($currentUser->isSuperAdmin() || $currentUser->isSalesManager()) {
            // Authorized
        } elseif ($currentUser->isExperienceCenterUser() && $user->experience_center_id === $currentUser->experience_center_id) {
            // Authorized
        } else {
            abort(403, 'Unauthorized.');
        }

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $currentUser = auth()->user();
        if ($currentUser->isSuperAdmin() || $currentUser->isSalesManager()) {
            // Authorized
        } elseif ($currentUser->isExperienceCenterUser() && $user->experience_center_id === $currentUser->experience_center_id) {
            // Authorized
        } else {
            abort(403, 'Unauthorized.');
        }

        $centers = ExperienceCenter::where('status', 'active')
            ->when($currentUser->isExperienceCenterUser(), fn($q) => $q->where('id', $currentUser->experience_center_id))
            ->get();

        return view('users.edit', compact('user', 'centers'));
    }

    public function update(Request $request, User $user)
    {
        $currentUser = auth()->user();
        if ($currentUser->isSuperAdmin() || $currentUser->isSalesManager()) {
            // Authorized
        } elseif ($currentUser->isExperienceCenterUser() && $user->experience_center_id === $currentUser->experience_center_id) {
            // Authorized
        } else {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'email'                => 'required|email|unique:tbl_users,email,' . $user->id,
            'mobile'               => 'nullable|string|max:15',
            'role'                 => 'required|in:super-admin,sales-manager,sales-executive,experience-center',
            'experience_center_id' => 'nullable|exists:tbl_experience_centers,id',
            'is_active'            => 'boolean',
            'password'             => 'nullable|min:8|confirmed',
        ]);

        // Security check for experience center users
        if ($currentUser->isExperienceCenterUser()) {
            // Force same center
            $validated['experience_center_id'] = $currentUser->experience_center_id;
            // Prevent changing role to admin/manager
            if (!in_array($validated['role'], ['sales-executive', 'experience-center'])) {
                $validated['role'] = $user->role; // fallback to current role
            }
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = bcrypt($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active');

        $user->update($validated);
        $user->syncRoles([$validated['role']]);

        if ($currentUser->isExperienceCenterUser()) {
            return redirect()->route('experience-centers.show', $currentUser->experience_center_id)->with('success', 'User updated.');
        }
        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        $currentUser = auth()->user();
        if ($currentUser->isSuperAdmin() || $currentUser->isSalesManager()) {
            // Authorized
        } elseif ($currentUser->isExperienceCenterUser() && $user->experience_center_id === $currentUser->experience_center_id) {
            // Authorized
        } else {
            abort(403, 'Unauthorized.');
        }

        $user->delete();

        if ($currentUser->isExperienceCenterUser()) {
            return redirect()->route('experience-centers.show', $currentUser->experience_center_id)->with('success', 'User deleted.');
        }
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }

    public function impersonate(User $user)
    {
        $currentUser = auth()->user();
        
        // Check if current user is authorized to impersonate the target user
        if ($currentUser->isSuperAdmin()) {
            // Super-admin has full authorization
        } elseif ($currentUser->isExperienceCenterUser() && $user->experience_center_id === $currentUser->experience_center_id) {
            // Experience Center users can only impersonate users within their own center
            if ($currentUser->id === $user->id) {
                abort(403, 'You cannot impersonate yourself.');
            }
        } else {
            abort(403, 'Unauthorized.');
        }

        // Store original user ID in session
        session(['impersonator_user_id' => $currentUser->id]);
        
        // Log in as the new user
        auth()->login($user);
        
        return redirect()->route('dashboard')->with('success', "Logged in as {$user->name}.");
    }

    public function stopImpersonate()
    {
        $originalId = session()->pull('impersonator_user_id');
        if ($originalId) {
            $originalUser = User::find($originalId);
            if ($originalUser) {
                auth()->login($originalUser);
                return redirect()->route('dashboard')->with('success', 'Returned to Admin account.');
            }
        }
        return redirect()->route('login');
    }
}
