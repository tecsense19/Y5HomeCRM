<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::forUser(Auth::user())->withCount('leads');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$s%")
                                      ->orWhere('mobile_number', 'like', "%$s%")
                                      ->orWhere('email', 'like', "%$s%"));
        }
        $customers = $query->paginate(20)->withQueryString();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'mobile_number' => 'required|string|max:15',
            'email'         => 'nullable|email',
            'address'       => 'nullable|string',
            'city'          => 'nullable|string',
            'state'         => 'nullable|string',
            'notes'         => 'nullable|string',
        ]);
        $validated['created_by'] = Auth::id();
        $customer = Customer::create($validated);
        return redirect()->route('customers.show', $customer)->with('success', 'Customer created.');
    }

    public function show(Customer $customer)
    {
        abort_unless(Customer::forUser(Auth::user())->where('id', $customer->id)->exists(), 403);
        $customer->load('leads', 'siteVisits', 'documents');
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        abort_unless(Customer::forUser(Auth::user())->where('id', $customer->id)->exists(), 403);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        abort_unless(Customer::forUser(Auth::user())->where('id', $customer->id)->exists(), 403);
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'mobile_number' => 'required|string|max:15',
            'email'         => 'nullable|email',
            'address'       => 'nullable|string',
            'city'          => 'nullable|string',
            'state'         => 'nullable|string',
            'notes'         => 'nullable|string',
        ]);
        $customer->update($validated);
        return redirect()->route('customers.show', $customer)->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        abort_unless(Customer::forUser(Auth::user())->where('id', $customer->id)->exists(), 403);
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted.');
    }
}
