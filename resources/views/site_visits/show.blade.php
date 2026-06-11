@extends('layouts.app')
@section('title', 'Site Visit Details')
@section('page-title', 'Site Visit Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Site Visit #{{ $siteVisit->visit_id }}</span>
                <div>
                    <a href="{{ route('site-visits.index') }}" class="btn btn-outline-secondary btn-sm me-1">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                    <a href="{{ route('site-visits.edit', $siteVisit) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil"></i> Edit Visit
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th class="w-30 bg-light">Visit ID</th>
                                <td><strong class="text-primary">{{ $siteVisit->visit_id }}</strong></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Customer Name</th>
                                <td>
                                    @if($siteVisit->customer_id)
                                        <a href="{{ route('customers.show', $siteVisit->customer_id) }}" class="fw-semibold text-primary text-decoration-none">
                                            {{ $siteVisit->customer_name }}
                                        </a>
                                    @else
                                        {{ $siteVisit->customer_name }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Associated Lead</th>
                                <td>
                                    @if($siteVisit->lead)
                                        <a href="{{ route('leads.show', $siteVisit->lead) }}" class="fw-semibold text-primary text-decoration-none">
                                            {{ $siteVisit->lead->lead_number }}
                                        </a>
                                    @else
                                        <span class="text-muted">No associated lead</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Visit Date</th>
                                <td>{{ $siteVisit->visit_date ? $siteVisit->visit_date->format('d M Y') : '–' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Conducted By</th>
                                <td>{{ $siteVisit->visitedBy?->name ?? '–' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Location / Address</th>
                                <td>{{ $siteVisit->location ?: '–' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Estimated Project Value</th>
                                <td>
                                    @if($siteVisit->estimated_project_value)
                                        ₹{{ number_format($siteVisit->estimated_project_value, 2) }}
                                    @else –
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Requirement Summary</th>
                                <td>{!! nl2br(e($siteVisit->requirement_summary)) ?: '<span class="text-muted">No summary provided.</span>' !!}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Products Required</th>
                                <td>
                                    @if(!empty($siteVisit->products_required))
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($siteVisit->products_required as $prodKey)
                                                <span class="badge bg-info text-dark">
                                                    {{ \App\Models\SiteVisit::products()[$prodKey] ?? $prodKey }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">No products selected.</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Notes</th>
                                <td>{!! nl2br(e($siteVisit->notes)) ?: '<span class="text-muted">No notes provided.</span>' !!}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Site Photos / Attachments Section -->
                <div class="mt-4 border-top pt-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-images me-1"></i> Site Photos & Attachments</h6>
                    @if($siteVisit->documents->count() > 0)
                        <div class="row row-cols-1 row-cols-md-3 g-3">
                            @foreach($siteVisit->documents as $doc)
                                <div class="col">
                                    <div class="card h-100 shadow-sm border">
                                        @if(in_array(strtolower(pathinfo($doc->original_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']))
                                            <img src="{{ route('documents.download', $doc) }}" class="card-img-top border-bottom" style="height: 120px; object-fit: cover;" alt="Preview">
                                        @else
                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center border-bottom text-muted" style="height: 120px;">
                                                <i class="bi bi-file-earmark-text fs-1"></i>
                                            </div>
                                        @endif
                                        <div class="card-body p-2 d-flex flex-column justify-content-between">
                                            <span class="small fw-semibold text-truncate d-block mb-1" title="{{ $doc->original_name }}">{{ $doc->original_name }}</span>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <span class="text-muted small" style="font-size: 0.75rem;">{{ number_format($doc->file_size / 1024, 1) }} KB</span>
                                                <a href="{{ route('documents.download', $doc) }}" class="btn btn-xs btn-primary btn-sm py-0 px-2" style="font-size: 0.75rem;"><i class="bi bi-download"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small mb-0">No site photos uploaded for this visit.</p>
                    @endif
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center border-top pt-3">
                    <span class="text-muted small">Recorded By: {{ $siteVisit->createdBy?->name ?? 'System' }}</span>
                    <form action="{{ route('site-visits.destroy', $siteVisit) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this site visit permanently?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash"></i> Delete Visit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
