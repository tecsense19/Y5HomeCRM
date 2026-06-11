{{-- reports/sales_pipeline.blade.php --}}
@extends('layouts.app')
@section('title', 'Sales Pipeline')
@section('page-title', 'Sales Pipeline')

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light fw-semibold">
        <i class="bi bi-funnel me-1"></i> Filters
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Stage</label>
                <select name="stage" class="form-select form-select-sm">
                    <option value="">All Stages</option>
                    @foreach(\App\Models\Opportunity::stages() as $k => $v)
                        <option value="{{ $k }}" {{ request('stage') === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Customer</label>
                <input type="text" name="customer" class="form-control form-control-sm" placeholder="Name..." value="{{ request('customer') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Min Value (₹)</label>
                <input type="number" name="min_value" class="form-control form-control-sm" placeholder="0" value="{{ request('min_value') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Max Value (₹)</label>
                <input type="number" name="max_value" class="form-control form-control-sm" placeholder="Any" value="{{ request('max_value') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Closing By</label>
                <input type="date" name="closing_before" class="form-control form-control-sm" value="{{ request('closing_before') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Apply
                </button>
                <a href="{{ route('reports.sales-pipeline') }}" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="bi bi-x"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold">
            Sales Pipeline
            <span class="badge bg-secondary ms-1 fw-normal">{{ $opportunities->total() }} records</span>
        </span>
        <a href="{{ route('reports.export', 'pipeline') }}?{{ http_build_query(request()->all()) }}" class="btn btn-sm btn-outline-success">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>

    {{-- Summary stats --}}
    <div class="card-body border-bottom py-2">
        <div class="row text-center g-2">
            <div class="col">
                <div class="small text-muted">Total Value</div>
                <div class="fw-bold">₹{{ number_format($stats['total_value']) }}</div>
            </div>
            <div class="col">
                <div class="small text-muted">Won Value</div>
                <div class="fw-bold text-success">₹{{ number_format($stats['won_value']) }}</div>
            </div>
            <div class="col">
                <div class="small text-muted">Avg Deal Value</div>
                <div class="fw-bold">₹{{ number_format($stats['avg_value']) }}</div>
            </div>
            <div class="col">
                <div class="small text-muted">Won Count</div>
                <div class="fw-bold text-success">{{ $stats['won_count'] }}</div>
            </div>
            <div class="col">
                <div class="small text-muted">Open Count</div>
                <div class="fw-bold text-primary">{{ $stats['open_count'] }}</div>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Opportunity</th>
                    <th>Customer</th>
                    <th class="text-end">Value (₹)</th>
                    <th>Stage</th>
                    <th>Assigned To</th>
                    <th>Expected Close</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($opportunities as $opp)
                @php
                    $stageBadge = match($opp->stage) {
                        'won'         => 'bg-success',
                        'lost'        => 'bg-danger',
                        'negotiation' => 'bg-warning text-dark',
                        'proposal'    => 'bg-info text-dark',
                        default       => 'bg-secondary',
                    };
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('opportunities.show', $opp) }}" class="fw-semibold text-decoration-none">
                            {{ $opp->opportunity_number }}
                        </a>
                    </td>
                    <td>{{ $opp->customer_name }}</td>
                    <td class="text-end fw-semibold">{{ $opp->expected_revenue ? '₹' . number_format($opp->expected_revenue) : '–' }}</td>
                    <td><span class="badge {{ $stageBadge }}">{{ \App\Models\Opportunity::stages()[$opp->stage] ?? $opp->stage }}</span></td>
                    <td class="small text-muted">{{ $opp->assignedTo?->name ?? '–' }}</td>
                    <td class="small {{ $opp->expected_closing_date && $opp->expected_closing_date->isPast() && $opp->stage !== 'won' ? 'text-danger fw-semibold' : 'text-muted' }}">
                        {{ $opp->expected_closing_date?->format('d M Y') ?? '–' }}
                    </td>
                    <td>
                        <a href="{{ route('opportunities.show', $opp) }}" class="btn btn-sm btn-outline-secondary py-0">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No opportunities match your filters.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
    @if($opportunities->hasPages())
    <div class="card-footer">{{ $opportunities->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
