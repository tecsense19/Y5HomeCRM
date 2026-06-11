@extends('layouts.app')
@section('title', 'New Customer')
@section('page-title', 'Create Customer')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Customer Information</span>
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('customers.store') }}" method="POST">
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
                            <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="mobile_number">Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="mobile_number" id="mobile_number" class="form-control" value="{{ old('mobile_number') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="address">Address</label>
                        <textarea name="address" id="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="city">City</label>
                            <input type="text" name="city" id="city" class="form-control" value="{{ old('city') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="state">State</label>
                            <input type="text" name="state" id="state" class="form-control" value="{{ old('state') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="notes">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
