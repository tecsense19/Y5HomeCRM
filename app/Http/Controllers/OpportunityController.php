<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpportunityController extends Controller
{
    public function index(Request $request)
    {
        $query = Opportunity::forUser(Auth::user())->with('lead', 'assignedTo');

        if ($request->filled('stage'))  $query->where('stage', $request->stage);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('customer_name', 'like', "%$s%")
                                      ->orWhere('opportunity_number', 'like', "%$s%"));
        }

        $opportunities = $query->latest()->paginate(20)->withQueryString();
        return view('opportunities.index', compact('opportunities'));
    }

    public function create()
    {
        $executives = User::whereIn('role', ['sales-executive', 'sales-manager'])->where('is_active', true)->get();
        return view('opportunities.create', compact('executives'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'         => 'required|string|max:255',
            'project_name'          => 'nullable|string',
            'expected_revenue'      => 'nullable|numeric',
            'expected_closing_date' => 'nullable|date',
            'probability'           => 'nullable|integer|min:0|max:100',
            'stage'                 => 'required|string',
            'notes'                 => 'nullable|string',
            'assigned_to'           => 'nullable|exists:tbl_users,id',
        ]);

        $validated['created_by'] = Auth::id();
        $opp = Opportunity::create($validated);

        return redirect()->route('opportunities.show', $opp)
             ->with('success', "Opportunity {$opp->opportunity_number} created.");
    }

    public function show(Opportunity $opportunity)
    {
        abort_unless(Opportunity::forUser(Auth::user())->where('id', $opportunity->id)->exists(), 403);
        $opportunity->load('lead', 'quotations', 'assignedTo');
        return view('opportunities.show', compact('opportunity'));
    }

    public function edit(Opportunity $opportunity)
    {
        abort_unless(Opportunity::forUser(Auth::user())->where('id', $opportunity->id)->exists(), 403);
        $executives = User::whereIn('role', ['sales-executive', 'sales-manager'])->where('is_active', true)->get();
        return view('opportunities.edit', compact('opportunity', 'executives'));
    }

    public function update(Request $request, Opportunity $opportunity)
    {
        abort_unless(Opportunity::forUser(Auth::user())->where('id', $opportunity->id)->exists(), 403);
        $validated = $request->validate([
            'customer_name'         => 'required|string',
            'project_name'          => 'nullable|string',
            'expected_revenue'      => 'nullable|numeric',
            'expected_closing_date' => 'nullable|date',
            'probability'           => 'nullable|integer|min:0|max:100',
            'stage'                 => 'required|string',
            'notes'                 => 'nullable|string',
            'assigned_to'           => 'nullable|exists:tbl_users,id',
        ]);

        $opportunity->update($validated);

        // Notify admin if won
        if ($validated['stage'] === 'won' && $opportunity->wasChanged('stage')) {
            // Mail::to(admin)->queue(new OpportunityWonMail($opportunity));
        }

        return redirect()->route('opportunities.show', $opportunity)->with('success', 'Opportunity updated.');
    }

    public function destroy(Opportunity $opportunity)
    {
        abort_unless(Opportunity::forUser(Auth::user())->where('id', $opportunity->id)->exists(), 403);
        $opportunity->delete();
        return redirect()->route('opportunities.index')->with('success', 'Opportunity deleted.');
    }

    public function updateStage(Request $request, Opportunity $opportunity)
    {
        abort_unless(Opportunity::forUser(Auth::user())->where('id', $opportunity->id)->exists(), 403);
        $request->validate(['stage' => 'required|in:' . implode(',', array_keys(Opportunity::stages()))]);
        $opportunity->update(['stage' => $request->stage]);
        return back()->with('success', 'Stage updated.');
    }
}
