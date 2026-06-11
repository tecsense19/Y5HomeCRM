{{-- reports/experience_center.blade.php --}}
@extends('layouts.app')
@section('title', 'Experience Center Report')
@section('page-title', 'Experience Center Performance')

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light fw-semibold">
        <i class="bi bi-funnel me-1"></i> Filters
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Experience Center</label>
                <select name="center_id" class="form-select form-select-sm">
                    <option value="">All Centers</option>
                    @foreach($centers as $center)
                        <option value="{{ $center->id }}" {{ request('center_id') == $center->id ? 'selected' : '' }}>
                            {{ $center->center_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Apply
                </button>
                <a href="{{ route('reports.experience-center') }}" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="bi bi-x"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header fw-semibold">Experience Center Performance</div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Center Name</th>
                    <th class="text-end">Leads</th>
                    <th class="text-end">Site Visits</th>
                    <th class="text-end">Quotations</th>
                    <th class="text-end">Won</th>
                    <th class="text-end">Conversion %</th>
                    <th class="text-end">Revenue (₹)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $row)
                <tr>
                    <td class="fw-semibold">{{ $row['center_name'] }}</td>
                    <td class="text-end">{{ $row['leads_assigned'] }}</td>
                    <td class="text-end">{{ $row['site_visits'] }}</td>
                    <td class="text-end">{{ $row['quotations'] }}</td>
                    <td class="text-end text-success fw-bold">{{ $row['won_projects'] }}</td>
                    <td class="text-end">
                        @php $pct = $row['leads_assigned'] > 0 ? round($row['won_projects'] * 100 / $row['leads_assigned'], 1) : 0; @endphp
                        <span class="badge {{ $pct >= 30 ? 'bg-success' : ($pct >= 10 ? 'bg-warning text-dark' : 'bg-secondary') }}">
                            {{ $pct }}%
                        </span>
                    </td>
                    <td class="text-end fw-semibold">₹{{ number_format($row['revenue']) }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No data for the selected filters.</td></tr>
                @endforelse
            </tbody>
            @if(count($data) > 0)
            <tfoot class="table-light fw-bold">
                <tr>
                    <td>Total</td>
                    <td class="text-end">{{ collect($data)->sum('leads_assigned') }}</td>
                    <td class="text-end">{{ collect($data)->sum('site_visits') }}</td>
                    <td class="text-end">{{ collect($data)->sum('quotations') }}</td>
                    <td class="text-end text-success">{{ collect($data)->sum('won_projects') }}</td>
                    <td class="text-end">
                        @php
                            $tl = collect($data)->sum('leads_assigned');
                            $tw = collect($data)->sum('won_projects');
                        @endphp
                        {{ $tl > 0 ? round($tw * 100 / $tl, 1) : 0 }}%
                    </td>
                    <td class="text-end">₹{{ number_format(collect($data)->sum('revenue')) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
        </div>
    </div>
</div>
@endsection
