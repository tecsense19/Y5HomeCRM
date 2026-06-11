<?php

namespace App\Http\Controllers;

use App\Models\Architect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArchitectController extends Controller
{
    public function index(Request $request)
    {
        $query = Architect::query();
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$s%")
                                      ->orWhere('firm_name', 'like', "%$s%")
                                      ->orWhere('email', 'like', "%$s%")
                                      ->orWhere('mobile_number', 'like', "%$s%"));
        }
        $architects = $query->latest()->paginate(20)->withQueryString();
        return view('architects.index', compact('architects'));
    }

    public function create()
    {
        abort_unless(Auth::user()->isSuperAdmin() || Auth::user()->isSalesManager(), 403);
        return view('architects.create');
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->isSuperAdmin() || Auth::user()->isSalesManager(), 403);
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'firm_name'     => 'nullable|string|max:255',
            'mobile_number' => 'required|string|max:15',
            'email'         => 'nullable|email',
            'city'          => 'nullable|string|max:100',
            'is_active'     => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $architect = Architect::create($validated);

        return redirect()->route('architects.index')->with('success', 'Architect created successfully.');
    }

    public function show(Architect $architect)
    {
        return view('architects.show', compact('architect'));
    }

    public function edit(Architect $architect)
    {
        abort_unless(Auth::user()->isSuperAdmin() || Auth::user()->isSalesManager(), 403);
        return view('architects.edit', compact('architect'));
    }

    public function update(Request $request, Architect $architect)
    {
        abort_unless(Auth::user()->isSuperAdmin() || Auth::user()->isSalesManager(), 403);
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'firm_name'     => 'nullable|string|max:255',
            'mobile_number' => 'required|string|max:15',
            'email'         => 'nullable|email',
            'city'          => 'nullable|string|max:100',
            'is_active'     => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $architect->update($validated);

        return redirect()->route('architects.index')->with('success', 'Architect updated successfully.');
    }

    public function destroy(Architect $architect)
    {
        abort_unless(Auth::user()->isSuperAdmin() || Auth::user()->isSalesManager(), 403);
        $architect->delete();
        return redirect()->route('architects.index')->with('success', 'Architect deleted successfully.');
    }
}
