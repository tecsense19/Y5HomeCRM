@extends('layouts.app')
@section('title', 'New Opportunity')
@section('page-title', 'Create Opportunity')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Opportunity Information</span>
                <a href="{{ route('opportunities.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('opportunities.store') }}" method="POST">
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
                            <label class="form-label" for="customer_name">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control" value="{{ old('customer_name') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="project_name">Project Name / Location</label>
                            <input type="text" name="project_name" id="project_name" class="form-control" value="{{ old('project_name') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="expected_revenue">Expected Revenue (₹)</label>
                            <input type="number" step="0.01" name="expected_revenue" id="expected_revenue" class="form-control" value="{{ old('expected_revenue') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="expected_closing_date">Expected Closing Date</label>
                            <input type="date" name="expected_closing_date" id="expected_closing_date" class="form-control" value="{{ old('expected_closing_date') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="probability">Probability (%)</label>
                            <input type="number" name="probability" id="probability" class="form-control" min="0" max="100" value="{{ old('probability', 20) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="stage">Sales Stage <span class="text-danger">*</span></label>
                            <select name="stage" id="stage" class="form-select" required>
                                @foreach(\App\Models\Opportunity::stages() as $key => $label)
                                    <option value="{{ $key }}" {{ old('stage') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="assigned_to">Assigned Representative</label>
                            <select name="assigned_to" id="assigned_to" class="form-select">
                                <option value="">— Unassigned —</option>
                                @foreach($executives as $exec)
                                    <option value="{{ $exec->id }}" {{ old('assigned_to') == $exec->id ? 'selected' : '' }}>{{ $exec->name }} ({{ str_replace('-', ' ', $exec->role) }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="notes">Notes / Meeting Details</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Opportunity
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
