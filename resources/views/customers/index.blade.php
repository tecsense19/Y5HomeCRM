@extends('layouts.app')
@section('title', 'Customers')
@section('page-title', 'Customer Management')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>All Customers</span>
        <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus"></i> New Customer
        </a>
    </div>

    <!-- Filters -->
    <div class="card-body border-bottom">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-9">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Search name, email, mobile number..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-primary w-100">Search</button>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>City / State</th>
                        <th>Leads Count</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $c)
                    <tr>
                        <td>
                            <a href="{{ route('customers.show', $c) }}" class="fw-semibold text-primary text-decoration-none">
                                {{ $c->name }}
                            </a>
                        </td>
                        <td>{{ $c->mobile_number }}</td>
                        <td>{{ $c->email ?: '–' }}</td>
                        <td>
                            @if($c->city || $c->state)
                                {{ $c->city }}{{ $c->city && $c->state ? ', ' : '' }}{{$c->state}}
                            @else –
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info text-dark">
                                {{ $c->leads_count }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $c->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="d-flex gap-1 justify-content-left">
                                <a href="{{ route('customers.show', $c) }}" class="btn btn-xs btn-outline-primary btn-sm" title="View"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('customers.edit', $c) }}" class="btn btn-xs btn-outline-secondary btn-sm" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('customers.destroy', $c) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger btn-sm" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No customers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($customers->hasPages())
    <div class="card-footer">{{ $customers->links() }}</div>
    @endif
</div>
@endsection
