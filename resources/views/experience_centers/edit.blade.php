@extends('layouts.app')
@section('title', 'Edit Experience Center')
@section('page-title', 'Edit Experience Center')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <form method="POST" action="{{ route('experience-centers.update', $experienceCenter) }}">
            @csrf
            @method('PUT')

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Edit Center: {{ $experienceCenter->center_name }}</span>
                    <a href="{{ route('experience-centers.show', $experienceCenter) }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
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
                            <label class="form-label" for="center_name">Center Name *</label>
                            <input type="text" name="center_name" id="center_name" class="form-control" value="{{ old('center_name', $experienceCenter->center_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="owner_name">Owner Name *</label>
                            <input type="text" name="owner_name" id="owner_name" class="form-control" value="{{ old('owner_name', $experienceCenter->owner_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="company_name">Company Name</label>
                            <input type="text" name="company_name" id="company_name" class="form-control" value="{{ old('company_name', $experienceCenter->company_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="gst_number">GST Number</label>
                            <input type="text" name="gst_number" id="gst_number" class="form-control" value="{{ old('gst_number', $experienceCenter->gst_number) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="mobile_number">Mobile *</label>
                            <input type="text" name="mobile_number" id="mobile_number" class="form-control" value="{{ old('mobile_number', $experienceCenter->mobile_number) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $experienceCenter->email) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="address">Address</label>
                            <textarea name="address" id="address" class="form-control" rows="2">{{ old('address', $experienceCenter->address) }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="city">City *</label>
                            <input type="text" name="city" id="city" class="form-control" value="{{ old('city', $experienceCenter->city) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="state">State *</label>
                            <input type="text" name="state" id="state" class="form-control" value="{{ old('state', $experienceCenter->state) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="country">Country</label>
                            <input type="text" name="country" id="country" class="form-control" value="{{ old('country', $experienceCenter->country) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="agreement_start_date">Agreement Start</label>
                            <input type="date" name="agreement_start_date" id="agreement_start_date" class="form-control" value="{{ old('agreement_start_date', $experienceCenter->agreement_start_date ? $experienceCenter->agreement_start_date->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="agreement_end_date">Agreement End</label>
                            <input type="date" name="agreement_end_date" id="agreement_end_date" class="form-control" value="{{ old('agreement_end_date', $experienceCenter->agreement_end_date ? $experienceCenter->agreement_end_date->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="security_deposit_amount">Security Deposit (₹)</label>
                            <input type="number" step="0.01" name="security_deposit_amount" id="security_deposit_amount" class="form-control" value="{{ old('security_deposit_amount', $experienceCenter->security_deposit_amount) }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary px-4">Update Center</button>
                <a href="{{ route('experience-centers.show', $experienceCenter) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
