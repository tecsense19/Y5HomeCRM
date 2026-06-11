@extends('layouts.app')
@section('title', 'Lead - ' . $lead->lead_number)
@section('page-title', 'Lead Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h5 class="mb-0 fw-bold">{{ $lead->lead_number }} — {{ $lead->customer_name }}</h5>
        <small class="text-muted">Created {{ $lead->lead_creation_date?->format('d M Y') ?? '—' }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('leads.edit', $lead) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSalesManager())
        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#assignModal">
            <i class="bi bi-person-check"></i> Assign
        </button>
        @endif
    </div>
</div>

<!-- Pipeline Progress -->
@php
$stages = array_keys(\App\Models\Lead::statuses());
$currentIdx = array_search($lead->status, $stages);
@endphp
<div class="card mb-3">
    <div class="card-body py-3">
        <div class="d-flex align-items-center overflow-auto pb-1">
            @foreach(\App\Models\Lead::statuses() as $key => $label)
            @php $idx = array_search($key, $stages); @endphp
            @if(!$loop->first)<div class="pipeline-line" style="min-width:20px"></div>@endif
            <div class="pipeline-step text-center" style="min-width:80px">
                <div class="pipeline-dot mx-auto mb-1
                    {{ $idx < $currentIdx ? 'done' : ($idx === $currentIdx ? 'active' : '') }}">
                    {{ $idx < $currentIdx ? '✓' : ($idx + 1) }}
                </div>
                <div style="font-size:.65rem;color:#888">{{ $label }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Lead Info -->
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">Lead Information</div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-sm-6"><small class="text-muted d-block">Mobile</small><strong>{{ $lead->mobile_number }}</strong></div>
                    <div class="col-sm-6"><small class="text-muted d-block">Email</small>{{ $lead->email ?? '–' }}</div>
                    <div class="col-sm-6"><small class="text-muted d-block">Lead Source</small>{{ \App\Models\Lead::sources()[$lead->lead_source] ?? '–' }}</div>
                    <div class="col-sm-6"><small class="text-muted d-block">Project Type</small>{{ \App\Models\Lead::projectTypes()[$lead->project_type] ?? '–' }}</div>
                    <div class="col-sm-6"><small class="text-muted d-block">Construction Stage</small>{{ \App\Models\Lead::constructionStages()[$lead->construction_stage] ?? '–' }}</div>
                    <div class="col-sm-6"><small class="text-muted d-block">Estimated Budget</small>{{ $lead->estimated_budget ? '₹' . number_format($lead->estimated_budget) : '–' }}</div>
                    <div class="col-sm-6"><small class="text-muted d-block">City / State</small>{{ $lead->city }}, {{ $lead->state }}</div>
                    <div class="col-sm-6"><small class="text-muted d-block">Next Follow-up</small>
                        @if($lead->next_followup_date)
                            <span class="{{ $lead->next_followup_date->isPast() ? 'text-danger fw-bold' : '' }}">
                                {{ $lead->next_followup_date->format('d M Y') }}
                            </span>
                        @else –
                        @endif
                    </div>
                    @if($lead->remarks)
                    <div class="col-12"><small class="text-muted d-block">Remarks</small>{{ $lead->remarks }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Site Visits -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                Site Visits <a href="{{ route('site-visits.create', ['lead_id' => $lead->id]) }}" class="btn btn-sm btn-outline-primary">+ Add Visit</a>
            </div>
            <div class="card-body p-0">
                @forelse($lead->siteVisits as $visit)
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $visit->visit_id }}</strong>
                        <small class="text-muted">{{ $visit->visit_date->format('d M Y') }}</small>
                    </div>
                    <div class="small text-muted">{{ $visit->location }} • by {{ $visit->visitedBy?->name }}</div>
                    @if($visit->requirement_summary)
                    <div class="small mt-1">{{ Str::limit($visit->requirement_summary, 100) }}</div>
                    @endif
                </div>
                @empty
                <p class="text-muted text-center py-3 small">No site visits recorded.</p>
                @endforelse
            </div>
        </div>

        <!-- Opportunities -->
        @if($lead->opportunities->count())
        <div class="card">
            <div class="card-header">Linked Opportunities</div>
            <div class="card-body p-0">
                @foreach($lead->opportunities as $opp)
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('opportunities.show', $opp) }}" class="fw-semibold">{{ $opp->opportunity_number }}</a>
                        <span class="badge bg-info">{{ \App\Models\Opportunity::stages()[$opp->stage] ?? $opp->stage }}</span>
                    </div>
                    <div class="small text-muted">Expected: {{ $opp->expected_revenue ? '₹' . number_format($opp->expected_revenue) : '–' }} | Close: {{ $opp->expected_closing_date?->format('d M Y') ?? '–' }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Status Update -->
        <div class="card mb-3">
            <div class="card-header">Update Status</div>
            <div class="card-body">
                <form method="POST" action="{{ route('leads.status', $lead) }}">
                    @csrf @method('PATCH')
                    <select name="status" class="form-select mb-2">
                        @foreach(\App\Models\Lead::statuses() as $key => $label)
                        <option value="{{ $key }}" {{ $lead->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div id="lostReasonWrap" style="{{ $lead->status === 'lost' ? '' : 'display:none' }}">
                        <select name="lost_reason" class="form-select mb-2">
                            <option value="">Select Reason</option>
                            @foreach(\App\Models\Lead::lostReasons() as $key => $label)
                            <option value="{{ $key }}" {{ $lead->lost_reason === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary btn-sm w-100">Update Status</button>
                </form>
            </div>
        </div>

        <!-- Assignment Info -->
        <div class="card mb-3">
            <div class="card-header">Assignment</div>
            <div class="card-body">
                <small class="text-muted d-block">Sales Executive</small>
                <strong>{{ $lead->assignedTo?->name ?? 'Unassigned' }}</strong>
                <small class="text-muted d-block mt-2">Experience Center</small>
                <strong>{{ $lead->experienceCenter?->center_name ?? 'Unassigned' }}</strong>
            </div>
        </div>
    </div>
</div>

<!-- Assign Modal -->
@if(auth()->user()->isSuperAdmin() || auth()->user()->isSalesManager())
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('leads.assign', $lead) }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Assign Lead</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Experience Center</label>
                    <select name="experience_center_id" class="form-select">
                        <option value="">— Select —</option>
                        @foreach(\App\Models\ExperienceCenter::where('status','active')->get() as $c)
                        <option value="{{ $c->id }}" {{ $lead->experience_center_id == $c->id ? 'selected' : '' }}>
                            {{ $c->center_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sales Executive</label>
                    <select name="assigned_to" class="form-select">
                        <option value="">— Select —</option>
                        @foreach(\App\Models\User::where('role','sales-executive')->where('is_active',true)->get() as $u)
                        <option value="{{ $u->id }}" {{ $lead->assigned_to == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Assign</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endif

@push('scripts')
<script>
document.querySelector('[name="status"]').addEventListener('change', function() {
    document.getElementById('lostReasonWrap').style.display = this.value === 'lost' ? '' : 'none';
});
</script>
@endpush
@endsection
