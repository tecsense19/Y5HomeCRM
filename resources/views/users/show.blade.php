@extends('layouts.app')
@section('title', 'User Details')
@section('page-title', 'User Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>User Account: {{ $user->name }}</span>
                <div>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm me-1">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil"></i> Edit User
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="text-center py-3">
                    <div class="rounded-circle bg-dark text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; font-size: 2rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <span class="badge bg-secondary text-uppercase mb-2">{{ str_replace('-', ' ', $user->role) }}</span>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th class="w-40 bg-light">Email Address</th>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Mobile Number</th>
                                <td>{{ $user->mobile ?: '–' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Associated Center</th>
                                <td>
                                    @if($user->experienceCenter)
                                        <a href="{{ route('experience-centers.show', $user->experienceCenter) }}" class="fw-semibold text-primary text-decoration-none">
                                            {{ $user->experienceCenter->center_name }}
                                        </a>
                                    @else
                                        <span class="text-muted">Global / Corporate</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Account Status</th>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Last Login</th>
                                <td>{{ $user->last_login_at ? $user->last_login_at->format('d M Y, h:i A') : 'Never' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center border-top pt-3">
                    <span class="text-muted small">Registered: {{ $user->created_at->format('d M Y') }}</span>
                    @if($user->id !== auth()->id())
                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user account permanently?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash"></i> Delete User
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
