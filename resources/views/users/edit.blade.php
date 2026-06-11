@extends('layouts.app')
@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Edit User Account: {{ $user->name }}</span>
                    @if(auth()->user()->isExperienceCenterUser())
                        <a href="{{ route('experience-centers.show', auth()->user()->experience_center_id) }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
                    @else
                        <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
                    @endif
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
                            <label class="form-label" for="name">Full Name *</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="email">Email *</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="mobile">Mobile</label>
                            <input type="text" name="mobile" id="mobile" class="form-control" value="{{ old('mobile', $user->mobile) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="role">Role *</label>
                            <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                                @if(auth()->user()->isSuperAdmin() || auth()->user()->isSalesManager())
                                <option value="super-admin" {{ old('role', $user->role) === 'super-admin' ? 'selected' : '' }}>Super Admin</option>
                                <option value="sales-manager" {{ old('role', $user->role) === 'sales-manager' ? 'selected' : '' }}>Sales Manager</option>
                                @endif
                                <option value="sales-executive" {{ old('role', $user->role) === 'sales-executive' ? 'selected' : '' }}>Sales Executive</option>
                                <option value="experience-center" {{ old('role', $user->role) === 'experience-center' ? 'selected' : '' }}>Experience Center</option>
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSalesManager())
                        <div class="col-md-6">
                            <label class="form-label" for="experience_center_id">Experience Center</label>
                            <select name="experience_center_id" id="experience_center_id" class="form-select">
                                <option value="">— None —</option>
                                @foreach($centers as $c)
                                    <option value="{{ $c->id }}" {{ old('experience_center_id', $user->experience_center_id) == $c->id ? 'selected' : '' }}>{{ $c->center_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <input type="hidden" name="experience_center_id" value="{{ auth()->user()->experience_center_id }}">
                        @endif
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active Account</label>
                            </div>
                        </div>

                        <div class="col-12 mt-3 pt-3 border-top">
                            <h6 class="fw-bold">Update Password (Leave blank to keep current)</h6>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="password">New Password</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="password_confirmation">Confirm New Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">Update User</button>
                @if(auth()->user()->isExperienceCenterUser())
                    <a href="{{ route('experience-centers.show', auth()->user()->experience_center_id) }}" class="btn btn-outline-secondary">Cancel</a>
                @else
                    <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary">Cancel</a>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection
