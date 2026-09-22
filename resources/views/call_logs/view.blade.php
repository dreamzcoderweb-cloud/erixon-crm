@extends('layouts.master')
@section('title', 'Call Logs - Super Admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div id="alert-container"></div>

        <div class="card">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="card-header p-0 m-0"><i class="bx bx-phone-call me-2"></i>Call Logs</h5>
                @can('call-logs.create')
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCallLogModal">
                        <i class="bx bx-plus me-1"></i> Add Call Log
                    </button>
                @endcan
            </div>

            <!-- Call Logs Filter Bar -->
            <div class="p-3 bg-light border-bottom">
                <form id="callLogListFilterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12">
                            <label class="form-label fw-semibold d-block">Date Period</label>
                            <div class="btn-group btn-group-sm flex-wrap" role="group" id="callLogListPeriodBtnGroup">
                                <button type="button" class="btn btn-outline-primary btn-call-log-list-period active" data-period="all">All Time</button>
                                <button type="button" class="btn btn-outline-primary btn-call-log-list-period" data-period="daily">Daily</button>
                                <button type="button" class="btn btn-outline-primary btn-call-log-list-period" data-period="weekly">Weekly</button>
                                <button type="button" class="btn btn-outline-primary btn-call-log-list-period" data-period="monthly">Monthly</button>
                                <button type="button" class="btn btn-outline-primary btn-call-log-list-period" data-period="yearly">Yearly</button>
                                <button type="button" class="btn btn-outline-primary btn-call-log-list-period" data-period="custom">Custom</button>
                            </div>
                            <input type="hidden" name="filter_type" id="call_log_list_filter_period" value="all">
                        </div>

                        <!-- Daily Datepicker -->
                        <div class="col-md-3 call-log-list-filter-date-group d-none" id="call_log_list_group_daily">
                            <label class="form-label fw-semibold">Select Date</label>
                            <input type="date" name="date" id="call_log_list_filter_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>

                        <!-- Weekly Datepicker -->
                        <div class="col-md-3 call-log-list-filter-date-group d-none" id="call_log_list_group_weekly">
                            <label class="form-label fw-semibold">Select Week</label>
                            <input type="week" name="week" id="call_log_list_filter_week" class="form-control form-control-sm" value="{{ date('Y-\WW') }}">
                        </div>

                        <!-- Monthly Datepicker -->
                        <div class="col-md-3 call-log-list-filter-date-group d-none" id="call_log_list_group_monthly">
                            <label class="form-label fw-semibold">Select Month</label>
                            <input type="month" name="month" id="call_log_list_filter_month" class="form-control form-control-sm" value="{{ date('Y-m') }}">
                        </div>

                        <!-- Yearly Datepicker -->
                        <div class="col-md-3 call-log-list-filter-date-group d-none" id="call_log_list_group_yearly">
                            <label class="form-label fw-semibold">Select Year</label>
                            <select name="year" id="call_log_list_filter_year" class="form-select form-select-sm">
                                @php
                                    $curYear = (int)date('Y');
                                @endphp
                                @for ($y = $curYear + 1; $y >= $curYear - 5; $y--)
                                    <option value="{{ $y }}" {{ $y == $curYear ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Custom Datepicker: Start Date -->
                        <div class="col-md-3 call-log-list-filter-date-group d-none" id="call_log_list_group_custom_start">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="date" name="start_date" id="call_log_list_filter_start_date" class="form-control form-control-sm" value="{{ date('Y-m-01') }}">
                        </div>

                        <!-- Custom Datepicker: End Date -->
                        <div class="col-md-3 call-log-list-filter-date-group d-none" id="call_log_list_group_custom_end">
                            <label class="form-label fw-semibold">End Date</label>
                            <input type="date" name="end_date" id="call_log_list_filter_end_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>

                        <!-- All Staff Dropdown -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Staff</label>
                            <select name="user_id" id="call_log_list_filter_user_id" class="form-select form-select-sm">
                                <option value="">-- All Staff --</option>
                                @if(isset($staffs) && count($staffs) > 0)
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Lead Dropdown -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Lead</label>
                            <select name="lead_id" id="call_log_list_filter_lead_id" class="form-select form-select-sm">
                                <option value="">-- All Leads --</option>
                                @if(isset($leads) && count($leads) > 0)
                                    @foreach ($leads as $lead)
                                        <option value="{{ $lead->lead_id }}">{{ $lead->lead_title }} ({{ $lead->customer->name ?? 'N/A' }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Customer Dropdown Searchable (Name or Phone) -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Customer</label>
                            <select name="customer_id" id="call_log_list_filter_customer_id" class="form-select form-select-sm select2-search">
                                <option value="">-- All Customers --</option>
                                @if(isset($customers) && count($customers) > 0)
                                    @foreach ($customers as $cust)
                                        <option value="{{ $cust->customer_id }}">{{ $cust->name }} ({{ $cust->mobile }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="bx bx-filter-alt me-1"></i> Apply Filter
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="resetCallLogListFilterBtn" title="Reset Filters">
                                    <i class="bx bx-refresh me-1"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table-responsive text-nowrap p-3">
                <table id="call-logs-table" class="table table-hover align-middle w-100">
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addCallLogModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="addCallLogForm" method="POST" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-phone-call me-1"></i> Add Call Log</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @include('call_logs.partials.form-fields', ['prefix' => 'add'])
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="addCallLogSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Save Call Log
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editCallLogModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="editCallLogForm" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="call_id" id="edit_call_log_id">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-edit me-1"></i> Edit Call Log</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @include('call_logs.partials.form-fields', ['prefix' => 'edit'])
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="editCallLogSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Update Call Log
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteCallLogModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-trash me-1 text-danger"></i> Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this call log?</p>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteCallLogBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection
