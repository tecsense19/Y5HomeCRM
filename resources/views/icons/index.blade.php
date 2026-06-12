@extends('layouts.app')
@section('title','Custom Icons')
@section('page-title','Custom Icons')
@section('content')


<div class="card mb-4">
    <div class="card-header">
        Upload New Icon
    </div>
    <div class="card-body">
        <form action="{{ route('icons.store') }}" method="POST" enctype="multipart/form-data" class="row g-3 align-items-center">
            @csrf
            <div class="col-auto">
                <label for="name" class="visually-hidden">Icon Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="E.g., Custom Light" required>
            </div>
            <div class="col-auto">
                <label for="icon" class="visually-hidden">Upload Icon</label>
                <input type="file" class="form-control" id="icon" name="icon" accept=".png,.jpg,.jpeg,.svg" required>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </form>
        @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
        @error('icon')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>All Uploaded Icons</span>
        <form action="{{ route('icons.index') }}" method="GET" class="d-flex m-0" style="width: 250px;">
            <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search icons..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-sm btn-primary" title="Search"><i class="bi bi-search"></i></button>
            @if(request('search'))
                <a href="{{ route('icons.index') }}" class="btn btn-sm btn-outline-secondary ms-1" title="Clear"><i class="bi bi-x"></i></a>
            @endif
        </form>
    </div>
    <div class="card-body p-3 bg-white">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 1rem;">
            @forelse($icons as $icon)
            <div class="card shadow-sm position-relative" style="border: 1px solid #e5e7eb; border-radius: 16px; aspect-ratio: 1 / 1;">
                
                <!-- Status Toggle (Top Left) -->
                <div class="position-absolute top-0 start-0 p-2" style="z-index: 2;">
                    <form action="{{ route('icons.status', $icon) }}" method="POST" class="m-0 p-0">
                        @csrf @method('PATCH')
                        <div class="form-check form-switch m-0 p-0">
                            <input class="form-check-input cursor-pointer m-0 shadow-none" type="checkbox" role="switch" onchange="this.form.submit()" {{ $icon->is_active ? 'checked' : '' }} style="width: 1.6em; height: 0.8em;" title="Toggle Status">
                        </div>
                    </form>
                </div>

                <!-- Actions (Top Right) -->
                <div class="position-absolute top-0 end-0 p-2 d-flex gap-1" style="z-index: 2;">
                    <button type="button" class="btn btn-link text-primary p-0 shadow-none m-0 lh-1" data-bs-toggle="modal" data-bs-target="#editIconModal{{ $icon->id }}" title="Edit">
                        <i class="bi bi-pencil" style="font-size: 0.75rem;"></i>
                    </button>
                    <form action="{{ route('icons.destroy', $icon) }}" method="POST" onsubmit="return confirm('Delete this icon? It will be removed from the Kiosk.');" class="m-0 p-0 lh-1">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-link text-danger p-0 shadow-none m-0 lh-1" title="Delete">
                            <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                        </button>
                    </form>
                </div>
                
                <div class="card-body text-center p-2 d-flex flex-column align-items-center justify-content-center h-100 pt-4">
                    <!-- Icon Preview -->
                    <div class="mb-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #f3f4f6; border-radius: 12px;">
                        @if(str_starts_with($icon->image_path, 'system:'))
                            <i class="bi bi-cpu fs-4 text-secondary" title="Built-in System Icon"></i>
                        @else
                            <img src="{{ asset('storage/'.$icon->image_path) }}" alt="{{ $icon->name }}" style="max-width: 28px; max-height: 28px;">
                        @endif
                    </div>

                    <!-- Title -->
                    <h6 class="card-title text-truncate w-100 mb-0 fw-bold" style="font-size: 0.75rem;" title="{{ $icon->name }}">{{ $icon->name }}</h6>
                </div>


                        <!-- Edit Modal -->
                        <div class="modal fade text-start" id="editIconModal{{ $icon->id }}" tabindex="-1" aria-labelledby="editIconModalLabel{{ $icon->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('icons.update', $icon) }}" method="POST" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editIconModalLabel{{ $icon->id }}">Edit Icon</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Icon Name</label>
                                                <input type="text" class="form-control" name="name" value="{{ $icon->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label d-block">Icon Preview</label>
                                                <div class="d-flex align-items-center gap-3">
                                                    <!-- Current -->
                                                    <div class="d-inline-flex flex-column align-items-center">
                                                        <div class="d-inline-flex align-items-center justify-content-center p-2 mb-1" style="background: #f3f4f6; border-radius: 8px; width: 64px; height: 64px; border: 1px solid #e5e7eb;">
                                                            @if(str_starts_with($icon->image_path, 'system:'))
                                                                <i class="bi bi-cpu fs-3 text-secondary" title="Built-in System Icon"></i>
                                                            @else
                                                                <img src="{{ asset('storage/'.$icon->image_path) }}" alt="{{ $icon->name }}" style="max-width: 48px; max-height: 48px;">
                                                            @endif
                                                        </div>
                                                        <small class="text-muted" style="font-size: 0.65rem;">Current</small>
                                                    </div>

                                                    <i class="bi bi-arrow-right text-muted fs-5 d-none" id="preview-arrow-{{ $icon->id }}"></i>

                                                    <!-- New -->
                                                    <div class="d-none flex-column align-items-center" id="preview-container-{{ $icon->id }}">
                                                        <div class="d-inline-flex align-items-center justify-content-center p-2 mb-1" style="background: #e0f2fe; border-radius: 8px; width: 64px; height: 64px; border: 1px solid #bae6fd;">
                                                            <img src="" id="preview-img-{{ $icon->id }}" style="max-width: 48px; max-height: 48px;">
                                                        </div>
                                                        <small class="text-primary fw-bold" style="font-size: 0.65rem;">New</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Replace Image (Optional)</label>
                                                <input type="file" class="form-control" name="icon" accept=".png,.jpg,.jpeg,.svg" onchange="previewSelectedIcon(this, '{{ $icon->id }}')">
                                                <small class="text-muted">Leave empty to keep the current image.</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
            </div>
            @empty
            <div class="text-center text-muted py-5 w-100">
                <i class="bi bi-images fs-1 d-block mb-3"></i>
                No icons found.
            </div>
            @endforelse
        </div>
    </div>
    @if($icons->hasPages())<div class="card-footer">{{ $icons->links() }}</div>@endif
</div>

<script>
function previewSelectedIcon(input, id) {
    const container = document.getElementById('preview-container-' + id);
    const img = document.getElementById('preview-img-' + id);
    const arrow = document.getElementById('preview-arrow-' + id);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            container.classList.remove('d-none');
            container.classList.add('d-inline-flex');
            arrow.classList.remove('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        img.src = "";
        container.classList.add('d-none');
        container.classList.remove('d-inline-flex');
        arrow.classList.add('d-none');
    }
}
</script>
@endsection
