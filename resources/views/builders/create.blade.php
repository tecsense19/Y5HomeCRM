@extends('layouts.app')
@section('title', 'New Builder')
@section('page-title', 'Create Builder')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <span>Create New Builder</span>
            </div>
            <div class="card-body">
                <form action="{{ route('builders.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name *</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="company_name" class="form-label">Company Name</label>
                        <input type="text" name="company_name" id="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}">
                        @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="mobile_number" class="form-label">Mobile Number *</label>
                            <input type="text" name="mobile_number" id="mobile_number" class="form-control @error('mobile_number') is-invalid @enderror" value="{{ old('mobile_number') }}" required>
                            @error('mobile_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}">
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" checked>
                        <label class="form-check-label" for="is_active">Active Partner</label>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="{{ route('builders.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Builder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
