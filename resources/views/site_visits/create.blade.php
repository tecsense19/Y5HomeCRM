@extends('layouts.app')
@section('title', 'Record Site Visit')
@section('page-title', 'Record Site Visit')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Visit Details</span>
                <a href="{{ route('site-visits.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('site-visits.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

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
                            <select name="lead_id" class="form-select">
                                <option value="">— Optional —</option>
                                @foreach($leads as $l)
                                <option value="{{ $l->id }}" {{ (request('lead_id') == $l->id || (isset($selectedLead) && $selectedLead?->id == $l->id)) ? 'selected' : '' }}>
                                    {{ $l->lead_number }} – {{ $l->customer_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control" value="{{ $selectedLead?->customer_name ?? old('customer_name') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Visit Date <span class="text-danger">*</span></label>
                            <input type="date" name="visit_date" class="form-control" value="{{ old('visit_date', today()->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Visited By</label>
                            <select name="visited_by" class="form-select">
                                <option value="">Select</option>
                                @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ auth()->id() == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Requirement Summary</label>
                        <textarea name="requirement_summary" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Products Required</label>
                        <div class="row g-2">
                            @foreach(\App\Models\SiteVisit::products() as $k => $v)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="products_required[]" value="{{ $k }}" class="form-check-input" id="prod_{{ $k }}">
                                    <label class="form-check-label small" for="prod_{{ $k }}">{{ $v }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estimated Project Value (₹)</label>
                            <input type="number" name="estimated_project_value" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Attachments</label>
                            <input type="file" name="attachments[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf,.mp4,.mov">
                            <small class="text-muted">Max 25MB each. JPG, PNG, PDF, MP4</small>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Record Visit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
