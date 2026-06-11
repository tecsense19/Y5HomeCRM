@extends('layouts.app')
@section('title', 'Edit Opportunity')
@section('page-title', 'Edit Opportunity')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <form method="POST" action="{{ route('opportunities.update', $opportunity) }}">
            @csrf
            @method('PUT')

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Edit Opportunity: {{ $opportunity->opportunity_number }}</span>
                    <a href="{{ route('opportunities.show', $opportunity) }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="customer_name">Customer Name *</label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control" value="{{ old('customer_name', $opportunity->customer_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="project_name">Project Name / Location</label>
                            <input type="text" name="project_name" id="project_name" class="form-control" value="{{ old('project_name', $opportunity->project_name) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="expected_revenue">Expected Revenue (₹)</label>
                            <input type="number" step="0.01" name="expected_revenue" id="expected_revenue" class="form-control" value="{{ old('expected_revenue', $opportunity->expected_revenue) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="expected_closing_date">Expected Closing Date</label>
                            <input type="date" name="expected_closing_date" id="expected_closing_date" class="form-control" value="{{ old('expected_closing_date', $opportunity->expected_closing_date ? $opportunity->expected_closing_date->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="probability">Probability (%)</label>
                            <input type="number" name="probability" id="probability" class="form-control" min="0" max="100" value="{{ old('probability', $opportunity->probability) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="stage">Sales Stage *</label>
                            <select name="stage" id="stage" class="form-select" required>
                                @foreach(\App\Models\Opportunity::stages() as $key => $label)
                                    <option value="{{ $key }}" {{ old('stage', $opportunity->stage) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="assigned_to">Assigned Representative</label>
                            <select name="assigned_to" id="assigned_to" class="form-select">
                                <option value="">— Unassigned —</option>
                                @foreach($executives as $exec)
                                    <option value="{{ $exec->id }}" {{ old('assigned_to', $opportunity->assigned_to) == $exec->id ? 'selected' : '' }}>{{ $exec->name }} ({{ str_replace('-', ' ', $exec->role) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="notes">Notes / Meeting Details</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $opportunity->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">Update Opportunity</button>
                <a href="{{ route('opportunities.show', $opportunity) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
