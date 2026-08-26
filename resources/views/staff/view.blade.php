@extends('layouts.master')
@section('title', 'Staff - Super Admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div id="alert-container"></div>
        @if (session('success') || session('danger'))
            <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show mb-4"
                role="alert">
                <strong>{{ session('success') ? session('success') : session('danger') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="card-header p-0 m-0"><i class="bx bx-user-check me-2"></i>Staff Management</h5>
                <div class="ms-auto d-flex gap-2">
                    @can('followups.reassign')
                        <button class="btn btn-outline-secondary btn-view-reassignment-history">
                            <i class="bx bx-history me-1"></i> Reassignment Audit Trail
                        </button>
                    @endcan
                    @can('staff.create')
                        <button class="btn btn-primary" onclick="location.href='{{ url('admin/add_staff') }}'">
                            <i class="bx bx-plus me-1"></i> Add Staff
                        </button>
                    @endcan
                </div>
            </div>
            <div class="table-responsive text-nowrap p-3">
                <table id="staff-table" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>Staff Info</th>
                            <th>Designation & Type</th>
                            <th>Timings & Allowed In</th>
                            <th>Salary & Leaves</th>
                            <th>Late Limit & Increments</th>
                            <th>Leave Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach ($staffs as $staff)
                            <tr id="staff-row-{{ $staff->id }}">
                                <td>
                                    <strong>{{ $staff->name }}</strong>
                                    <br><small class="text-muted"><i class="bx bx-envelope me-1"></i>{{ $staff->email }}</small>
                                    @if(!empty($staff->mobile_number))
                                        <br><small class="text-muted"><i class="bx bx-phone me-1"></i>{{ $staff->mobile_number }}</small>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $staff->designation ?? 'N/A' }}</strong>
                                    <br><span class="badge bg-label-info me-1">{{ $staff->roles->first()?->name ?? '-' }}</span>
                                    <span class="badge {{ ($staff->staff_type ?? 'Permanent') === 'Temporary' ? 'bg-label-warning' : 'bg-label-success' }}">{{ $staff->staff_type ?? 'Permanent' }}</span>
                                </td>
                                <td>
                                    @if($staff->check_in_time || $staff->check_out_time)
                                        <span class="badge bg-label-primary">
                                            <i class="bx bx-time me-1"></i>
                                            {{ $staff->check_in_time ? \Carbon\Carbon::parse($staff->check_in_time)->format('h:i A') : '--:--' }} - 
                                            {{ $staff->check_out_time ? \Carbon\Carbon::parse($staff->check_out_time)->format('h:i A') : '--:--' }}
                                        </span>
                                    @else
                                        <span class="text-muted d-block">Not Set</span>
                                    @endif
                                    @if($staff->allow_check_in_time)
                                        <small class="text-muted d-block mt-1"><i class="bx bx-log-in-circle me-1"></i>Allow In: <strong>{{ \Carbon\Carbon::parse($staff->allow_check_in_time)->format('h:i A') }}</strong></small>
                                    @endif
                                </td>
                                <td>
                                    <div><strong>₹{{ number_format($staff->base_salary ?? 0, 2) }}</strong></div>
                                    @if(($staff->staff_type ?? 'Permanent') !== 'Temporary')
                                        <small class="text-muted">Leaves: <strong>{{ $staff->available_leave_count ?? 0 }} day(s)</strong></small>
                                    @else
                                        <small class="text-muted">Leaves: <em>N/A (Temporary)</em></small>
                                    @endif
                                </td>
                                <td>
                                    <small class="d-block">Allowed Late: <strong>{{ $staff->late_attendance_count ?? 0 }} count(s)</strong></small>
                                    @if($staff->increment_amount > 0)
                                        <small class="text-success d-block"><i class="bx bx-trending-up me-1"></i>+₹{{ number_format($staff->increment_amount, 2) }}
                                        @if($staff->increment_date)
                                            ({{ \Carbon\Carbon::parse($staff->increment_date)->format('d-m-Y') }})
                                        @endif
                                        </small>
                                    @else
                                        <small class="text-muted">No Increment</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge leave-status-badge {{ $staff->is_on_leave ? 'bg-label-danger' : 'bg-label-success' }}">
                                        <i class="bx {{ $staff->is_on_leave ? 'bx-user-x' : 'bx-user-check' }} me-1"></i>
                                        {{ $staff->is_on_leave ? 'On Leave Today' : 'Active (Available)' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            @can('staff.edit')
                                                <a class="dropdown-item btn-toggle-leave" href="javascript:void(0);" data-id="{{ $staff->id }}">
                                                    <i class="bx {{ $staff->is_on_leave ? 'bx-user-check' : 'bx-user-minus' }} me-1"></i>
                                                    {{ $staff->is_on_leave ? 'Mark Active' : 'Mark On Leave' }}
                                                </a>
                                            @endcan
                                            @if($staff->is_on_leave)
                                                <a class="dropdown-item btn-view-leave-followups" href="javascript:void(0);" data-id="{{ $staff->id }}" data-name="{{ $staff->name }}">
                                                    <i class="bx bx-calendar-event me-1"></i> Reassign Follow-ups
                                                </a>
                                            @endif
                                            @can('staff.edit')
                                                <a class="dropdown-item btn-edit" href="{{ url('admin/edit_staff/' . $staff->id) }}">
                                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                                </a>
                                            @endcan
                                            @can('staff.delete')
                                                <a class="dropdown-item text-danger btn-delete" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="{{ $staff->id }}" data-name="admin/delete_staff">
                                                    <i class="bx bx-trash me-1"></i> Delete
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal: Reassign Today's Follow-ups for Staff on Leave -->
    <div class="modal fade" id="leaveStaffFollowupsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-label-warning text-dark">
                    <h5 class="modal-title"><i class="bx bx-user-x me-1"></i> Today's Follow-ups for Staff on Leave: <span id="leaveStaffNameTitle"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Below are the pending client follow-ups scheduled for today assigned to this staff member. Select an available staff member to reassign each follow-up.</p>
                    
                    <div id="leaveStaffFollowupsContainer" class="table-responsive text-nowrap">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Client / Lead</th>
                                    <th>Contact</th>
                                    <th>Follow-up Type</th>
                                    <th>Next Date & Time</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                    <th>Reassign To Staff</th>
                                </tr>
                            </thead>
                            <tbody id="leaveStaffFollowupsTbody">
                                <!-- Loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Reassignment Audit History -->
    <div class="modal fade" id="reassignmentHistoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-history me-1"></i> Reassignment Audit Trail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Client / Lead</th>
                                    <th>Previous Staff</th>
                                    <th>New Staff</th>
                                    <th>Reassigned By</th>
                                    <th>Reassigned Date/Time</th>
                                </tr>
                            </thead>
                            <tbody id="reassignmentHistoryTbody">
                                <!-- Loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
