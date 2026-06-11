<?php

namespace App\Http\Controllers;

use App\Models\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuilderController extends Controller
{
    public function index(Request $request)
    {
        $query = Builder::query();
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$s%")
                                      ->orWhere('company_name', 'like', "%$s%")
                                      ->orWhere('email', 'like', "%$s%")
                                      ->orWhere('mobile_number', 'like', "%$s%"));
        }
        $builders = $query->latest()->paginate(20)->withQueryString();
        return view('builders.index', compact('builders'));
    }

    public function create()
    {
        abort_unless(Auth::user()->isSuperAdmin() || Auth::user()->isSalesManager(), 403);
        return view('builders.create');
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->isSuperAdmin() || Auth::user()->isSalesManager(), 403);
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'company_name'  => 'nullable|string|max:255',
            'mobile_number' => 'required|string|max:15',
            'email'         => 'nullable|email',
            'city'          => 'nullable|string|max:100',
            'is_active'     => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $builder = Builder::create($validated);

        return redirect()->route('builders.index')->with('success', 'Builder created successfully.');
    }

    public function show(Builder $builder)
    {
        return view('builders.show', compact('builder'));
    }

    public function edit(Builder $builder)
    {
        abort_unless(Auth::user()->isSuperAdmin() || Auth::user()->isSalesManager(), 403);
        return view('builders.edit', compact('builder'));
    }

    public function update(Request $request, Builder $builder)
    {
        abort_unless(Auth::user()->isSuperAdmin() || Auth::user()->isSalesManager(), 403);
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'company_name'  => 'nullable|string|max:255',
            'mobile_number' => 'required|string|max:15',
            'email'         => 'nullable|email',
            'city'          => 'nullable|string|max:100',
            'is_active'     => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $builder->update($validated);

        return redirect()->route('builders.index')->with('success', 'Builder updated successfully.');
    }

    public function destroy(Builder $builder)
    {
        abort_unless(Auth::user()->isSuperAdmin() || Auth::user()->isSalesManager(), 403);
        $builder->delete();
        return redirect()->route('builders.index')->with('success', 'Builder deleted successfully.');
    }
}
