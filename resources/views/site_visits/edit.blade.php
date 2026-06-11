@extends('layouts.app')
@section('title', 'Edit Site Visit')
@section('page-title', 'Edit Site Visit')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Edit Visit Details: {{ $siteVisit->visit_id }}</span>
                <div>
                    <a href="{{ route('site-visits.show', $siteVisit) }}" class="btn btn-outline-secondary btn-sm me-1">
                        Cancel
                    </a>
                    <a href="{{ route('site-visits.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('site-visits.update', $siteVisit) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Link to Lead</label>
                            <select name="lead_id" class="form-select" disabled>
                                <option value="">— Optional —</option>
                                @foreach($leads as $l)
                                    <option value="{{ $l->id }}" {{ $siteVisit->lead_id == $l->id ? 'selected' : '' }}>
                                        {{ $l->lead_number }} – {{ $l->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Associated lead cannot be changed.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $siteVisit->customer_name) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Visit Date <span class="text-danger">*</span></label>
                            <input type="date" name="visit_date" class="form-control" value="{{ old('visit_date', $siteVisit->visit_date ? $siteVisit->visit_date->format('Y-m-d') : '') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Visited By</label>
                            <select name="visited_by" class="form-select">
                                <option value="">Select</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ old('visited_by', $siteVisit->visited_by) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" value="{{ old('location', $siteVisit->location) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Requirement Summary</label>
                        <textarea name="requirement_summary" class="form-control" rows="3">{{ old('requirement_summary', $siteVisit->requirement_summary) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Products Required</label>
                        <div class="row g-2">
                            @foreach(\App\Models\SiteVisit::products() as $k => $v)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="products_required[]" value="{{ $k }}" class="form-check-input" id="prod_{{ $k }}"
                                            {{ is_array($siteVisit->products_required) && in_array($k, $siteVisit->products_required) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="prod_{{ $k }}">{{ $v }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Estimated Project Value (₹)</label>
                        <input type="number" name="estimated_project_value" class="form-control" value="{{ old('estimated_project_value', $siteVisit->estimated_project_value) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $siteVisit->notes) }}</textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Visit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
