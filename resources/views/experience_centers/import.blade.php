@extends('layouts.app')
@section('title', 'Import Experience Centers')
@section('page-title', 'Import Experience Centers')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Upload Experience Centers CSV</span>
                <a href="{{ route('experience-centers.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back to Centers
                </a>
            </div>

            <div class="card-body">
                
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
                        <li>Required columns: <code>center_name</code>, <code>owner_name</code>, <code>mobile_number</code>, <code>city</code>, <code>state</code></li>
                        <li>Optional columns: <code>company_name</code>, <code>gst_number</code>, <code>pan_number</code>, <code>msme_udyam_number</code>, <code>email</code>, <code>address</code>, <code>country</code>, <code>agreement_start_date</code>, <code>agreement_end_date</code>, <code>security_deposit_amount</code></li>
                        <li>Please make sure the column headers match exactly (case-insensitive). Spaces will be converted to underscores.</li>
                    </ul>
                </div>

                <form action="{{ route('experience-centers.import.post') }}" method="POST" enctype="multipart/form-data">
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
                            <a href="{{ route('experience-centers.import.template') }}" class="btn btn-outline-success">
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
