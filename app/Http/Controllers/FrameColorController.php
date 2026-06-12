<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrameColorController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $search = request('search');
        $colors = \App\Models\FrameColor::when($search, function($q, $s) {
            return $q->where('name', 'like', "%$s%")
                     ->orWhere('series', 'like', "%$s%")
                     ->orWhere('hex_code', 'like', "%$s%");
        })->orderBy('series')->orderBy('name')->paginate(30)->withQueryString();
        return view('frame_colors.index', compact('colors'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $request->validate([
            'series' => 'required|string',
            'name' => 'required|string|max:255',
            'hex_code' => 'nullable|string|max:10',
        ]);
        
        \App\Models\FrameColor::create([
            'series' => $request->series,
            'name' => $request->name,
            'hex_code' => $request->hex_code,
            'is_active' => true,
        ]);
        return back()->with('success', 'Frame Color added successfully.');
    }

    public function destroy(\App\Models\FrameColor $frameColor)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $frameColor->delete();
        return back()->with('success', 'Frame Color deleted successfully.');
    }

    public function updateStatus(\App\Models\FrameColor $frameColor)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $frameColor->update(['is_active' => !$frameColor->is_active]);
        return back()->with('success', 'Frame Color status updated.');
    }
}
