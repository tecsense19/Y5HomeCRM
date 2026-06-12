<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $search = request('search');
        $locations = \App\Models\Location::when($search, function($q, $s) {
            return $q->where('name', 'like', "%$s%");
        })->orderBy('name')->paginate(30)->withQueryString();
        return view('locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $request->validate(['name' => 'required|string|max:255|unique:locations,name']);
        \App\Models\Location::create(['name' => $request->name, 'is_active' => true]);
        return back()->with('success', 'Location added successfully.');
    }

    public function destroy(\App\Models\Location $location)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $location->delete();
        return back()->with('success', 'Location deleted successfully.');
    }

    public function updateStatus(\App\Models\Location $location)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $location->update(['is_active' => !$location->is_active]);
        return back()->with('success', 'Location status updated.');
    }
}
