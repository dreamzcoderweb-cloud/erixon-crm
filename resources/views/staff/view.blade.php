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
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Leave Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach ($staffs as $staff)
                            <tr id="staff-row-{{ $staff->id }}">
                                <td>
                                    <strong>{{ $staff->name }}</strong>
                                    @if(!empty($staff->mobile_number))
                                        <br><small class="text-muted"><i class="bx bx-phone me-1"></i>{{ $staff->mobile_number }}</small>
                                    @endif
                                </td>
                                <td>{{ $staff->email }}</td>
                                <td><span class="badge bg-label-info">{{ $staff->roles->first()?->name ?? '-' }}</span></td>
                                <td>
                                    <span class="badge leave-status-badge {{ $staff->is_on_leave ? 'bg-label-danger' : 'bg-label-success' }}">
                                        <i class="bx {{ $staff->is_on_leave ? 'bx-user-x' : 'bx-user-check' }} me-1"></i>
                                        {{ $staff->is_on_leave ? 'On Leave Today' : 'Active (Available)' }}
                                    </span>
                                </td>
                                <td>{{ $staff->created_at?->format('d-m-Y') }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        @can('staff.edit')
                                            <button class="btn btn-sm {{ $staff->is_on_leave ? 'btn-outline-success' : 'btn-outline-warning' }} btn-toggle-leave"
                                                data-id="{{ $staff->id }}"
                                                title="{{ $staff->is_on_leave ? 'Mark as Active' : 'Mark as On Leave' }}">
                                                <i class="bx {{ $staff->is_on_leave ? 'bx-user-check' : 'bx-user-minus' }}"></i>
                                                {{ $staff->is_on_leave ? 'Mark Active' : 'Mark On Leave' }}
                                            </button>
                                        @endcan

                                        @if($staff->is_on_leave)
                                            <button class="btn btn-sm btn-outline-primary btn-view-leave-followups"
                                                data-id="{{ $staff->id }}" data-name="{{ $staff->name }}"
                                                title="View & Reassign Today's Follow-ups">
                                                <i class="bx bx-calendar-event me-1"></i> Reassign Follow-ups
                                            </button>
                                        @endif

                                        @can('staff.edit')
                                            <a class="btn btn-sm btn-outline-primary btn-edit"
                                                href="{{ url('admin/edit_staff/' . $staff->id) }}" title="Edit Staff">
                                                <i class="bx bx-edit-alt"></i>
                                            </a>
                                        @endcan
                                        @can('staff.delete')
                                            <a href="#" class="btn btn-sm btn-outline-danger btn-delete" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal" data-id="{{ $staff->id }}"
                                                data-name="admin/delete_staff" title="Delete Staff">
                                                <i class="bx bx-trash"></i>
                                            </a>
                                        @endcan
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
