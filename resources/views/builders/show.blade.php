@extends('layouts.app')
@section('title', 'Builder Details')
@section('page-title', 'Builder Details')

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Builder Profile Card -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-3" style="width: 72px; height: 72px;">
                    <i class="bi bi-building fs-1"></i>
                </div>
                <h4 class="card-title mb-1">{{ $builder->name }}</h4>
                <p class="text-muted small mb-3">{{ $builder->company_name ?: 'Independent Builder' }}</p>
                <div>
                    @if($builder->is_active)
                        <span class="badge bg-success">Active Partner</span>
                    @else
                        <span class="badge bg-secondary">Inactive Partner</span>
                    @endif
                </div>
            </div>
            <hr class="my-0">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-telephone text-primary"></i>
                    <div>
                        <div class="text-muted small">Mobile Number</div>
                        <div>{{ $builder->mobile_number }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-envelope text-primary"></i>
                    <div>
                        <div class="text-muted small">Email Address</div>
                        <div>{{ $builder->email ?: '–' }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-geo-alt text-primary"></i>
                    <div>
                        <div class="text-muted small">City</div>
                        <div>{{ $builder->city ?: '–' }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-event text-primary"></i>
                    <div>
                        <div class="text-muted small">Created Date</div>
                        <div>{{ $builder->created_at->format('d M Y') }}</div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light d-flex justify-content-between">
                <a href="{{ route('builders.edit', $builder) }}" class="btn btn-sm btn-outline-secondary">Edit Details</a>
                <a href="{{ route('builders.index') }}" class="btn btn-sm btn-link text-decoration-none text-muted">Back to List</a>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Builder Activity / Leads Info -->
        <div class="card">
            <div class="card-header">
                <span>Builder Projects / Referred Leads</span>
            </div>
            <div class="card-body py-5 text-center text-muted">
                <i class="bi bi-info-circle fs-3 mb-2 d-block"></i>
                <p class="mb-0">There are no leads linked directly to this Builder yet.</p>
                <small>Future updates will support automatic referral link tracking.</small>
            </div>
        </div>
    </div>
</div>
@endsection
