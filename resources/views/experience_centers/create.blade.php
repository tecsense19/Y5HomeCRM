@extends('layouts.app')
@section('title', 'Add Experience Center')
@section('page-title', 'Add Experience Center')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Center Details</span>
                <a href="{{ route('experience-centers.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('experience-centers.store') }}" method="POST">
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
                            <label class="form-label" for="center_name">Center Name <span class="text-danger">*</span></label>
                            <input type="text" name="center_name" id="center_name" class="form-control" value="{{ old('center_name') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="owner_name">Owner Name <span class="text-danger">*</span></label>
                            <input type="text" name="owner_name" id="owner_name" class="form-control" value="{{ old('owner_name') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="company_name">Company Name</label>
                            <input type="text" name="company_name" id="company_name" class="form-control" value="{{ old('company_name') }}">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="gst_number">GST Number</label>
                            <input type="text" name="gst_number" id="gst_number" class="form-control" value="{{ old('gst_number') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="pan_number">PAN Number</label>
                            <input type="text" name="pan_number" id="pan_number" class="form-control" value="{{ old('pan_number') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="msme_udyam_number">MSME Udyam Number</label>
                            <input type="text" name="msme_udyam_number" id="msme_udyam_number" class="form-control" value="{{ old('msme_udyam_number') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="mobile_number">Mobile <span class="text-danger">*</span></label>
                            <input type="text" name="mobile_number" id="mobile_number" class="form-control" value="{{ old('mobile_number') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="address">Address</label>
                        <textarea name="address" id="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="city">City <span class="text-danger">*</span></label>
                            <input type="text" name="city" id="city" class="form-control" value="{{ old('city') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="state">State <span class="text-danger">*</span></label>
                            <input type="text" name="state" id="state" class="form-control" value="{{ old('state') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="country">Country</label>
                            <input type="text" name="country" id="country" class="form-control" value="{{ old('country', 'India') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="agreement_start_date">Agreement Start</label>
                            <input type="date" name="agreement_start_date" id="agreement_start_date" class="form-control" value="{{ old('agreement_start_date') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="agreement_end_date">Agreement End</label>
                            <input type="date" name="agreement_end_date" id="agreement_end_date" class="form-control" value="{{ old('agreement_end_date') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="security_deposit_amount">Security Deposit (₹)</label>
                            <input type="number" step="0.01" name="security_deposit_amount" id="security_deposit_amount" class="form-control" value="{{ old('security_deposit_amount') }}">
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Create Center
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
