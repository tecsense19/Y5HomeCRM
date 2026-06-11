{{-- users/create.blade.php --}}
@extends('layouts.app')
@section('title','Create User')
@section('page-title','Create User')
@section('content')
<div class="row justify-content-center"><div class="col-lg-7">
<form method="POST" action="{{ route('users.store') }}">
@csrf
<div class="card mb-3">
    <div class="card-header">User Details</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Full Name *</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6"><label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6"><label class="form-label">Mobile</label>
                <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}">
            </div>
            <div class="col-md-6"><label class="form-label">Role *</label>
                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                    <option value="">Select Role</option>
                    <option value="super-admin">Super Admin</option>
                    <option value="sales-manager">Sales Manager</option>
                    <option value="sales-executive">Sales Executive</option>
                    <option value="experience-center">Experience Center</option>
                </select>
            </div>
            <div class="col-md-6"><label class="form-label">Experience Center</label>
                <select name="experience_center_id" class="form-select">
                    <option value="">— None —</option>
                    @foreach($centers as $c)
                    <option value="{{ $c->id }}">{{ $c->center_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <div class="form-check">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" checked>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
            <div class="col-md-6"><label class="form-label">Password *</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6"><label class="form-label">Confirm Password *</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
        </div>
    </div>
</div>
<div class="d-flex gap-2">
    <button class="btn btn-primary px-4">Create User</button>
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
</form>
</div></div>
@endsection
