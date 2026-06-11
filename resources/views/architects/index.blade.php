@extends('layouts.app')
@section('title', 'Architects')
@section('page-title', 'Architect Management')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>All Architects</span>
        <a href="{{ route('architects.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> New Architect
        </a>
    </div>

    <!-- Filters -->
    <div class="card-body border-bottom">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-9">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Search name, firm, email, mobile..." value="{{ request('search') }}">
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
                        <th>Firm Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>City</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($architects as $a)
                    <tr>
                        <td>
                            <a href="{{ route('architects.show', $a) }}" class="fw-semibold text-primary text-decoration-none">
                                {{ $a->name }}
                            </a>
                        </td>
                        <td>{{ $a->firm_name ?: '–' }}</td>
                        <td>{{ $a->mobile_number }}</td>
                        <td>{{ $a->email ?: '–' }}</td>
                        <td>{{ $a->city ?: '–' }}</td>
                        <td>
                            @if($a->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('architects.show', $a) }}" class="btn btn-xs btn-outline-primary btn-sm">View</a>
                                <a href="{{ route('architects.edit', $a) }}" class="btn btn-xs btn-outline-secondary btn-sm">Edit</a>
                                <form action="{{ route('architects.destroy', $a) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this architect?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger btn-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No architects found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($architects->hasPages())
    <div class="card-footer">{{ $architects->links() }}</div>
    @endif
</div>
@endsection
