{{-- leads/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Lead')
@section('page-title', 'Edit Lead – ' . $lead->lead_number)

@section('content')
<div class="row justify-content-center">
<div class="col-lg-10">
<form method="POST" action="{{ route('leads.update', $lead) }}">
    @csrf @method('PUT')
    <div class="card mb-3">
        <div class="card-header">Customer Information</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                    <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $lead->customer_name) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                    <input type="text" name="mobile_number" class="form-control" value="{{ old('mobile_number', $lead->mobile_number) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $lead->email) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city', $lead->city) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">State</label>
                    <input type="text" name="state" class="form-control" value="{{ old('state', $lead->state) }}">
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
                        <option value="">Select</option>
                        @foreach(\App\Models\Lead::sources() as $k => $v)
                        <option value="{{ $k }}" {{ old('lead_source', $lead->lead_source) === $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Project Type</label>
                    <select name="project_type" class="form-select">
                        <option value="">Select</option>
                        @foreach(\App\Models\Lead::projectTypes() as $k => $v)
                        <option value="{{ $k }}" {{ old('project_type', $lead->project_type) === $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Construction Stage</label>
                    <select name="construction_stage" class="form-select">
                        <option value="">Select</option>
                        @foreach(\App\Models\Lead::constructionStages() as $k => $v)
                        <option value="{{ $k }}" {{ old('construction_stage', $lead->construction_stage) === $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estimated Budget (₹)</label>
                    <input type="number" name="estimated_budget" class="form-control" value="{{ old('estimated_budget', $lead->estimated_budget) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Next Follow-up Date</label>
                    <input type="date" name="next_followup_date" class="form-control" value="{{ old('next_followup_date', $lead->next_followup_date?->format('Y-m-d')) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks" class="form-control" rows="2">{{ old('remarks', $lead->remarks) }}</textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4">Save Changes</button>
        <a href="{{ route('leads.show', $lead) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
</div>
</div>
@endsection
