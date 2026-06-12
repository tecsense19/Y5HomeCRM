@extends('layouts.app')
@section('title','Frame Colors')
@section('page-title','Frame Colors')
@section('content')


<div class="card mb-4">
    <div class="card-header">
        Add New Frame Color
    </div>
    <div class="card-body">
        <form action="{{ route('frame-colors.store') }}" method="POST" class="row g-3 align-items-center">
            @csrf
            <div class="col-auto">
                <label for="series" class="visually-hidden">Series</label>
                <select class="form-select" id="series" name="series" required>
                    <option value="" disabled selected>Select Series</option>
                    <option value="classic">Classic Series</option>
                    <option value="architectural-elite">Architectural Series - Elite</option>
                    <option value="architectural-pro">Architectural Series - Pro</option>
                    <option value="architectural-pro-plus">Architectural Series - Pro+</option>
                </select>
            </div>
            <div class="col-auto">
                <label for="name" class="visually-hidden">Color Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Color Name (e.g., Black)" required>
            </div>
            <div class="col-auto">
                <label for="hex_code" class="visually-hidden">Hex Code (Optional)</label>
                <input type="text" class="form-control" id="hex_code" name="hex_code" placeholder="#000000">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Add Color</button>
            </div>
        </form>
        @error('series')<div class="text-danger small">{{ $message }}</div>@enderror
        @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>All Frame Colors</span>
        <form action="{{ route('frame-colors.index') }}" method="GET" class="d-flex m-0" style="width: 250px;">
            <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search colors..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-sm btn-primary" title="Search"><i class="bi bi-search"></i></button>
            @if(request('search'))
                <a href="{{ route('frame-colors.index') }}" class="btn btn-sm btn-outline-secondary ms-1" title="Clear"><i class="bi bi-x"></i></a>
            @endif
        </form>
    </div>
    <div class="card-body p-3 bg-white">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 1rem;">
            @forelse($colors as $color)
            <div class="card shadow-sm position-relative" style="border: 1px solid #e5e7eb; border-radius: 16px; aspect-ratio: 1 / 1;">
                
                <!-- Status Toggle (Top Left) -->
                <div class="position-absolute top-0 start-0 p-2" style="z-index: 2;">
                    <form action="{{ route('frame-colors.status', $color) }}" method="POST" class="m-0 p-0">
                        @csrf @method('PATCH')
                        <div class="form-check form-switch m-0 p-0">
                            <input class="form-check-input cursor-pointer m-0 shadow-none" type="checkbox" role="switch" onchange="this.form.submit()" {{ $color->is_active ? 'checked' : '' }} style="width: 1.6em; height: 0.8em;" title="Toggle Status">
                        </div>
                    </form>
                </div>

                <!-- Actions (Top Right) -->
                <div class="position-absolute top-0 end-0 p-2 d-flex gap-1" style="z-index: 2;">
                    <form action="{{ route('frame-colors.destroy', $color) }}" method="POST" onsubmit="return confirm('Delete this frame color?');" class="m-0 p-0 lh-1">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-link text-danger p-0 shadow-none m-0 lh-1" title="Delete">
                            <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                        </button>
                    </form>
                </div>
                
                <div class="card-body text-center p-2 d-flex flex-column align-items-center justify-content-center h-100 pt-4">
                    <!-- Color Circle -->
                    <div class="mb-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: {{ $color->hex_code ?? '#f3f4f6' }}; border-radius: 50%; border: 2px solid #e5e7eb; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        @if(!$color->hex_code)
                            <i class="bi bi-palette text-muted"></i>
                        @endif
                    </div>

                    <!-- Title -->
                    <h6 class="card-title text-truncate w-100 mb-1 fw-bold" style="font-size: 0.75rem;" title="{{ $color->name }}">{{ $color->name }}</h6>
                    
                    <!-- Series Badge -->
                    @php
                        $seriesColors = [
                            'classic' => ['bg' => '#e0f2fe', 'text' => '#0284c7'], // light blue
                            'architectural-elite' => ['bg' => '#ffedd5', 'text' => '#c2410c'], // light orange (like Qualified)
                            'architectural-pro' => ['bg' => '#fce7f3', 'text' => '#be185d'], // light pink
                            'architectural-pro-plus' => ['bg' => '#e0e7ff', 'text' => '#4338ca'], // light indigo
                        ];
                        $theme = $seriesColors[$color->series] ?? ['bg' => '#f3f4f6', 'text' => '#4b5563'];
                    @endphp
                    <span class="badge text-uppercase text-truncate w-100" style="background-color: {{ $theme['bg'] }}; color: {{ $theme['text'] }}; font-size: 0.6rem; font-weight: 600; padding: 0.35rem; border-radius: 6px;" title="{{ str_replace('-', ' ', $color->series) }}">{{ str_replace('-', ' ', $color->series) }}</span>
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-5 w-100">
                <i class="bi bi-palette fs-1 d-block mb-3"></i>
                No frame colors found.
            </div>
            @endforelse
        </div>
    </div>
    @if($colors->hasPages())<div class="card-footer">{{ $colors->links() }}</div>@endif
</div>
@endsection
