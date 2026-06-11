{{-- site_visits/create.blade.php --}}
@extends('layouts.app')
@section('title','Record Site Visit')
@section('page-title','Record Site Visit')
@section('content')
<div class="row justify-content-center"><div class="col-lg-9">
<form method="POST" action="{{ route('site-visits.store') }}" enctype="multipart/form-data">
@csrf
<div class="card mb-3">
    <div class="card-header">Visit Details</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Link to Lead</label>
                <select name="lead_id" class="form-select">
                    <option value="">— Optional —</option>
                    @foreach($leads as $l)
                    <option value="{{ $l->id }}" {{ (request('lead_id') == $l->id || (isset($selectedLead) && $selectedLead?->id == $l->id)) ? 'selected' : '' }}>
                        {{ $l->lead_number }} – {{ $l->customer_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Customer Name *</label>
                <input type="text" name="customer_name" class="form-control" value="{{ $selectedLead?->customer_name ?? old('customer_name') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Visit Date *</label>
                <input type="date" name="visit_date" class="form-control" value="{{ old('visit_date', today()->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Visited By</label>
                <select name="visited_by" class="form-select">
                    <option value="">Select</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ auth()->id() == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Location</label>
                <input type="text" name="location" class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label">Requirement Summary</label>
                <textarea name="requirement_summary" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Products Required</label>
                <div class="row g-2">
                    @foreach(\App\Models\SiteVisit::products() as $k => $v)
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" name="products_required[]" value="{{ $k }}" class="form-check-input" id="prod_{{ $k }}">
                            <label class="form-check-label small" for="prod_{{ $k }}">{{ $v }}</label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Estimated Project Value (₹)</label>
                <input type="number" name="estimated_project_value" class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label">Attachments (Photos, Videos, Floor Plans)</label>
                <input type="file" name="attachments[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf,.mp4,.mov">
                <small class="text-muted">Max 25MB each. JPG, PNG, PDF, MP4</small>
            </div>
        </div>
    </div>
</div>
<div class="d-flex gap-2">
    <button class="btn btn-primary px-4">Record Visit</button>
    <a href="{{ route('site-visits.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
</form>
</div></div>
@endsection
