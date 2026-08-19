@extends('layouts.master')
@section('title', 'Attendance - Super Admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div id="alert-container"></div>
        @php
        $user = auth()->user();
        $isSuperAdmin = $user->hasAnyRole(['super admin', 'super-admin','Super Admin']);
        @endphp
        @if(!$isSuperAdmin)
        <!-- Quick Attendance Widget for Logged In User -->
        <div class="card mb-4 bg-label-primary border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="mb-1 text-primary"><i class="bx bx-time-five me-1"></i> Today's Attendance Quick Action</h5>
                    <small class="text-muted">
                        Assigned Reference Shift:
                        <strong>
                            {{ auth()->user()->check_in_time ? \Carbon\Carbon::parse(auth()->user()->check_in_time)->format('h:i A') : '09:00 AM' }} -
                            {{ auth()->user()->check_out_time ? \Carbon\Carbon::parse(auth()->user()->check_out_time)->format('h:i A') : '06:00 PM' }}
                        </strong>
                    </small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if(empty($myTodayAttendance))
                        <button class="btn btn-success btn-mark-self-attendance" data-type="check_in">
                            <i class="bx bx-log-in me-1"></i> Check In Now
                        </button>
                    @elseif(empty($myTodayAttendance->check_out))
                        <span class="badge bg-success fs-6 me-2">Checked In: {{ \Carbon\Carbon::parse($myTodayAttendance->check_in)->format('h:i A') }} ({{ $myTodayAttendance->status }})</span>
                        <button class="btn btn-danger btn-mark-self-attendance" data-type="check_out">
                            <i class="bx bx-log-out me-1"></i> Check Out Now
                        </button>
                    @else
                        <span class="badge bg-primary fs-6"><i class="bx bx-check-circle me-1"></i> Completed Today ({{ $myTodayAttendance->check_in }} - {{ $myTodayAttendance->check_out }})</span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="card-header p-0 m-0"><i class="bx bx-calendar-check me-2"></i>Attendance Management</h5>
                @can('attendance.create')
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAttendanceModal">
                        <i class="bx bx-plus me-1"></i> Add Attendance
                    </button>
                @endcan
            </div>
            <div class="table-responsive text-nowrap p-3">
                <table id="attendance-table" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Staff Name</th>
                            <th>Date</th>
                            <th>Check-In</th>
                            <th>Check-Out</th>
                            <th>Working Hours</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loaded via AJAX DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Attendance Modal -->
    <div class="modal fade" id="addAttendanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="addAttendanceForm" method="POST" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-plus-circle me-1"></i> Add Attendance Record</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Select Staff <span class="text-danger">*</span></label>
                                <select name="user_id" id="add_attendance_user_id" class="form-select" required>
                                    <option value="">-- Select Staff Member --</option>
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}"
                                            data-check-in="{{ $staff->check_in_time ? \Carbon\Carbon::parse($staff->check_in_time)->format('H:i') : '' }}"
                                            data-check-out="{{ $staff->check_out_time ? \Carbon\Carbon::parse($staff->check_out_time)->format('H:i') : '' }}">
                                            {{ $staff->name }} (Shift: {{ $staff->check_in_time ? \Carbon\Carbon::parse($staff->check_in_time)->format('h:i A') : '09:00 AM' }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Check-In Time <span class="text-danger">*</span></label>
                                <input type="time" name="check_in" id="add_attendance_check_in" class="form-control" required>
                                <small class="text-muted" id="add_ref_check_in_label">Assigned Shift Check-In: --:--</small>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Check-Out Time</label>
                                <input type="time" name="check_out" id="add_attendance_check_out" class="form-control">
                                <small class="text-muted" id="add_ref_check_out_label">Assigned Shift Check-Out: --:--</small>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Attendance Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="Auto">Auto (Compare with Assigned Reference Timings)</option>
                                    <option value="Present">Present</option>
                                    <option value="Late">Late</option>
                                    <option value="Half Day">Half Day</option>
                                    <option value="Absent">Absent</option>
                                    <option value="On Leave">On Leave</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="addAttendanceSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Save Attendance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Attendance Modal -->
    <div class="modal fade" id="editAttendanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="editAttendanceForm" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="attendance_id" id="edit_attendance_id">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-edit me-1"></i> Edit Attendance Record</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Select Staff <span class="text-danger">*</span></label>
                                <select name="user_id" id="edit_attendance_user_id" class="form-select" required>
                                    <option value="">-- Select Staff Member --</option>
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" id="edit_attendance_date" class="form-control" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Check-In Time <span class="text-danger">*</span></label>
                                <input type="time" name="check_in" id="edit_attendance_check_in" class="form-control" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Check-Out Time</label>
                                <input type="time" name="check_out" id="edit_attendance_check_out" class="form-control">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Attendance Status <span class="text-danger">*</span></label>
                                <select name="status" id="edit_attendance_status" class="form-select" required>
                                    <option value="Present">Present</option>
                                    <option value="Late">Late</option>
                                    <option value="Half Day">Half Day</option>
                                    <option value="Absent">Absent</option>
                                    <option value="On Leave">On Leave</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="editAttendanceSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Update Attendance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Attendance Modal -->
    <div class="modal fade" id="deleteAttendanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-trash me-1 text-danger"></i> Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this attendance record?</p>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteAttendanceBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection
