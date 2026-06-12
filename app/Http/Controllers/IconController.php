<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IconController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }
        $search = request('search');
        $icons = \App\Models\Icon::when($search, function($q, $s) {
            return $q->where('name', 'like', "%$s%");
        })->latest()->paginate(30)->withQueryString();
        return view('icons.index', compact('icons'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $path = $request->file('icon')->store('icons', 'public');

        \App\Models\Icon::create([
            'name' => $request->name,
            'image_path' => $path,
            'is_active' => true,
        ]);

        return back()->with('success', 'Icon uploaded successfully.');
    }

    public function update(Request $request, \App\Models\Icon $icon)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $data = ['name' => $request->name];

        if ($request->hasFile('icon')) {
            if (!str_starts_with($icon->image_path, 'system:')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($icon->image_path);
            }
            $data['image_path'] = $request->file('icon')->store('icons', 'public');
        }

        $icon->update($data);

        return back()->with('success', 'Icon updated successfully.');
    }

    public function destroy(\App\Models\Icon $icon)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }
        \Illuminate\Support\Facades\Storage::disk('public')->delete($icon->image_path);
        $icon->delete();
        return back()->with('success', 'Icon deleted successfully.');
    }

    public function updateStatus(\App\Models\Icon $icon)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }
        $icon->update(['is_active' => !$icon->is_active]);
        return back()->with('success', 'Icon status updated.');
    }
}
