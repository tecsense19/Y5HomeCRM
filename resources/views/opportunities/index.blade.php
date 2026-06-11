{{-- opportunities/index.blade.php --}}
@extends('layouts.app')
@section('title','Opportunities')
@section('page-title','Opportunities')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        All Opportunities
        <a href="{{ route('opportunities.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> New</a>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Opp #</th>
                    <th>Customer</th>
                    <th>Project</th>
                    <th>Value</th>
                    <th>Stage</th>
                    <th>Probability</th>
                    <th>Close Date</th>
                    <th>Action</th>
                </tr></thead>
            <tbody>
            @forelse($opportunities as $o)
            <tr>
                <td class="fw-semibold text-primary">{{ $o->opportunity_number }}</td>
                <td>{{ $o->customer_name }}</td>
                <td>{{ $o->project_name ?? '–' }}</td>
                <td>{{ $o->expected_revenue ? '₹'.number_format($o->expected_revenue) : '–' }}</td>
                <td><span class="badge bg-info text-dark">{{ \App\Models\Opportunity::stages()[$o->stage] ?? $o->stage }}</span></td>
                <td>
                    <div class="progress" style="height:6px;min-width:60px">
                        <div class="progress-bar" style="width:{{ $o->probability }}%"></div>
                    </div>
                    <small class="text-muted">{{ $o->probability }}%</small>
                </td>
                <td class="small text-muted">{{ $o->expected_closing_date?->format('d M Y') ?? '–' }}</td>
                <td>
                    <div class="d-flex gap-1 justify-content-left">
                        <a href="{{ route('opportunities.show', $o) }}" class="btn btn-xs btn-outline-primary btn-sm" title="View"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('opportunities.edit', $o) }}" class="btn btn-xs btn-outline-secondary btn-sm" title="Edit"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('opportunities.destroy', $o) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this opportunity?');" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-outline-danger btn-sm" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-4">No opportunities.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($opportunities->hasPages())<div class="card-footer">{{ $opportunities->links() }}</div>@endif
</div>
@endsection
