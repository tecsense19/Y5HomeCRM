<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been deactivated.']);
            }

            $user->update(['last_login_at' => now()]);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function profile()
    {
        return view('auth.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name'   => 'required|string|max:255',
            'mobile' => 'nullable|string|max:15',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $data = $request->only('name', 'mobile');
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }
}
