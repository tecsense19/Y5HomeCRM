@extends('layouts.app')
@section('title', 'Document Details')
@section('page-title', 'Document Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>File Metadata</span>
                <div>
                    <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary btn-sm me-1">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                    <a href="{{ route('documents.download', $document) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-download"></i> Download File
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th class="w-30 bg-light">Original Name</th>
                                <td>{{ $document->original_name }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">System File Name</th>
                                <td><code>{{ $document->file_name }}</code></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Category</th>
                                <td>
                                    <span class="badge bg-secondary text-capitalize">
                                        {{ str_replace('_', ' ', $document->category) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Associated With</th>
                                <td>
                                    @if($document->documentable)
                                        @if(method_exists($document->documentable, 'customer'))
                                            {{ $document->documentable->customer->customer_name ?? $document->documentable->customer_name ?? 'Lead #'.$document->documentable->id }}
                                        @else
                                            {{ $document->documentable->customer_name ?? $document->documentable->name ?? 'Record #'.$document->documentable_id }}
                                        @endif
                                        <span class="text-muted">({{ class_basename($document->documentable_type) }} ID: {{ $document->documentable_id }})</span>
                                    @else
                                        <span class="text-muted text-warning">Orphaned Record (ID: {{ $document->documentable_id }})</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">File Size</th>
                                <td>{{ number_format($document->file_size / 1024, 2) }} KB ({{ number_format($document->file_size / 1024 / 1024, 2) }} MB)</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Mime Type</th>
                                <td><code>{{ $document->mime_type }}</code></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Uploaded By</th>
                                <td>{{ $document->uploadedBy?->name ?? 'System' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Upload Time</th>
                                <td>{{ $document->created_at->format('d M Y, h:i A') }} ({{ $document->created_at->diffForHumans() }})</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Notes</th>
                                <td>{!! nl2br(e($document->notes)) ?: '<span class="text-muted">No notes provided.</span>' !!}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if(in_array(strtolower(pathinfo($document->original_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                    <div class="mt-4 border p-3 rounded bg-light text-center">
                        <h6 class="text-start mb-3"><i class="bi bi-image"></i> Image Preview</h6>
                        <img src="{{ route('documents.download', $document) }}" alt="Preview" class="img-fluid rounded border shadow-sm" style="max-height: 450px;">
                    </div>
                @endif

                <div class="mt-4 d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Storage Path: <code>{{ $document->file_path }}</code></span>
                    <form action="{{ route('documents.destroy', $document) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this document permanently?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i> Delete Document
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
