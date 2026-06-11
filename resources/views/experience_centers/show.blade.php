@extends('layouts.app')
@section('title', $experienceCenter->center_name)
@section('page-title', 'Experience Center Details')

@section('content')
<div class="row g-4">
    <!-- Left Card: Metadata -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body text-center py-4">
                <div class="rounded bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; font-size: 2rem;">
                    <i class="bi bi-building"></i>
                </div>
                <h5 class="card-title mb-1 fw-bold">{{ $experienceCenter->center_name }}</h5>
                <p class="text-muted small mb-3">Code: <code>{{ $experienceCenter->center_code }}</code></p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('experience-centers.edit', $experienceCenter) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil"></i> Edit Center
                    </a>
                </div>
            </div>
            <div class="card-body border-top p-0">
                <ul class="list-group list-group-flush rounded-bottom">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted small">Owner</span>
                        <span class="fw-semibold small">{{ $experienceCenter->owner_name }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted small">Company Name</span>
                        <span class="fw-semibold small">{{ $experienceCenter->company_name ?: '–' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted small">GST Number</span>
                        <span class="fw-semibold small"><code>{{ $experienceCenter->gst_number ?: '–' }}</code></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted small">Mobile</span>
                        <span class="fw-semibold small"><a href="tel:{{ $experienceCenter->mobile_number }}" class="text-decoration-none">{{ $experienceCenter->mobile_number }}</a></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted small">Email</span>
                        <span class="fw-semibold small">
                            @if($experienceCenter->email)
                                <a href="mailto:{{ $experienceCenter->email }}" class="text-decoration-none">{{ $experienceCenter->email }}</a>
                            @else –
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted small">City / State</span>
                        <span class="fw-semibold small">{{ $experienceCenter->city }}, {{ $experienceCenter->state }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Status Card -->
        <div class="card mb-4">
            <div class="card-header bg-light fw-bold text-muted small uppercase">Center Status & Agreement</div>
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Current Status:</span>
                    @php $statusCls = ['active'=>'success','inactive'=>'secondary','suspended'=>'danger'][$experienceCenter->status] ?? 'secondary' @endphp
                    <span class="badge bg-{{ $statusCls }} text-uppercase">{{ $experienceCenter->status }}</span>
                </div>
                <form action="{{ route('experience-centers.status', $experienceCenter) }}" method="POST" class="d-flex align-items-center gap-2 mb-3">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="form-select form-select-sm">
                        <option value="active" {{ $experienceCenter->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $experienceCenter->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ $experienceCenter->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                </form>

                <div class="border-top pt-2">
                    <div class="d-flex justify-content-between py-2 small">
                        <span class="text-muted">Start Date:</span>
                        <span class="fw-semibold">{{ $experienceCenter->agreement_start_date ? $experienceCenter->agreement_start_date->format('d M Y') : '–' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 small">
                        <span class="text-muted">End Date:</span>
                        <span class="fw-semibold">{{ $experienceCenter->agreement_end_date ? $experienceCenter->agreement_end_date->format('d M Y') : '–' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 small">
                        <span class="text-muted">Security Deposit:</span>
                        <span class="fw-semibold text-success">
                            @if($experienceCenter->security_deposit_amount)
                                ₹{{ number_format($experienceCenter->security_deposit_amount, 2) }}
                            @else –
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Card: Associated Leads & Users -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white border-bottom">
                <ul class="nav nav-tabs card-header-tabs" id="centerDetailTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="leads-tab" data-bs-toggle="tab" data-bs-target="#leads" type="button" role="tab" aria-controls="leads" aria-selected="true">
                            <i class="bi bi-person-lines-fill"></i> Leads ({{ $experienceCenter->leads->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="false">
                            <i class="bi bi-people"></i> Staff / Users ({{ $experienceCenter->users->count() }})
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="centerDetailTabsContent">
                <!-- Leads Tab -->
                <div class="tab-pane fade show active p-0" id="leads" role="tabpanel" aria-labelledby="leads-tab">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead>
                                <tr class="bg-light">
                                    <th>Lead #</th>
                                    <th>Customer Name</th>
                                    <th>Mobile</th>
                                    <th>Budget</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($experienceCenter->leads as $lead)
                                <tr>
                                    <td>
                                        <a href="{{ route('leads.show', $lead) }}" class="fw-semibold text-primary text-decoration-none">
                                            {{ $lead->lead_number }}
                                        </a>
                                    </td>
                                    <td>{{ $lead->customer_name }}</td>
                                    <td class="small">{{ $lead->mobile_number }}</td>
                                    <td class="small">₹{{ number_format($lead->estimated_budget) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $lead->status }}">
                                            {{ \App\Models\Lead::statuses()[$lead->status] ?? $lead->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('leads.show', $lead) }}" class="btn btn-xs btn-outline-primary btn-sm">View</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No leads assigned to this center.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Users Tab -->
                <div class="tab-pane fade p-0" id="users" role="tabpanel" aria-labelledby="users-tab">
                    <div class="card-body border-bottom d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Manage staff users associated with this center</span>
                        @if(auth()->user()->isSuperAdmin() || auth()->user()->isExperienceCenterUser())
                        <button type="button" class="btn btn-xs btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                            <i class="bi bi-person-plus"></i> Add Staff User
                        </button>
                        @endif
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead>
                                <tr class="bg-light">
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isExperienceCenterUser())
                                        <th>Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($experienceCenter->users as $u)
                                 <tr>
                                     <td class="fw-semibold">{{ $u->name }}</td>
                                     <td class="small">{{ $u->email }}</td>
                                     <td class="small">{{ $u->mobile ?: '–' }}</td>
                                     <td class="small text-uppercase">{{ str_replace('-', ' ', $u->role) }}</td>
                                     <td>
                                         @if($u->is_active)
                                             <span class="badge bg-success">Active</span>
                                         @else
                                             <span class="badge bg-secondary">Inactive</span>
                                         @endif
                                     </td>
                                     @if(auth()->user()->isSuperAdmin() || auth()->user()->isExperienceCenterUser())
                                     <td>
                                         <div class="d-flex gap-1">
                                             @if(auth()->id() !== $u->id)
                                             <form action="{{ route('users.impersonate', $u) }}" method="POST" class="d-inline">
                                                 @csrf
                                                 <button type="submit" class="btn btn-xs btn-outline-primary btn-sm d-flex align-items-center gap-1">
                                                     <i class="bi bi-box-arrow-in-right"></i> Login
                                                 </button>
                                             </form>
                                             @endif
                                             
                                             @if(auth()->user()->isSuperAdmin() || (auth()->user()->isExperienceCenterUser() && auth()->user()->experience_center_id === $experienceCenter->id))
                                             <a href="{{ route('users.edit', $u) }}" class="btn btn-xs btn-outline-secondary btn-sm">
                                                 <i class="bi bi-pencil"></i> Edit
                                             </a>
                                             
                                             <form action="{{ route('experience-centers.remove-staff', [$experienceCenter, $u]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this staff user?');">
                                                 @csrf
                                                 @method('DELETE')
                                                 <button type="submit" class="btn btn-xs btn-outline-danger btn-sm">
                                                     <i class="bi bi-trash"></i> Delete
                                                 </button>
                                             </form>
                                             @endif
                                         </div>
                                     </td>
                                     @endif
                                 </tr>
                                 @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No staff/users linked to this center.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->isSuperAdmin() || auth()->user()->isExperienceCenterUser())
<!-- Add Staff User Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('experience-centers.add-staff', $experienceCenter) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addStaffModalLabel">Add Staff User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="staff_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="staff_name" class="form-control" required placeholder="e.g. John Doe">
                    </div>
                    <div class="mb-3">
                        <label for="staff_email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="staff_email" class="form-control" required placeholder="e.g. john@y5home.com">
                    </div>
                    <div class="mb-3">
                        <label for="staff_mobile" class="form-label">Mobile Number</label>
                        <input type="text" name="mobile" id="staff_mobile" class="form-control" placeholder="e.g. 9876543210">
                    </div>
                    <div class="mb-3">
                        <label for="staff_role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" id="staff_role" class="form-select" required>
                            <option value="sales-executive" selected>Sales Executive</option>
                            <option value="experience-center">Experience Center User</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="staff_password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="staff_password" class="form-control" required minlength="8" placeholder="At least 8 characters">
                    </div>
                    <div class="mb-3">
                        <label for="staff_password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" id="staff_password_confirmation" class="form-control" required minlength="8" placeholder="Confirm password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Staff</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
