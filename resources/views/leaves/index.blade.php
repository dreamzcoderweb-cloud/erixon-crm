@extends('layouts.master')
@section('title', 'Leave & Salary Management - CRM')
@section('content')
    <style>
        /* Force salary report table container to allow horizontal scrolling without overflowing card */
        .salary-report-responsive {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            max-width: 100% !important;
            width: 100% !important;
            display: block;
        }

        #salary-report-table {
            width: 100% !important;
            min-width: 1100px; /* Ensure 9 columns have generous width without overlapping */
            margin: 0 !important;
            border-collapse: collapse !important;
        }

        #salary-report-table th, 
        #salary-report-table td {
            vertical-align: middle !important;
            white-space: nowrap !important;
            padding: 0.75rem 0.85rem !important;
        }

        #salary-report-table th {
            background-color: #f8f9fa !important;
            color: #566a7f;
            font-weight: 600;
            font-size: 0.825rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        #salary-report-table_wrapper .dt-layout-row {
            align-items: center;
            margin-bottom: 0.75rem;
        }

        #salary-report-table_wrapper .dt-search input {
            border: 1px solid #d9dee3;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            margin-left: 0.5rem;
            outline: none;
        }

        #salary-report-table_wrapper .dt-search input:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.15);
        }

        #salary-report-table_wrapper .dt-layout-start {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        #salary-report-table_wrapper .dt-layout-end {
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div id="alert-container"></div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0"><i class="bx bx-calendar-event me-2"></i> Leave & Salary Management</h4>
            <div class="d-flex gap-2">
                @can('leaves.create')
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#requestLeaveModal">
                        <i class="bx bx-plus me-1"></i> Submit Leave Request
                    </button>
                @endcan
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="nav-align-top mb-4">
            <ul class="nav nav-tabs nav-fill" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-leave-requests" aria-controls="navs-leave-requests" aria-selected="true">
                        <i class="bx bx-list-check me-1"></i> Leave Requests
                    </button>
                </li>
                @can('salary.view')
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-salary-report" aria-controls="navs-salary-report" aria-selected="false">
                            <i class="bx bx-calculator me-1"></i> Monthly Salary & Leave Deduction
                        </button>
                    </li>
                @endcan
            </ul>

            <div class="tab-content border-0 p-0 pt-3">
                <!-- TAB 1: Leave Requests List -->
                <div class="tab-pane fade show active" id="navs-leave-requests" role="tabpanel">
                    <div class="card">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5 class="card-title m-0"><i class="bx bx-history me-1"></i> Leave Applications</h5>
                            <div class="d-flex gap-2">
                                @if(auth()->user()->isSuperAdmin())
                                    <select id="filter_leave_user_id" class="form-select form-select-sm" style="width: 200px;">
                                        <option value="">-- All Staff Members --</option>
                                        @foreach ($staffs as $staff)
                                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <select id="filter_leave_user_id" class="form-select form-select-sm" style="width: 200px;">
                                        <option value="{{ auth()->user()->id }}">{{ auth()->user()->name }}</option>
                                    </select>
                                @endif
                                <select id="filter_leave_status" class="form-select form-select-sm" style="width: 150px;">
                                    <option value="">-- All Status --</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                        <div class="table-responsive text-nowrap p-3">
                            <table id="leaves-table" class="table table-hover align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Staff Name</th>
                                        <th>Leave Type</th>
                                        <th>Dates</th>
                                        <th>Days</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- AJAX DataTable -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: Monthly Salary & Leave Deduction -->
                @can('salary.view')
                    <div class="tab-pane fade" id="navs-salary-report" role="tabpanel">
                        <!-- Filter Card -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <form id="salaryReportFilterForm" class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Select Salary Month</label>
                                        <input type="month" id="salary_month" name="month" class="form-control"
                                            value="{{ date('Y-m') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="bx bx-filter-alt me-1"></i> Calculate Deduction
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- KPI Summary Row -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="card border-start border-primary border-4 shadow-sm">
                                    <div class="card-body p-3 text-center">
                                        <small class="text-muted text-uppercase fw-semibold d-block">Target Month</small>
                                        <h4 class="mb-0 text-primary fw-bold mt-1" id="kpi_month_title">{{ date('F Y') }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-start border-info border-4 shadow-sm">
                                    <div class="card-body p-3 text-center">
                                        <small class="text-muted text-uppercase fw-semibold d-block">Working Days</small>
                                        <h4 class="mb-0 text-info fw-bold mt-1" id="kpi_working_days">-- days</h4>
                                        <small class="text-muted" id="kpi_days_breakdown">(Calculating...)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-start border-warning border-4 shadow-sm">
                                    <div class="card-body p-3 text-center">
                                        <small class="text-muted text-uppercase fw-semibold d-block">Excess Leave Deductions</small>
                                        <h4 class="mb-0 text-warning fw-bold mt-1" id="kpi_total_deductions">₹0.00</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-start border-success border-4 shadow-sm">
                                    <div class="card-body p-3 text-center">
                                        <small class="text-muted text-uppercase fw-semibold d-block">Total Net Payable</small>
                                        <h4 class="mb-0 text-success fw-bold mt-1" id="kpi_total_net_salary">₹0.00</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Table Card -->
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h5 class="card-title m-0"><i class="bx bx-detail me-1"></i> Staff Salary & Excess Leave Deduction</h5>
                            </div>
                            <div class="salary-report-responsive p-3">
                                <table id="salary-report-table" class="table table-bordered table-striped table-hover align-middle w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-start align-middle text-nowrap" style="min-width: 200px;">Staff Name</th>
                                            <th class="text-center align-middle text-nowrap" style="min-width: 130px;">Designation</th>
                                            <th class="text-center align-middle text-nowrap" style="min-width: 130px;">Base Salary</th>
                                            <th class="text-center align-middle text-nowrap" style="min-width: 140px;">Allowed Leaves</th>
                                            <th class="text-center align-middle text-nowrap" style="min-width: 170px;">Approved Leaves Taken</th>
                                            <th class="text-center align-middle text-nowrap" style="min-width: 160px;">Excess Leave Days</th>
                                            <th class="text-center align-middle text-nowrap" style="min-width: 160px;">Per-Day Salary Rate</th>
                                            <th class="text-center align-middle text-nowrap" style="min-width: 150px;">Salary Deduction</th>
                                            <th class="text-center align-middle text-nowrap" style="min-width: 140px;">Net Salary</th>
                                        </tr>
                                    </thead>
                                    <tbody id="salaryReportTbody">
                                        <!-- Loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    </div>

    <!-- Modal: Request Leave -->
    <div class="modal fade" id="requestLeaveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="requestLeaveForm" method="POST" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-plus-circle me-1"></i> Submit Leave Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            @if(auth()->user()->isSuperAdmin())
                                <div class="col-md-12">
                                    <label class="form-label">Select Staff Member <span class="text-danger">*</span></label>
                                    <select name="user_id" class="form-select">
                                        <option value="">-- Apply for Myself (Logged in Staff) --</option>
                                        @foreach ($staffs as $staff)
                                            <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            @endif

                            <div class="col-md-6">
                                <label class="form-label">From Date <span class="text-danger">*</span></label>
                                <input type="date" name="from_date" id="leave_from_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">To Date <span class="text-danger">*</span></label>
                                <input type="date" name="to_date" id="leave_to_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Number of Leave Days <span class="text-danger">*</span></label>
                                <input type="number" step="0.5" min="0.5" name="number_of_days" id="leave_number_of_days" class="form-control" value="1" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Leave Type <span class="text-danger">*</span></label>
                                <select name="leave_type" class="form-select" required>
                                    <option value="Casual Leave">Casual Leave</option>
                                    <option value="Sick Leave">Sick Leave</option>
                                    <option value="Earned Leave">Earned Leave</option>
                                    <option value="Paid Leave">Paid Leave</option>
                                    <option value="Unpaid Leave">Unpaid Leave</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Reason</label>
                                <textarea name="reason" class="form-control" rows="3" placeholder="Specify reason for leave..."></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="submitLeaveBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Approve / Reject Leave -->
    <div class="modal fade" id="actionLeaveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="actionLeaveForm" method="POST">
                    @csrf
                    <input type="hidden" name="leave_id" id="action_leave_id">
                    <input type="hidden" name="action_type" id="action_leave_type">
                    <div class="modal-header" id="actionModalHeader">
                        <h5 class="modal-title" id="actionModalTitle">Action Leave Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="actionModalDescription">Are you sure you want to proceed?</p>
                        <div class="mb-3">
                            <label class="form-label">Admin Remarks (Optional)</label>
                            <textarea name="admin_remarks" class="form-control" rows="2" placeholder="Add approval/rejection remarks..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" id="confirmActionLeaveBtn">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
