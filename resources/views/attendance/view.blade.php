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

        <!-- Analytics KPI Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card shadow-sm border-start border-primary border-4 kpi-card-clickable" id="kpi_card_total_attendance" style="cursor: pointer;" title="Click to reset filters">
                    <div class="card-body p-3 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block">Attendance Count</small>
                        <h3 class="mb-0 text-primary fw-bold mt-1" id="kpi_total_attendance">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card shadow-sm border-start border-success border-4 kpi-card-clickable" id="kpi_card_present_count" style="cursor: pointer;" title="Click to view present attendance">
                    <div class="card-body p-3 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block">Present Count</small>
                        <h3 class="mb-0 text-success fw-bold mt-1" id="kpi_present_count">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card shadow-sm border-start border-warning border-4 kpi-card-clickable" id="kpi_card_staff_count" style="cursor: pointer;" title="Click to view staff count">
                    <div class="card-body p-3 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block">Staff Count</small>
                        <h3 class="mb-0 text-warning fw-bold mt-1" id="kpi_staff_count">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="card-header p-0 m-0"><i class="bx bx-calendar-check me-2"></i>Attendance Management</h5>
                @can('attendance.create')
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAttendanceModal">
                        <i class="bx bx-plus me-1"></i> Add Attendance
                    </button>
                @endcan
            </div>

            <!-- Attendance Filter Bar -->
            <div class="p-3 bg-light border-bottom">
                <form id="attendanceFilterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12">
                            <label class="form-label fw-semibold d-block">Date Period</label>
                            <div class="btn-group btn-group-sm" role="group" id="attendancePeriodBtnGroup">
                                <button type="button" class="btn btn-outline-primary btn-attendance-period active" data-period="all">All Time</button>
                                <button type="button" class="btn btn-outline-primary btn-attendance-period" data-period="daily">Daily</button>
                                <button type="button" class="btn btn-outline-primary btn-attendance-period" data-period="weekly">Weekly</button>
                                <button type="button" class="btn btn-outline-primary btn-attendance-period" data-period="monthly">Monthly</button>
                                <button type="button" class="btn btn-outline-primary btn-attendance-period" data-period="custom">Custom</button>
                            </div>
                            <input type="hidden" name="filter_type" id="attendance_filter_period" value="all">
                        </div>

                        <div class="col-md-3 attendance-filter-date-group d-none" id="attendance_group_daily">
                            <label class="form-label fw-semibold">Date</label>
                            <input type="date" name="date" id="attendance_filter_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-3 attendance-filter-date-group d-none" id="attendance_group_monthly">
                            <label class="form-label fw-semibold">Month</label>
                            <input type="month" name="month" id="attendance_filter_month" class="form-control form-control-sm" value="{{ date('Y-m') }}">
                        </div>

                        <div class="col-md-3 attendance-filter-date-group d-none" id="attendance_group_custom_start">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="date" name="start_date" id="attendance_filter_start_date" class="form-control form-control-sm" value="{{ date('Y-m-01') }}">
                        </div>

                        <div class="col-md-3 attendance-filter-date-group d-none" id="attendance_group_custom_end">
                            <label class="form-label fw-semibold">End Date</label>
                            <input type="date" name="end_date" id="attendance_filter_end_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Staff</label>
                            <select name="user_id" id="attendance_filter_user_id" class="form-select form-select-sm">
                                <option value="">-- All Staff --</option>
                                @if(isset($staffs) && count($staffs) > 0)
                                    @foreach ($staffs as $stf)
                                        <option value="{{ $stf->id }}">{{ $stf->name }} ({{ $stf->email }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" id="attendance_filter_status" class="form-select form-select-sm">
                                <option value="">-- All Statuses --</option>
                                <option value="Present">Present</option>
                                <option value="Late">Late</option>
                                <option value="Half Day">Half Day</option>
                                <option value="Absent">Absent</option>
                                <option value="On Leave">On Leave</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Check-In Time</label>
                            <input type="time" name="check_in_time" id="attendance_filter_check_in_time" class="form-control form-control-sm" title="Filter by Check-In Time">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Check-Out Time</label>
                            <input type="time" name="check_out_time" id="attendance_filter_check_out_time" class="form-control form-control-sm" title="Filter by Check-Out Time">
                        </div>

                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="bx bx-filter-alt me-1"></i> Apply Filter
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="resetAttendanceFilterBtn" title="Reset Filters">
                                    <i class="bx bx-refresh me-1"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive text-nowrap p-3">
                <table id="attendance-table" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Staff Name</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Check-In</th>
                            <th class="text-center">Check-Out</th>
                            <th class="text-center">Working Hours</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
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
