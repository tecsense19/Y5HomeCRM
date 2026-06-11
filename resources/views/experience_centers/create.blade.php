{{-- experience_centers/create.blade.php --}}
@extends('layouts.app')
@section('title','Add Experience Center')
@section('page-title','Add Experience Center')
@section('content')
<div class="row justify-content-center"><div class="col-lg-9">
<form method="POST" action="{{ route('experience-centers.store') }}">
@csrf
<div class="card mb-3">
    <div class="card-header">Center Details</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Center Name *</label><input type="text" name="center_name" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Owner Name *</label><input type="text" name="owner_name" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Company Name</label><input type="text" name="company_name" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">GST Number</label><input type="text" name="gst_number" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Mobile *</label><input type="text" name="mobile_number" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
            <div class="col-12"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2"></textarea></div>
            <div class="col-md-4"><label class="form-label">City *</label><input type="text" name="city" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">State *</label><input type="text" name="state" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Country</label><input type="text" name="country" class="form-control" value="India"></div>
            <div class="col-md-4"><label class="form-label">Agreement Start</label><input type="date" name="agreement_start_date" class="form-control"></div>
            <div class="col-md-4"><label class="form-label">Agreement End</label><input type="date" name="agreement_end_date" class="form-control"></div>
            <div class="col-md-4"><label class="form-label">Security Deposit (₹)</label><input type="number" name="security_deposit_amount" class="form-control"></div>
        </div>
    </div>
</div>
<div class="d-flex gap-2">
    <button class="btn btn-primary px-4">Create Center</button>
    <a href="{{ route('experience-centers.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
</form>
</div></div>
@endsection
