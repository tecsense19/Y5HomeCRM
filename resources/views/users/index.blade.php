{{-- users/index.blade.php --}}
@extends('layouts.app')
@section('title','Users')
@section('page-title','User Management')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        All Users
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> Add User</a>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Name</th><th>Email</th><th>Mobile</th><th>Role</th><th>Center</th><th>Status</th><th>Last Login</th><th></th></tr></thead>
            <tbody>
            @forelse($users as $u)
            <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->mobile ?? '–' }}</td>
                <td><span class="badge bg-secondary text-uppercase" style="font-size:.65rem">{{ str_replace('-',' ',$u->role) }}</span></td>
                <td class="small">{{ $u->experienceCenter?->center_name ?? '–' }}</td>
                <td>@if($u->is_active)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif</td>
                <td class="small text-muted">{{ $u->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                <td>
                    <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-4">No users.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())<div class="card-footer">{{ $users->links() }}</div>@endif
</div>
@endsection
