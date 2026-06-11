{{-- experience_centers/index.blade.php --}}
@extends('layouts.app')
@section('title','Experience Centers')
@section('page-title','Experience Centers')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        All Centers
        @if(auth()->user()->isSuperAdmin())
        <div>
            <a href="{{ route('experience-centers.import') }}" class="btn btn-secondary btn-sm me-2">
                <i class="bi bi-upload"></i> Import Centers
            </a>
            <a href="{{ route('experience-centers.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Add Center
            </a>
        </div>
        @endif
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Code</th><th>Center Name</th><th>Owner</th><th>City</th><th>Mobile</th><th>Leads</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            @forelse($centers as $c)
            <tr>
                <td class="fw-semibold text-primary">{{ $c->center_code }}</td>
                <td>{{ $c->center_name }}</td>
                <td>{{ $c->owner_name }}</td>
                <td>{{ $c->city }}, {{ $c->state }}</td>
                <td>{{ $c->mobile_number }}</td>
                <td><span class="badge bg-secondary">{{ $c->leads_count }}</span></td>
                <td>
                    @if($c->status === 'active')<span class="badge bg-success">Active</span>
                    @elseif($c->status === 'suspended')<span class="badge bg-warning text-dark">Suspended</span>
                    @else<span class="badge bg-secondary">Inactive</span>@endif
                </td>
                <td>
                    <div class="d-flex gap-1 justify-content-left">
                        <a href="{{ route('experience-centers.show', $c) }}" class="btn btn-xs btn-outline-primary btn-sm" title="View"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('experience-centers.edit', $c) }}" class="btn btn-xs btn-outline-secondary btn-sm" title="Edit"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('experience-centers.destroy', $c) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this experience center?');" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-outline-danger btn-sm" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-4">No experience centers.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($centers->hasPages())<div class="card-footer">{{ $centers->links() }}</div>@endif
</div>
@endsection
