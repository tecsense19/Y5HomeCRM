@extends('layouts.app')
@section('title', $customer->name)
@section('page-title', 'Customer Details')

@section('content')
<div class="row g-4">
    <!-- Left Column: Customer Card -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body text-center py-4">
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; font-size: 2rem;">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </div>
                <h5 class="card-title mb-1 fw-bold">{{ $customer->name }}</h5>
                <p class="text-muted small mb-3">Customer Profile</p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil"></i> Edit Profile
                    </a>
                </div>
            </div>
            <div class="card-body border-top p-0">
                <ul class="list-group list-group-flush rounded-bottom">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted small">Mobile</span>
                        <span class="fw-semibold small"><a href="tel:{{ $customer->mobile_number }}" class="text-decoration-none">{{ $customer->mobile_number }}</a></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted small">Email</span>
                        <span class="fw-semibold small">
                            @if($customer->email)
                                <a href="mailto:{{ $customer->email }}" class="text-decoration-none">{{ $customer->email }}</a>
                            @else –
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted small">City / State</span>
                        <span class="fw-semibold small">
                            @if($customer->city || $customer->state)
                                {{ $customer->city }}{{ $customer->city && $customer->state ? ', ' : '' }}{{$customer->state}}
                            @else –
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted small">Added On</span>
                        <span class="fw-semibold small">{{ $customer->created_at->format('d M Y') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        @if($customer->notes)
        <div class="card">
            <div class="card-header bg-light fw-bold text-muted small uppercase">Internal Notes</div>
            <div class="card-body">
                <p class="small text-dark mb-0" style="white-space: pre-wrap;">{{ $customer->notes }}</p>
            </div>
        </div>
        @endif
    </div>

    <!-- Right Column: Associated Data tabs -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white border-bottom">
                <ul class="nav nav-tabs card-header-tabs" id="customerDetailTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="leads-tab" data-bs-toggle="tab" data-bs-target="#leads" type="button" role="tab" aria-controls="leads" aria-selected="true">
                            <i class="bi bi-person-lines-fill"></i> Leads ({{ $customer->leads->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="visits-tab" data-bs-toggle="tab" data-bs-target="#visits" type="button" role="tab" aria-controls="visits" aria-selected="false">
                            <i class="bi bi-geo-alt"></i> Site Visits ({{ $customer->siteVisits->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab" aria-controls="documents" aria-selected="false">
                            <i class="bi bi-folder2-open"></i> Documents ({{ $customer->all_documents->count() }})
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="customerDetailTabsContent">
                <!-- Leads Tab -->
                <div class="tab-pane fade show active p-0" id="leads" role="tabpanel" aria-labelledby="leads-tab">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead>
                                <tr class="bg-light">
                                    <th>Lead #</th>
                                    <th>Source</th>
                                    <th>Project Type</th>
                                    <th>Budget</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customer->leads as $lead)
                                <tr>
                                    <td>
                                        <a href="{{ route('leads.show', $lead) }}" class="fw-semibold text-primary text-decoration-none">
                                            {{ $lead->lead_number }}
                                        </a>
                                    </td>
                                    <td class="small">{{ \App\Models\Lead::sources()[$lead->lead_source] ?? $lead->lead_source }}</td>
                                    <td class="small">{{ \App\Models\Lead::projectTypes()[$lead->project_type] ?? $lead->project_type }}</td>
                                    <td class="small">₹{{ number_format($lead->estimated_budget) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $lead->status }}">
                                            {{ \App\Models\Lead::statuses()[$lead->status] ?? $lead->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('leads.show', $lead) }}" class="btn btn-xs btn-outline-primary btn-sm">View</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No leads found for this customer.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Site Visits Tab -->
                <div class="tab-pane fade p-0" id="visits" role="tabpanel" aria-labelledby="visits-tab">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead>
                                <tr class="bg-light">
                                    <th>Visit Date & Time</th>
                                    <th>Center</th>
                                    <th>Visitor Name</th>
                                    <th>Conducted By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customer->siteVisits as $visit)
                                <tr>
                                    <td class="small fw-semibold">
                                        {{ $visit->visit_date ? $visit->visit_date->format('d M Y') : '–' }}
                                        {{ $visit->visit_time ? ' at ' . \Carbon\Carbon::createFromFormat('H:i:s', $visit->visit_time)->format('h:i A') : '' }}
                                    </td>
                                    <td class="small">{{ $visit->experienceCenter?->center_name ?? '–' }}</td>
                                    <td class="small">{{ $visit->visitor_name }}</td>
                                    <td class="small">{{ $visit->conductedBy?->name ?? '–' }}</td>
                                    <td>
                                        <a href="{{ route('site-visits.show', $visit) }}" class="btn btn-xs btn-outline-primary btn-sm">View</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No site visits recorded.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Documents Tab -->
                <div class="tab-pane fade p-0" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                    <div class="card-body border-bottom d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Customer documents store</span>
                        <a href="{{ route('documents.create', ['module' => 'customer', 'id' => $customer->id]) }}" class="btn btn-xs btn-primary btn-sm">
                            <i class="bi bi-upload"></i> Upload Document
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead>
                                <tr class="bg-light">
                                    <th>File Name</th>
                                    <th>Associated With</th>
                                    <th>Category</th>
                                    <th>Size</th>
                                    <th>Uploaded Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customer->all_documents as $doc)
                                <tr>
                                    <td>
                                        <a href="{{ route('documents.show', $doc) }}" class="fw-semibold text-primary text-decoration-none">
                                            <i class="bi bi-file-earmark-arrow-down me-1"></i>{{ $doc->original_name }}
                                        </a>
                                    </td>
                                    <td class="small">
                                        @if($doc->documentable_type === 'App\Models\Customer')
                                            <span class="badge bg-success-subtle text-success">Customer</span>
                                        @elseif($doc->documentable_type === 'App\Models\Lead')
                                            <span class="badge bg-primary-subtle text-primary">Lead ({{ $doc->documentable?->lead_number ?? '#'.$doc->documentable_id }})</span>
                                        @elseif($doc->documentable_type === 'App\Models\Opportunity')
                                            <span class="badge bg-warning-subtle text-warning">Opportunity ({{ $doc->documentable?->opportunity_number ?? '#'.$doc->documentable_id }})</span>
                                        @elseif($doc->documentable_type === 'App\Models\Quotation')
                                            <span class="badge bg-info-subtle text-info">Quotation ({{ $doc->documentable?->quotation_number ?? '#'.$doc->documentable_id }})</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">{{ class_basename($doc->documentable_type) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary text-capitalize">
                                            {{ str_replace('_', ' ', $doc->category) }}
                                        </span>
                                    </td>
                                    <td class="small text-muted">{{ number_format($doc->file_size / 1024, 2) }} KB</td>
                                    <td class="small text-muted">{{ $doc->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('documents.download', $doc) }}" class="btn btn-xs btn-outline-primary btn-sm"><i class="bi bi-download"></i></a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No documents uploaded.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
