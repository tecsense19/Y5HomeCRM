@extends('layouts.app')
@section('title', 'Opportunity Details')
@section('page-title', 'Opportunity Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Opportunity #{{ $opportunity->opportunity_number }}</span>
                <div>
                    <a href="{{ route('opportunities.index') }}" class="btn btn-outline-secondary btn-sm me-1">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                    <a href="{{ route('opportunities.edit', $opportunity) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil"></i> Edit Opportunity
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Sales Stage quick-updater -->
                <div class="mb-4 p-3 bg-light rounded d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <span class="text-muted small d-block">Current Stage</span>
                        @php
                            $stageBadge = [
                                'requirement_gathering' => 'secondary',
                                'proposal'              => 'primary',
                                'negotiation'           => 'warning text-dark',
                                'won'                   => 'success',
                                'lost'                  => 'danger',
                            ][$opportunity->stage] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $stageBadge }} text-uppercase fs-6">
                            {{ \App\Models\Opportunity::stages()[$opportunity->stage] ?? $opportunity->stage }}
                        </span>
                    </div>
                    <form action="{{ route('opportunities.stage', $opportunity) }}" method="POST" class="d-flex align-items-center gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="stage" class="form-select form-select-sm" style="width: 220px;">
                            @foreach(\App\Models\Opportunity::stages() as $key => $label)
                                <option value="{{ $key }}" {{ $opportunity->stage === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-primary">Update Stage</button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th class="w-30 bg-light">Opportunity Number</th>
                                <td><strong class="text-primary">{{ $opportunity->opportunity_number }}</strong></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Customer Name</th>
                                <td>
                                    @if($opportunity->customer_id)
                                        <a href="{{ route('customers.show', $opportunity->customer_id) }}" class="fw-semibold text-primary text-decoration-none">
                                            {{ $opportunity->customer_name }}
                                        </a>
                                    @else
                                        {{ $opportunity->customer_name }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Project / Location</th>
                                <td>{{ $opportunity->project_name ?: '–' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Expected Revenue</th>
                                <td>
                                    @if($opportunity->expected_revenue)
                                        <strong class="text-dark">₹{{ number_format($opportunity->expected_revenue, 2) }}</strong>
                                    @else –
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Expected Closing Date</th>
                                <td>{{ $opportunity->expected_closing_date ? $opportunity->expected_closing_date->format('d M Y') : '–' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Probability</th>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $opportunity->probability }}%;" aria-valuenow="{{ $opportunity->probability }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="small fw-semibold">{{ $opportunity->probability }}%</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Associated Lead</th>
                                <td>
                                    @if($opportunity->lead)
                                        <a href="{{ route('leads.show', $opportunity->lead) }}" class="fw-semibold text-primary text-decoration-none">
                                            {{ $opportunity->lead->lead_number }}
                                        </a>
                                    @else
                                        <span class="text-muted">No associated lead (manually created)</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Assigned To</th>
                                <td>{{ $opportunity->assignedTo?->name ?? '–' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Notes</th>
                                <td>{!! nl2br(e($opportunity->notes)) ?: '<span class="text-muted">No notes provided.</span>' !!}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Quotations Section -->
                <div class="mt-4 border-top pt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-file-earmark-text me-1"></i> Quotations</h6>
                        <a href="{{ route('quotations.create', ['opportunity_id' => $opportunity->id]) }}" class="btn btn-xs btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Create Quotation
                        </a>
                    </div>
                    @if($opportunity->quotations->count() > 0)
                        <div class="table-responsive border rounded">
                            <table class="table mb-0 align-middle small">
                                <thead>
                                    <tr class="bg-light">
                                        <th>Quotation #</th>
                                        <th>Date</th>
                                        <th>Value</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($opportunity->quotations as $q)
                                    <tr>
                                        <td><strong>{{ $q->quotation_number }}</strong> <span class="text-muted text-xs">v{{ $q->version_number }}</span></td>
                                        <td>{{ $q->quotation_date->format('d M Y') }}</td>
                                        <td>₹{{ number_format($q->quotation_value) }}</td>
                                        <td>
                                            @php $cls = ['draft'=>'secondary','sent'=>'primary','approved'=>'success','rejected'=>'danger'][$q->status] ?? 'secondary' @endphp
                                            <span class="badge bg-{{ $cls }}">{{ ucfirst($q->status) }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1 justify-content-left">
                                                <a href="{{ route('quotations.show', $q) }}" class="btn btn-outline-primary btn-sm" title="View"><i class="bi bi-eye"></i></a>
                                                <form action="{{ route('quotations.destroy', $q) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this quotation?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted small mb-0">No quotations generated yet for this opportunity.</p>
                    @endif
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center border-top pt-3">
                    <span class="text-muted small">Created: {{ $opportunity->created_at->format('d M Y') }}</span>
                    <form action="{{ route('opportunities.destroy', $opportunity) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this opportunity permanently?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash"></i> Delete Opportunity
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
