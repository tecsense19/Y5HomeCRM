@extends('layouts.app')
@section('title','Locations')
@section('page-title','Locations (Room Tags)')
@section('content')


<div class="card mb-4">
    <div class="card-header">
        Add New Location
    </div>
    <div class="card-body">
        <form action="{{ route('locations.store') }}" method="POST" class="row g-3 align-items-center">
            @csrf
            <div class="col-auto">
                <label for="name" class="visually-hidden">Location Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="E.g., Master Bed" required>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Add Location</button>
            </div>
        </form>
        @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>All Locations</span>
        <form action="{{ route('locations.index') }}" method="GET" class="d-flex m-0" style="width: 250px;">
            <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search locations..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-sm btn-primary" title="Search"><i class="bi bi-search"></i></button>
            @if(request('search'))
                <a href="{{ route('locations.index') }}" class="btn btn-sm btn-outline-secondary ms-1" title="Clear"><i class="bi bi-x"></i></a>
            @endif
        </form>
    </div>
    <div class="card-body p-3 bg-white">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 1rem;">
            @forelse($locations as $loc)
            <div class="card shadow-sm position-relative" style="border: 1px solid #e5e7eb; border-radius: 16px; aspect-ratio: 1 / 1;">
                
                <!-- Status Toggle (Top Left) -->
                <div class="position-absolute top-0 start-0 p-2" style="z-index: 2;">
                    <form action="{{ route('locations.status', $loc) }}" method="POST" class="m-0 p-0">
                        @csrf @method('PATCH')
                        <div class="form-check form-switch m-0 p-0">
                            <input class="form-check-input cursor-pointer m-0 shadow-none" type="checkbox" role="switch" onchange="this.form.submit()" {{ $loc->is_active ? 'checked' : '' }} style="width: 1.6em; height: 0.8em;" title="Toggle Status">
                        </div>
                    </form>
                </div>

                <!-- Actions (Top Right) -->
                <div class="position-absolute top-0 end-0 p-2 d-flex gap-1" style="z-index: 2;">
                    <form action="{{ route('locations.destroy', $loc) }}" method="POST" onsubmit="return confirm('Delete this location?');" class="m-0 p-0 lh-1">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-link text-danger p-0 shadow-none m-0 lh-1" title="Delete">
                            <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                        </button>
                    </form>
                </div>
                
                <div class="card-body text-center p-2 d-flex flex-column align-items-center justify-content-center h-100 pt-4">
                    <!-- Icon Preview -->
                    <div class="mb-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #e0f2fe; border-radius: 12px; color: #0284c7;">
                        <i class="bi bi-geo-alt-fill fs-4"></i>
                    </div>

                    <!-- Title -->
                    <h6 class="card-title text-truncate w-100 mb-0 fw-bold" style="font-size: 0.75rem;" title="{{ $loc->name }}">{{ $loc->name }}</h6>
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-5 w-100">
                <i class="bi bi-geo-alt fs-1 d-block mb-3"></i>
                No locations found.
            </div>
            @endforelse
        </div>
    </div>
    @if($locations->hasPages())<div class="card-footer">{{ $locations->links() }}</div>@endif
</div>
@endsection
