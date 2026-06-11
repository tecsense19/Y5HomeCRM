@extends('layouts.app')
@section('title', 'Edit Architect')
@section('page-title', 'Edit Architect')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Edit Architect Information</span>
                <div>
                    <a href="{{ route('architects.show', $architect) }}" class="btn btn-outline-secondary btn-sm me-1">
                        Cancel
                    </a>
                    <a href="{{ route('architects.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('architects.update', $architect) }}" method="POST">
                    @csrf
                    @method('PUT')

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
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $architect->name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="mobile_number" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="mobile_number" id="mobile_number" class="form-control" value="{{ old('mobile_number', $architect->mobile_number) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="firm_name" class="form-label">Firm Name</label>
                        <input type="text" name="firm_name" id="firm_name" class="form-control" value="{{ old('firm_name', $architect->firm_name) }}">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $architect->email) }}">
                    </div>

                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" name="city" id="city" class="form-control" value="{{ old('city', $architect->city) }}">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $architect->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active Partner</label>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Architect
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
