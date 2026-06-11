@extends('layouts.app')
@section('title', 'Create Lead')
@section('page-title', 'Create New Lead')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-10">
<form method="POST" action="{{ route('leads.store') }}">
    @csrf
    <div class="card mb-3">
        <div class="card-header">Customer Information</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                    <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror"
                           value="{{ old('customer_name') }}" required>
                    @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                    <input type="text" name="mobile_number" class="form-control @error('mobile_number') is-invalid @enderror"
                           value="{{ old('mobile_number') }}" required>
                    @error('mobile_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Project Address</label>
                    <textarea name="project_address" class="form-control" rows="2">{{ old('project_address') }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">State</label>
                    <input type="text" name="state" class="form-control" value="{{ old('state') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Country</label>
                    <input type="text" name="country" class="form-control" value="{{ old('country', 'India') }}">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Lead Details</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Lead Source</label>
                    <select name="lead_source" class="form-select">
                        <option value="">Select Source</option>
                        @foreach(\App\Models\Lead::sources() as $key => $label)
                        <option value="{{ $key }}" {{ old('lead_source') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Project Type</label>
                    <select name="project_type" class="form-select">
                        <option value="">Select Type</option>
                        @foreach(\App\Models\Lead::projectTypes() as $key => $label)
                        <option value="{{ $key }}" {{ old('project_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Construction Stage</label>
                    <select name="construction_stage" class="form-select">
                        <option value="">Select Stage</option>
                        @foreach(\App\Models\Lead::constructionStages() as $key => $label)
                        <option value="{{ $key }}" {{ old('construction_stage') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estimated Budget (₹)</label>
                    <input type="number" name="estimated_budget" class="form-control" value="{{ old('estimated_budget') }}" step="1000">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Next Follow-up Date</label>
                    <input type="date" name="next_followup_date" class="form-control" value="{{ old('next_followup_date') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks" class="form-control" rows="2">{{ old('remarks') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSalesManager())
    <div class="card mb-3">
        <div class="card-header">Assignment</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Assign to Experience Center</label>
                    <select name="experience_center_id" class="form-select">
                        <option value="">— Select Center —</option>
                        @foreach($centers as $c)
                        <option value="{{ $c->id }}" {{ old('experience_center_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->center_name }} ({{ $c->center_code }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Assign to Sales Executive</label>
                    <select name="assigned_to" class="form-select">
                        <option value="">— Select Executive —</option>
                        @foreach($executives as $u)
                        <option value="{{ $u->id }}" {{ old('assigned_to') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-lg"></i> Create Lead
        </button>
        <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
</div>
</div>
@endsection
