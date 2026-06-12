@extends('layouts.app')
@section('title', isset($frame) ? 'Edit Frame' : 'Create Frame')
@section('page-title', isset($frame) ? 'Edit Frame: '.$frame->unique_id : 'Create New Frame')
@section('content')

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ isset($frame) ? route('frames.update', $frame) : route('frames.store') }}" method="POST">
            @csrf
            @if(isset($frame)) @method('PUT') @endif
            
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Unique ID (e.g., arch-2m-2switch)</label>
                    <input type="text" name="unique_id" class="form-control" value="{{ old('unique_id', $frame->unique_id ?? '') }}" required>
                    @error('unique_id')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Category (e.g., 2M, 4M)</label>
                    <input type="text" name="category" class="form-control" value="{{ old('category', $frame->category ?? '') }}" required>
                    @error('category')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Label / Title</label>
                    <input type="text" name="label" class="form-control" value="{{ old('label', $frame->label ?? '') }}" required>
                    @error('label')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">Grid Cols</label>
                    <input type="number" name="cols" class="form-control" value="{{ old('cols', $frame->cols ?? 3) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Grid Rows</label>
                    <input type="number" name="rows" class="form-control" value="{{ old('rows', $frame->rows ?? 1) }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Frame Width</label>
                    <input type="number" name="frame_w" class="form-control" value="{{ old('frame_w', $frame->frame_w ?? 290) }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Frame Height</label>
                    <input type="number" name="frame_h" class="form-control" value="{{ old('frame_h', $frame->frame_h ?? 280) }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Inset</label>
                    <input type="number" name="inset" class="form-control" value="{{ old('inset', $frame->inset ?? 24) }}" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Grid Items (JSON)</label>
                <div class="small text-muted mb-2">Provide a valid JSON array of slots/spacers. Example: <code>[{"type":"slot","x":0,"y":0}]</code></div>
                <textarea name="items" class="form-control font-monospace" rows="10" required>{{ old('items', isset($frame) ? json_encode($frame->items, JSON_PRETTY_PRINT) : '[\n  {\n    "type": "slot",\n    "x": 0,\n    "y": 0\n  }\n]') }}</textarea>
                @error('items')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Decorations (JSON - Optional)</label>
                <textarea name="decorations" class="form-control font-monospace" rows="3">{{ old('decorations', isset($frame) ? json_encode($frame->decorations, JSON_PRETTY_PRINT) : '[]') }}</textarea>
                @error('decorations')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('frames.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Frame</button>
            </div>
        </form>
    </div>
</div>

@endsection
