@extends('layouts.master')
@section('title', 'Attendance Report - Super Admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0"><i class="bx bx-bar-chart-alt-2 me-2"></i> Attendance Report</h4>
            <div>
                <span class="badge bg-label-primary fs-6"><i class="bx bx-shield me-1"></i> Full Access</span>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="attendanceReportFilterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Select Staff</label>
                            <select name="user_id" id="filter_user_id" class="form-select">
                                <option value="">-- All Staff Members --</option>
                                @foreach ($staffs as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter inputs per type -->
                        <div class="col-md-3 filter-input-group" id="group_daily">
                            <label class="form-label fw-semibold">Select Date</label>
                            <input type="date" name="date" id="filter_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-3 filter-input-group d-none" id="group_monthly">
                            <label class="form-label fw-semibold">Select Month</label>
                            <input type="month" name="month" id="filter_month" class="form-control" value="{{ date('Y-m') }}">
                        </div>

                        <div class="col-md-3 filter-input-group d-none" id="group_custom_start">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="date" name="start_date" id="filter_start_date" class="form-control" value="{{ date('Y-m-01') }}">
                        </div>

                        <div class="col-md-3 filter-input-group d-none" id="group_custom_end">
                            <label class="form-label fw-semibold">End Date</label>
                            <input type="date" name="end_date" id="filter_end_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1" id="applyReportFilterBtn">
                                    <i class="bx bx-filter-alt me-1"></i> Apply Filter
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="resetReportFilterBtn" title="Reset Filters">
                                    <i class="bx bx-refresh"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Analytics KPI Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card shadow-sm border-start border-primary border-4">
                    <div class="card-body p-3 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block">Present</small>
                        <h3 class="mb-0 text-success fw-bold mt-1" id="kpi_present">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card shadow-sm border-start border-warning border-4">
                    <div class="card-body p-3 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block">Late</small>
                        <h3 class="mb-0 text-warning fw-bold mt-1" id="kpi_late">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card shadow-sm border-start border-info border-4">
                    <div class="card-body p-3 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block">Half Day</small>
                        <h3 class="mb-0 text-info fw-bold mt-1" id="kpi_half_day">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card shadow-sm border-start border-danger border-4">
                    <div class="card-body p-3 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block">Absent</small>
                        <h3 class="mb-0 text-danger fw-bold mt-1" id="kpi_absent">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card shadow-sm border-start border-secondary border-4">
                    <div class="card-body p-3 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block">On Leave</small>
                        <h3 class="mb-0 text-secondary fw-bold mt-1" id="kpi_on_leave">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card shadow-sm border-start border-dark border-4">
                    <div class="card-body p-3 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block">Total Hours</small>
                        <h5 class="mb-0 text-dark fw-bold mt-2" id="kpi_total_hours">0 hrs</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title m-0"><i class="bx bx-table me-1"></i> Attendance Logs</h5>
                <small class="text-muted" id="report_period_label">Showing Today's Report</small>
            </div>
            <div class="table-responsive text-nowrap p-3">
                <table id="attendance-report-table" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Staff Member</th>
                            <th>Date</th>
                            <th>Check-In</th>
                            <th>Check-Out</th>
                            <th>Working Hours</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data loaded dynamically via DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
