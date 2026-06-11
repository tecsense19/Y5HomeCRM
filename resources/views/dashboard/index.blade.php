@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_leads'] }}</div>
            <div class="stat-label">Total Leads</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="border-left-color:#1a3c6e">
            <div class="stat-value text-primary">{{ $stats['new_leads'] }}</div>
            <div class="stat-label">New Leads</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="border-left-color:#28a745">
            <div class="stat-value" style="color:#28a745">{{ $stats['qualified_leads'] }}</div>
            <div class="stat-label">Qualified</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="border-left-color:#6f42c1">
            <div class="stat-value" style="color:#6f42c1">{{ $stats['open_opps'] }}</div>
            <div class="stat-label">Open Opportunities</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="border-left-color:#198754">
            <div class="stat-value" style="color:#198754">{{ $stats['won_leads'] }}</div>
            <div class="stat-label">Won Leads</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="border-left-color:#dc3545">
            <div class="stat-value" style="color:#dc3545">{{ $stats['lost_leads'] }}</div>
            <div class="stat-label">Lost Leads</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="border-left-color:#0dcaf0">
            <div class="stat-value" style="color:#0dcaf0">{{ $stats['site_visits'] }}</div>
            <div class="stat-label">Site Visits</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="border-left-color:#ffc107">
            @if($pendingFollowups > 0)
            <div class="stat-value text-warning">{{ $pendingFollowups }}</div>
            @else
            <div class="stat-value">0</div>
            @endif
            <div class="stat-label">Pending Follow-ups</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Revenue stats -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">Revenue Overview</div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Quotation Value</span>
                    <strong>₹{{ number_format($stats['quotation_value'], 0) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Won Revenue</span>
                    <strong class="text-success">₹{{ number_format($stats['won_value'], 0) }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Lost Revenue</span>
                    <strong class="text-danger">₹{{ number_format($stats['lost_value'], 0) }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Top lead sources -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">Top Lead Sources</div>
            <div class="card-body">
                @forelse($topSources as $src)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small">{{ \App\Models\Lead::sources()[$src->lead_source] ?? $src->lead_source }}</span>
                    <span class="badge bg-primary">{{ $src->count }}</span>
                </div>
                @empty
                <p class="text-muted small text-center">No data</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Top experience centers -->
    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSalesManager())
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">Top Experience Centers</div>
            <div class="card-body">
                @forelse($topCenters as $center)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small">{{ $center->center_name }}</span>
                    <span class="badge bg-secondary">{{ $center->leads_count }} leads</span>
                </div>
                @empty
                <p class="text-muted small text-center">No centers</p>
                @endforelse
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Recent Leads -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        Recent Leads
        <a href="{{ route('leads.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus"></i> New Lead
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Lead #</th>
                        <th>Customer</th>
                        <th>Mobile</th>
                        <th>Source</th>
                        <th>Status</th>
                        <th>Follow-up</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLeads as $lead)
                    <tr>
                        <td><span class="fw-semibold text-primary">{{ $lead->lead_number }}</span></td>
                        <td>{{ $lead->customer_name }}</td>
                        <td>{{ $lead->mobile_number }}</td>
                        <td>{{ \App\Models\Lead::sources()[$lead->lead_source] ?? '–' }}</td>
                        <td>
                            <span class="badge badge-{{ $lead->status }}">
                                {{ \App\Models\Lead::statuses()[$lead->status] ?? $lead->status }}
                            </span>
                        </td>
                        <td>
                            @if($lead->next_followup_date)
                                @if($lead->next_followup_date->isPast())
                                    <span class="text-danger small"><i class="bi bi-exclamation-circle"></i> {{ $lead->next_followup_date->format('d M') }}</span>
                                @else
                                    <span class="text-muted small">{{ $lead->next_followup_date->format('d M') }}</span>
                                @endif
                            @else
                                <span class="text-muted small">–</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('leads.show', $lead) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No leads yet. <a href="{{ route('leads.create') }}">Create first lead</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
