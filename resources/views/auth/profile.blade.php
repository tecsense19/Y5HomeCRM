@extends('layouts.app')
@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong class="d-block mb-1">Please fix the following issues:</strong>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i class="bi bi-person-fill me-2 text-primary"></i>Profile Details
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label text-secondary fw-semibold mb-2" for="email">Email Address</label>
                            <input type="email" id="email" class="form-control bg-light" value="{{ $user->email }}" readonly disabled>
                            <div class="form-text">Your email address cannot be changed.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-secondary fw-semibold mb-2" for="role">User Role</label>
                            <input type="text" id="role" class="form-control bg-light text-uppercase fw-semibold" value="{{ $user->role }}" readonly disabled>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label text-dark fw-semibold mb-2" for="name">Full Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-person text-secondary"></i></span>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" placeholder="Enter full name" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold mb-2" for="mobile">Mobile Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-telephone text-secondary"></i></span>
                                <input type="text" name="mobile" id="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile', $user->mobile) }}" placeholder="Enter mobile number">
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 text-muted">

                    <h5 class="fw-bold mb-3 text-dark">
                        <i class="bi bi-shield-lock-fill me-2 text-primary"></i>Change Password
                    </h5>
                    <p class="text-muted small mb-4">Leave fields blank if you do not want to change your password.</p>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label text-dark fw-semibold mb-2" for="password">New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-key text-secondary"></i></span>
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimum 8 characters">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold mb-2" for="password_confirmation">Confirm New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-key-fill text-secondary"></i></span>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Re-type password">
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save2 me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
