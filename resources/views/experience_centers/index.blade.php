{{-- experience_centers/index.blade.php --}}
@extends('layouts.app')
@section('title','Experience Centers')
@section('page-title','Experience Centers')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        All Centers
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('experience-centers.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> Add Center</a>
        @endif
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Code</th><th>Center Name</th><th>Owner</th><th>City</th><th>Mobile</th><th>Leads</th><th>Status</th><th></th></tr></thead>
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
                <td><a href="{{ route('experience-centers.show', $c) }}" class="btn btn-sm btn-outline-primary">View</a></td>
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
