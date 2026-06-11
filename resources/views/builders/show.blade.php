@extends('layouts.app')
@section('title', $builder->name)
@section('page-title', 'Builder Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Builder: {{ $builder->name }}</span>
                <div>
                    <a href="{{ route('builders.index') }}" class="btn btn-outline-secondary btn-sm me-1">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                    <a href="{{ route('builders.edit', $builder) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil"></i> Edit Builder
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="text-center py-3">
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; font-size: 2rem;">
                        {{ strtoupper(substr($builder->name, 0, 1)) }}
                    </div>
                    <h5 class="fw-bold mb-1">{{ $builder->name }}</h5>
                    <span class="badge bg-secondary mb-2">{{ $builder->company_name ?: 'Independent Builder' }}</span>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th class="w-30 bg-light">Company Name</th>
                                <td>{{ $builder->company_name ?: '–' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Mobile Number</th>
                                <td><a href="tel:{{ $builder->mobile_number }}" class="text-decoration-none">{{ $builder->mobile_number }}</a></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Email Address</th>
                                <td>
                                    @if($builder->email)
                                        <a href="mailto:{{ $builder->email }}" class="text-decoration-none">{{ $builder->email }}</a>
                                    @else –
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">City</th>
                                <td>{{ $builder->city ?: '–' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Status</th>
                                <td>
                                    @if($builder->is_active)
                                        <span class="badge bg-success">Active Partner</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive Partner</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 border-top pt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-person-lines-fill me-1"></i> Referred Leads</h6>
                    </div>
                    <div class="table-responsive border rounded">
                        <table class="table mb-0 align-middle small text-center">
                            <tbody>
                                <tr>
                                    <td class="text-muted py-4">There are no leads linked directly to this Builder yet.<br><small>Future updates will support automatic referral link tracking.</small></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center border-top pt-3">
                    <span class="text-muted small">Added On: {{ $builder->created_at->format('d M Y') }}</span>
                    <form action="{{ route('builders.destroy', $builder) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this builder permanently?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash"></i> Delete Builder
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
