{{-- users/index.blade.php --}}
@extends('layouts.app')
@section('title','Users')
@section('page-title','User Management')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>All Users</span>
        <div class="d-flex align-items-center gap-2">
            <form action="{{ route('users.index') }}" method="GET" class="d-flex m-0" style="width: 250px;">
                <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search users..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-primary" title="Search"><i class="bi bi-search"></i></button>
                @if(request('search'))
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary ms-1" title="Clear"><i class="bi bi-x"></i></a>
                @endif
            </form>
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm text-nowrap"><i class="bi bi-plus"></i> Add User</a>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Name</th><th>Email</th><th>Mobile</th><th>Role</th><th>Center</th><th>Status</th><th>Last Login</th><th>Action</th></tr></thead>
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
                    <div class="d-flex gap-1 justify-content-left">
                        <a href="{{ route('users.show', $u) }}" class="btn btn-xs btn-outline-primary btn-sm" title="View"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('users.edit', $u) }}" class="btn btn-xs btn-outline-secondary btn-sm" title="Edit"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('users.destroy', $u) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-outline-danger btn-sm" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
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
