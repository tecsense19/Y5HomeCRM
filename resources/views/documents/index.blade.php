@extends('layouts.app')
@section('title', 'Documents')
@section('page-title', 'Document Management')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>All Project Documents</span>
        <a href="{{ route('documents.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-upload"></i> Upload Document
        </a>
    </div>

    <!-- Filters -->
    <div class="card-body border-bottom">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Search file name, notes..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    <option value="site_photos" {{ request('category') === 'site_photos' ? 'selected' : '' }}>Site Photos</option>
                    <option value="quotations" {{ request('category') === 'quotations' ? 'selected' : '' }}>Quotations</option>
                    <option value="drawings" {{ request('category') === 'drawings' ? 'selected' : '' }}>Drawings</option>
                    <option value="agreements" {{ request('category') === 'agreements' ? 'selected' : '' }}>Agreements</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Category</th>
                        <th>Associated With</th>
                        <th>Size</th>
                        <th>Uploaded By</th>
                        <th>Uploaded Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                    <tr>
                        <td>
                            <a href="{{ route('documents.show', $doc) }}" class="fw-semibold text-primary text-decoration-none">
                                <i class="bi bi-file-earmark-arrow-down me-1"></i>{{ $doc->original_name }}
                            </a>
                            @if($doc->notes)
                            <div class="text-muted small text-truncate" style="max-width: 300px;">{{ $doc->notes }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary text-capitalize">
                                {{ str_replace('_', ' ', $doc->category) }}
                            </span>
                        </td>
                        <td class="small">
                            @if($doc->documentable)
                                @if(method_exists($doc->documentable, 'customer'))
                                    {{ $doc->documentable->customer->customer_name ?? $doc->documentable->customer_name ?? 'Lead #'.$doc->documentable->id }}
                                @else
                                    {{ $doc->documentable->customer_name ?? $doc->documentable->name ?? 'Record #'.$doc->documentable_id }}
                                @endif
                                <span class="text-muted">({{ class_basename($doc->documentable_type) }})</span>
                            @else
                                <span class="text-muted">Orphaned (ID: {{ $doc->documentable_id }})</span>
                            @endif
                        </td>
                        <td class="small text-muted">{{ number_format($doc->file_size / 1024 / 1024, 2) }} MB</td>
                        <td class="small">{{ $doc->uploadedBy?->name ?? 'System' }}</td>
                        <td class="small text-muted">{{ $doc->created_at->format('d M Y, h:i A') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('documents.download', $doc) }}" class="btn btn-xs btn-outline-primary btn-sm" title="Download">
                                    <i class="bi bi-download"></i>
                                </a>
                                <a href="{{ route('documents.show', $doc) }}" class="btn btn-xs btn-outline-info btn-sm" title="Details">
                                    <i class="bi bi-info-circle"></i>
                                </a>
                                <form action="{{ route('documents.destroy', $doc) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger btn-sm" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No documents uploaded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($documents->hasPages())
    <div class="card-footer">{{ $documents->links() }}</div>
    @endif
</div>
@endsection
