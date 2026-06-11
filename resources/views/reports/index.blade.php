@extends('layouts.app')
@section('title', 'Reports')
@section('page-title', 'Reports & Analytics')

@section('content')
<div class="row g-3">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center py-4">
                <i class="bi bi-bar-chart-fill fs-1 text-primary mb-3 d-block"></i>
                <h6 class="fw-bold">Lead Source Performance</h6>
                <p class="text-muted small">Leads by source, won rate, conversion %</p>
                <a href="{{ route('reports.lead-source') }}" class="btn btn-primary btn-sm px-4">View Report</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center py-4">
                <i class="bi bi-building fs-1 text-success mb-3 d-block"></i>
                <h6 class="fw-bold">Experience Center Performance</h6>
                <p class="text-muted small">Leads, visits, quotations, won per center</p>
                <a href="{{ route('reports.experience-center') }}" class="btn btn-success btn-sm px-4">View Report</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center py-4">
                <i class="bi bi-funnel-fill fs-1 text-info mb-3 d-block"></i>
                <h6 class="fw-bold">Sales Pipeline</h6>
                <p class="text-muted small">Opportunities by stage, value, closing</p>
                <a href="{{ route('reports.sales-pipeline') }}" class="btn btn-info btn-sm px-4 text-white">View Report</a>
            </div>
        </div>
    </div>
</div>
@endsection
