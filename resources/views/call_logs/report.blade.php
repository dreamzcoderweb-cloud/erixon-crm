@extends('layouts.master')
@section('title', 'Call Log Report - Super Admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0"><i class="bx bx-bar-chart-alt-2 me-2"></i> Call Log Report</h4>
            <span class="badge bg-label-primary fs-6"><i class="bx bx-shield me-1"></i> Full Access</span>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="btn-group mb-3" role="group">
                    <button type="button" class="btn btn-outline-primary btn-call-log-period active" data-period="daily">Daily</button>
                    <button type="button" class="btn btn-outline-primary btn-call-log-period" data-period="weekly">Weekly</button>
                    <button type="button" class="btn btn-outline-primary btn-call-log-period" data-period="monthly">Monthly</button>
                    <button type="button" class="btn btn-outline-primary btn-call-log-period" data-period="custom">Custom</button>
                </div>

                <form id="callLogReportFilterForm">
                    <input type="hidden" name="filter_type" id="call_log_filter_type" value="daily">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Staff</label>
                            <select name="user_id" id="call_log_filter_user_id" class="form-select">
                                <option value="">-- All Staff --</option>
                                @foreach ($staffs as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Lead</label>
                            <select name="lead_id" id="call_log_filter_lead_id" class="form-select">
                                <option value="">-- All Leads --</option>
                                @foreach ($leads as $lead)
                                    <option value="{{ $lead->lead_id }}">{{ $lead->lead_title }} ({{ $lead->customer->name ?? 'N/A' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 call-log-filter-input-group" id="call_log_group_daily">
                            <label class="form-label fw-semibold">Date</label>
                            <input type="date" name="date" id="call_log_filter_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3 call-log-filter-input-group d-none" id="call_log_group_monthly">
                            <label class="form-label fw-semibold">Month</label>
                            <input type="month" name="month" id="call_log_filter_month" class="form-control" value="{{ date('Y-m') }}">
                        </div>
                        <div class="col-md-3 call-log-filter-input-group d-none" id="call_log_group_custom_start">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="date" name="start_date" id="call_log_filter_start_date" class="form-control" value="{{ date('Y-m-01') }}">
                        </div>
                        <div class="col-md-3 call-log-filter-input-group d-none" id="call_log_group_custom_end">
                            <label class="form-label fw-semibold">End Date</label>
                            <input type="date" name="end_date" id="call_log_filter_end_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Call Type</label>
                            <select name="call_type" id="call_log_filter_call_type" class="form-select">
                                <option value="">-- All Types --</option>
                                <option value="Inbound">Inbound</option>
                                <option value="Outbound">Outbound</option>
                                <option value="Missed">Missed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="call_status" id="call_log_filter_call_status" class="form-select">
                                <option value="">-- All Statuses --</option>
                                <option value="Completed">Completed</option>
                                <option value="Missed">Missed</option>
                                <option value="No Answer">No Answer</option>
                                <option value="Busy">Busy</option>
                                <option value="Failed">Failed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <i class="bx bx-filter-alt me-1"></i> Apply Filter
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="resetCallLogReportFilterBtn" title="Reset Filters">
                                    <i class="bx bx-refresh"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card shadow-sm border-start border-primary border-4">
                    <div class="card-body p-3 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block">Total</small>
                        <h3 class="mb-0 text-primary fw-bold mt-1" id="call_log_kpi_total">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card shadow-sm border-start border-success border-4">
                    <div class="card-body p-3 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block">Inbound</small>
                        <h3 class="mb-0 text-success fw-bold mt-1" id="call_log_kpi_inbound">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card shadow-sm border-start border-info border-4">
                    <div class="card-body p-3 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block">Outbound</small>
                        <h3 class="mb-0 text-info fw-bold mt-1" id="call_log_kpi_outbound">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card shadow-sm border-start border-danger border-4">
                    <div class="card-body p-3 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block">Missed</small>
                        <h3 class="mb-0 text-danger fw-bold mt-1" id="call_log_kpi_missed">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card shadow-sm border-start border-secondary border-4">
                    <div class="card-body p-3 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block">Completed</small>
                        <h3 class="mb-0 text-secondary fw-bold mt-1" id="call_log_kpi_completed">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card shadow-sm border-start border-dark border-4">
                    <div class="card-body p-3 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block">Recorded</small>
                        <h3 class="mb-0 text-dark fw-bold mt-1" id="call_log_kpi_recorded">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title m-0"><i class="bx bx-table me-1"></i> Call Logs</h5>
                <small class="text-muted" id="call_log_report_period_label">Daily Call Log Report</small>
            </div>
            <div class="table-responsive text-nowrap p-3">
                <table id="call-log-report-table" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Lead</th>
                            <th>Staff</th>
                            <th>Phone</th>
                            <th>Type</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Recording</th>
                            <th>Call Date</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
