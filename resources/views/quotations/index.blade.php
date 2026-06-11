{{-- quotations/index.blade.php --}}
@extends('layouts.app')
@section('title','Quotations')
@section('page-title','Quotations')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        All Quotations
        <a href="{{ route('quotations.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> New Quotation</a>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Quotation #</th><th>Customer</th><th>Date</th><th>Value</th><th>Version</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($quotations as $q)
            <tr>
                <td class="fw-semibold text-primary">{{ $q->quotation_number }}</td>
                <td>{{ $q->customer_name }}</td>
                <td>{{ $q->quotation_date->format('d M Y') }}</td>
                <td>{{ $q->quotation_value ? '₹'.number_format($q->quotation_value) : '–' }}</td>
                <td>v{{ $q->version_number }}</td>
                <td>
                    @php $cls = ['draft'=>'secondary','sent'=>'primary','approved'=>'success','rejected'=>'danger'][$q->status] ?? 'secondary' @endphp
                    <span class="badge bg-{{ $cls }}">{{ ucfirst($q->status) }}</span>
                </td>
                <td><a href="{{ route('quotations.show', $q) }}" class="btn btn-sm btn-outline-primary">View</a></td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No quotations.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($quotations->hasPages())<div class="card-footer">{{ $quotations->links() }}</div>@endif
</div>
@endsection
