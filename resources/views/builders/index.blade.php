@extends('layouts.app')
@section('title', 'Builders')
@section('page-title', 'Builder Management')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>All Builders</span>
        <a href="{{ route('builders.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> New Builder
        </a>
    </div>

    <!-- Filters -->
    <div class="card-body border-bottom">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-9">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Search name, company, email, mobile..." value="{{ request('search') }}">
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
                        <th>Company Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>City</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($builders as $b)
                    <tr>
                        <td>
                            <a href="{{ route('builders.show', $b) }}" class="fw-semibold text-primary text-decoration-none">
                                {{ $b->name }}
                            </a>
                        </td>
                        <td>{{ $b->company_name ?: '–' }}</td>
                        <td>{{ $b->mobile_number }}</td>
                        <td>{{ $b->email ?: '–' }}</td>
                        <td>{{ $b->city ?: '–' }}</td>
                        <td>
                            @if($b->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('builders.show', $b) }}" class="btn btn-xs btn-outline-primary btn-sm">View</a>
                                <a href="{{ route('builders.edit', $b) }}" class="btn btn-xs btn-outline-secondary btn-sm">Edit</a>
                                <form action="{{ route('builders.destroy', $b) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this builder?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger btn-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No builders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($builders->hasPages())
    <div class="card-footer">{{ $builders->links() }}</div>
    @endif
</div>
@endsection
