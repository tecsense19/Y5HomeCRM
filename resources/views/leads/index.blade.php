{{-- leads/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Leads')
@section('page-title', 'Lead Management')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>All Leads</span>
        <div>
            <a href="{{ route('leads.import') }}" class="btn btn-secondary btn-sm me-2">
                <i class="bi bi-upload"></i> Import Leads
            </a>
            <a href="{{ route('leads.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> New Lead
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card-body border-bottom">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Search name, mobile, lead #" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    @foreach(\App\Models\Lead::statuses() as $key => $label)
                    <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="source" class="form-select form-select-sm">
                    <option value="">All Sources</option>
                    @foreach(\App\Models\Lead::sources() as $key => $label)
                    <option value="{{ $key }}" {{ request('source') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
            </div>
        </form>
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
                        <th>Type</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Follow-up</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr>
                        <td><a href="{{ route('leads.show', $lead) }}" class="fw-semibold text-primary text-decoration-none">{{ $lead->lead_number }}</a></td>
                        <td>{{ $lead->customer_name }}</td>
                        <td>{{ $lead->mobile_number }}</td>
                        <td><span class="text-muted small">{{ \App\Models\Lead::sources()[$lead->lead_source] ?? '–' }}</span></td>
                        <td><span class="text-muted small">{{ \App\Models\Lead::projectTypes()[$lead->project_type] ?? '–' }}</span></td>
                        <td>
                            <span class="badge badge-{{ $lead->status }}">
                                {{ \App\Models\Lead::statuses()[$lead->status] ?? $lead->status }}
                            </span>
                        </td>
                        <td class="small">{{ $lead->assignedTo?->name ?? '–' }}</td>
                        <td class="small">
                            @if($lead->next_followup_date)
                                <span class="{{ $lead->next_followup_date->isPast() ? 'text-danger' : 'text-muted' }}">
                                    {{ $lead->next_followup_date->format('d M Y') }}
                                </span>
                            @else –
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1 justify-content-left">
                                <a href="{{ route('leads.show', $lead) }}" class="btn btn-xs btn-outline-primary btn-sm" title="View"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('leads.edit', $lead) }}" class="btn btn-xs btn-outline-secondary btn-sm" title="Edit"><i class="bi bi-pencil"></i></a>
                                
                                @if(!$lead->locked_until || $lead->locked_until->isPast())
                                <button type="button" class="btn btn-xs btn-outline-warning btn-sm" title="Lock Lead" onclick="openLockModal({{ $lead->id }})">
                                    <i class="bi bi-lock"></i>
                                </button>
                                @else
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSalesManager() || auth()->id() === $lead->locked_by)
                                    <form action="{{ route('leads.unlock', $lead) }}" method="POST" class="d-inline">
                                        @csrf @method('POST')
                                        <button class="btn btn-xs btn-outline-success btn-sm" title="Unlock Lead">
                                            <i class="bi bi-unlock"></i>
                                        </button>
                                    </form>
                                    @else
                                    <button class="btn btn-xs btn-secondary btn-sm" disabled title="Locked by {{ $lead->lockedBy?->name }}">
                                        <i class="bi bi-lock-fill"></i>
                                    </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">No leads found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($leads->hasPages())
    <div class="card-footer">{{ $leads->links() }}</div>
    @endif
</div>

<!-- Lock Modal -->
<div class="modal fade" id="lockModalIndex" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="" id="lockFormIndex" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Lock Lead</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Locking this lead will prevent others from creating a new lead with the same mobile number.</p>
                <div class="mb-3">
                    <label class="form-label">Lock Duration (Days) <span class="text-danger">*</span></label>
                    <input type="number" name="lock_days" class="form-control" min="1" max="365" value="30" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning">Lock Lead</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openLockModal(leadId) {
    document.getElementById('lockFormIndex').action = "{{ route('leads.index') }}/" + leadId + "/lock";
    new bootstrap.Modal(document.getElementById('lockModalIndex')).show();
}
</script>
@endpush
@endsection
