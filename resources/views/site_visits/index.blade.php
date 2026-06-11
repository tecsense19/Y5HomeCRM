{{-- site_visits/index.blade.php --}}
@extends('layouts.app')
@section('title','Site Visits')
@section('page-title','Site Visits')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        All Site Visits
        <a href="{{ route('site-visits.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> Record Visit</a>
    </div>
    <div class="card-header border-top bg-white">
        <form method="GET" class="row g-2">
            <div class="col-md-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name/ID" value="{{ request('search') }}"></div>
            <div class="col-md-3"><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}"></div>
            <div class="col-md-3"><input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}"></div>
            <div class="col-md-2"><button class="btn btn-sm btn-primary w-100">Filter</button></div>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Visit ID</th><th>Customer</th><th>Visit Date</th><th>Visited By</th><th>Location</th><th>Est. Value</th><th></th></tr></thead>
            <tbody>
            @forelse($visits as $v)
            <tr>
                <td class="fw-semibold text-primary">{{ $v->visit_id }}</td>
                <td>{{ $v->customer_name }}</td>
                <td>{{ $v->visit_date->format('d M Y') }}</td>
                <td>{{ $v->visitedBy?->name ?? '–' }}</td>
                <td>{{ $v->location ?? '–' }}</td>
                <td>{{ $v->estimated_project_value ? '₹'.number_format($v->estimated_project_value) : '–' }}</td>
                <td><a href="{{ route('site-visits.show', $v) }}" class="btn btn-sm btn-outline-primary">View</a></td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No site visits.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($visits->hasPages())<div class="card-footer">{{ $visits->links() }}</div>@endif
</div>
@endsection
