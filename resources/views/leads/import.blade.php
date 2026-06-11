{{-- leads/import.blade.php --}}
@extends('layouts.app')
@section('title', 'Import Leads')
@section('page-title', 'Import Leads')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Upload Leads CSV</span>
                <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back to Leads
                </a>
            </div>

            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('import_errors'))
                    <div class="alert alert-warning">
                        <strong>Some rows failed to import:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach(session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="alert alert-info small">
                    <strong>Instructions:</strong>
                    <ul class="mb-0">
                        <li>Only CSV files are supported.</li>
                        <li>Required columns: <code>customer_name</code>, <code>mobile_number</code></li>
                        <li>Optional columns: <code>email</code>, <code>lead_source</code>, <code>project_type</code>, <code>construction_stage</code>, <code>estimated_budget</code>, <code>city</code>, <code>state</code>, <code>country</code>, <code>project_address</code>, <code>remarks</code>, <code>assigned_to</code> (User ID), <code>experience_center_id</code> (Experience Center ID), <code>next_followup_date</code></li>
                        <li>Please make sure the column headers match exactly (case-insensitive). Spaces will be converted to underscores.</li>
                    </ul>
                </div>

                <form action="{{ route('leads.import.post') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="file" class="form-label">Select CSV File <span class="text-danger">*</span></label>
                        <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror" accept=".csv" required>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('leads.import.template') }}" class="btn btn-outline-success">
                                <i class="bi bi-download"></i> Download CSV Template
                            </a>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload"></i> Import Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
