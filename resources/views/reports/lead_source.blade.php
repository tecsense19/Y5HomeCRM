{{-- reports/lead_source.blade.php --}}
@extends('layouts.app')
@section('title', 'Lead Source Report')
@section('page-title', 'Lead Source Performance')

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light fw-semibold">
        <i class="bi bi-funnel me-1"></i> Filters
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Lead Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    @foreach(\App\Models\Lead::statuses() as $k => $v)
                        <option value="{{ $k }}" {{ request('status') === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Apply
                </button>
                <a href="{{ route('reports.lead-source') }}" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="bi bi-x"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold">
            Lead Source Performance
            @if(request('date_from') || request('date_to'))
                <span class="badge bg-info text-dark ms-2 fw-normal">
                    {{ request('date_from') ? \Carbon\Carbon::parse(request('date_from'))->format('d M Y') : '…' }}
                    –
                    {{ request('date_to') ? \Carbon\Carbon::parse(request('date_to'))->format('d M Y') : 'Today' }}
                </span>
            @endif
        </span>
        <a href="{{ route('reports.export', 'lead-source') }}?{{ http_build_query(request()->all()) }}" class="btn btn-sm btn-outline-success">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Lead Source</th>
                    <th class="text-end">Total Leads</th>
                    <th class="text-end">Won Leads</th>
                    <th class="text-end">Conversion %</th>
                    <th class="text-end">Open Leads</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $row)
                <tr>
                    <td class="fw-semibold">{{ \App\Models\Lead::sources()[$row->lead_source] ?? $row->lead_source }}</td>
                    <td class="text-end">{{ $row->lead_count }}</td>
                    <td class="text-end text-success fw-semibold">{{ $row->won_leads }}</td>
                    <td class="text-end">
                        <span class="badge {{ $row->conversion_pct >= 30 ? 'bg-success' : ($row->conversion_pct >= 10 ? 'bg-warning text-dark' : 'bg-secondary') }}">
                            {{ $row->conversion_pct }}%
                        </span>
                    </td>
                    <td class="text-end text-muted">{{ $row->lead_count - $row->won_leads }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No data for the selected filters.</td></tr>
                @endforelse
            </tbody>
            @if($data->count() > 0)
            <tfoot class="table-light fw-bold">
                <tr>
                    <td>Total</td>
                    <td class="text-end">{{ $data->sum('lead_count') }}</td>
                    <td class="text-end text-success">{{ $data->sum('won_leads') }}</td>
                    <td class="text-end">
                        @php $t = $data->sum('lead_count'); $w = $data->sum('won_leads'); @endphp
                        {{ $t > 0 ? round($w * 100 / $t, 1) : 0 }}%
                    </td>
                    <td class="text-end">{{ $data->sum('lead_count') - $data->sum('won_leads') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
        </div>
    </div>
</div>
@endsection
