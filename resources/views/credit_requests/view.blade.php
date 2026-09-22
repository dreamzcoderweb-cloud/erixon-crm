@extends('layouts.master')
@section('title', 'Credit Requests')
@section('content')
    <script>
        window.visibleCreditRequestColumns = @json($visibleColumns ?? []);
    </script>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div id="alert-container"></div>

        <div class="card">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom flex-wrap gap-2">
                <h5 class="card-header p-0 m-0"><i class="bx bx-credit-card me-2"></i>Credit Requests</h5>
                <div class="d-flex gap-2">
                    @can('credit-requests.create')
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCreditRequestModal">
                            <i class="bx bx-plus me-1"></i> New Credit Request
                        </button>
                    @endcan
                </div>
            </div>

            <!-- Credit Request Filter Bar -->
            <div class="p-3 bg-light border-bottom">
                <form id="creditFilterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12">
                            <label class="form-label fw-semibold d-block">Date Period</label>
                            <div class="btn-group btn-group-sm flex-wrap" role="group" id="creditPeriodBtnGroup">
                                <button type="button" class="btn btn-outline-primary btn-credit-period active" data-period="all">All Time</button>
                                <button type="button" class="btn btn-outline-primary btn-credit-period" data-period="daily">Daily</button>
                                <button type="button" class="btn btn-outline-primary btn-credit-period" data-period="weekly">Weekly</button>
                                <button type="button" class="btn btn-outline-primary btn-credit-period" data-period="monthly">Monthly</button>
                                <button type="button" class="btn btn-outline-primary btn-credit-period" data-period="yearly">Yearly</button>
                                <button type="button" class="btn btn-outline-primary btn-credit-period" data-period="custom">Custom</button>
                            </div>
                            <input type="hidden" name="filter_type" id="credit_filter_period" value="all">
                        </div>

                        <!-- Daily Datepicker -->
                        <div class="col-md-3 credit-filter-date-group d-none" id="credit_group_daily">
                            <label class="form-label fw-semibold">Select Date</label>
                            <input type="date" name="date" id="credit_filter_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>

                        <!-- Weekly Datepicker -->
                        <div class="col-md-3 credit-filter-date-group d-none" id="credit_group_weekly">
                            <label class="form-label fw-semibold">Select Week</label>
                            <input type="week" name="week" id="credit_filter_week" class="form-control form-control-sm" value="{{ date('Y-\WW') }}">
                        </div>

                        <!-- Monthly Datepicker -->
                        <div class="col-md-3 credit-filter-date-group d-none" id="credit_group_monthly">
                            <label class="form-label fw-semibold">Select Month</label>
                            <input type="month" name="month" id="credit_filter_month" class="form-control form-control-sm" value="{{ date('Y-m') }}">
                        </div>

                        <!-- Yearly Datepicker -->
                        <div class="col-md-3 credit-filter-date-group d-none" id="credit_group_yearly">
                            <label class="form-label fw-semibold">Select Year</label>
                            <select name="year" id="credit_filter_year" class="form-select form-select-sm">
                                @php
                                    $curYear = (int)date('Y');
                                @endphp
                                @for ($y = $curYear + 1; $y >= $curYear - 5; $y--)
                                    <option value="{{ $y }}" {{ $y == $curYear ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Custom Datepicker: Start Date -->
                        <div class="col-md-3 credit-filter-date-group d-none" id="credit_group_custom_start">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="date" name="start_date" id="credit_filter_start_date" class="form-control form-control-sm" value="{{ date('Y-m-01') }}">
                        </div>

                        <!-- Custom Datepicker: End Date -->
                        <div class="col-md-3 credit-filter-date-group d-none" id="credit_group_custom_end">
                            <label class="form-label fw-semibold">End Date</label>
                            <input type="date" name="end_date" id="credit_filter_end_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>

                        <!-- Lead Requirement Dropdown Filter -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Lead Requirement</label>
                            <select name="lead_requirement_id" id="credit_filter_lead_requirement_id" class="form-select form-select-sm">
                                <option value="">-- All Lead Requirements --</option>
                                @if(isset($leadRequirements) && count($leadRequirements) > 0)
                                    @foreach ($leadRequirements as $req)
                                        <option value="{{ $req->lead_requirements_id }}">{{ $req->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" id="statusFilter" class="form-select form-select-sm">
                                <option value="">-- All Statuses --</option>
                                <option value="Pending Admin Approval">Pending Admin Approval</option>
                                <option value="Forwarded to Product Manager">Forwarded to Product Manager</option>
                                <option value="Credit Added">Credit Added</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="bx bx-filter-alt me-1"></i> Apply Filter
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="resetCreditFilterBtn" title="Reset Filters">
                                    <i class="bx bx-refresh me-1"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table-responsive text-nowrap p-3">
                <table id="credit-requests-table" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            @if (!empty($visibleColumns) && count($visibleColumns) > 0)
                                @foreach ($visibleColumns as $col)
                                    <th>{{ $col['label'] }}</th>
                                @endforeach
                            @else
                                <th>Customer / User</th>
                                <th>Phone / Email</th>
                                <th>Credit Amount</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Requested By</th>
                                <th>Date</th>
                            @endif
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

    <!-- Add Credit Request Modal -->
    <div class="modal fade" id="addCreditRequestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="addCreditRequestForm" action="{{ url('admin/credit-requests/store') }}" method="POST" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-plus-circle me-1"></i> Create Credit Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Find Customer (Name / Phone / Email) <span class="text-danger">*</span></label>
                                <select name="customer_id" id="customer_search_select" class="form-select" required style="width: 100%;">
                                    <option value="">-- Select or Search Customer --</option>
                                    @foreach ($customers as $cust)
                                        <option value="{{ $cust->customer_id }}" data-phone="{{ $cust->mobile }}" data-email="{{ $cust->email }}" data-name="{{ $cust->name }}">
                                            {{ $cust->name }} | {{ $cust->mobile }} | {{ $cust->email ?? 'No Email' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lead Sources</label>
                                <select name="lead_source_id" class="form-select">
                                    <option value="">-- Select Lead Source --</option>
                                    @foreach ($leadSources as $leadSource)
                                        <option value="{{ $leadSource->lead_sources_id }}">
                                            {{ $leadSource->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lead Requirements</label>
                                <select name="lead_requirement_id" class="form-select">
                                    <option value="">-- Select Lead Requirement --</option>
                                    @if(isset($leadRequirements) && count($leadRequirements) > 0)
                                        @foreach ($leadRequirements as $leadReq)
                                            <option value="{{ $leadReq->lead_requirements_id }}">
                                                {{ $leadReq->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Credit Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="credit_amount" class="form-control" placeholder="e.g. 5000.00" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label d-block">&nbsp;</label>
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" name="is_estimate" value="1" id="isEstimateCheck">
                                    <label class="form-check-label fw-bold text-primary" for="isEstimateCheck">
                                        <i class="bx bx-calculator me-1"></i> Create as Estimate Credit
                                    </label>
                                </div>
                            </div>

                            @if (isset($customFields) && count($customFields) > 0)
                                @foreach ($customFields as $field)
                                    <div class="col-md-6">
                                        <label class="form-label">{{ $field->field_label }}
                                            @if ($field->is_required === 'Yes') <span class="text-danger">*</span> @endif
                                        </label>
                                        @if ($field->field_type === 'Text')
                                            <input type="text" name="custom_fields[{{ $field->field_name }}]" class="form-control"
                                                @if ($field->is_required === 'Yes') required @endif>
                                        @elseif($field->field_type === 'Number')
                                            <input type="number" step="any" name="custom_fields[{{ $field->field_name }}]" class="form-control"
                                                @if ($field->is_required === 'Yes') required @endif>
                                        @elseif($field->field_type === 'Date')
                                            <input type="date" name="custom_fields[{{ $field->field_name }}]" class="form-control"
                                                @if ($field->is_required === 'Yes') required @endif>
                                        @elseif($field->field_type === 'Textarea')
                                            <textarea name="custom_fields[{{ $field->field_name }}]" class="form-control" rows="2"
                                                @if ($field->is_required === 'Yes') required @endif></textarea>
                                        @elseif($field->field_type === 'Dropdown')
                                            <select name="custom_fields[{{ $field->field_name }}]" class="form-select"
                                                @if ($field->is_required === 'Yes') required @endif>
                                                <option value="">-- Select {{ $field->field_label }} --</option>
                                                @if ($field->field_options)
                                                    @foreach (explode(',', $field->field_options) as $option)
                                                        <option value="{{ trim($option) }}">{{ trim($option) }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        @elseif($field->field_type === 'Checkbox')
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="custom_fields[{{ $field->field_name }}]" value="1" id="cf_add_{{ $field->field_name }}">
                                                <label class="form-check-label" for="cf_add_{{ $field->field_name }}">
                                                    Yes
                                                </label>
                                            </div>
                                        @endif
                                        <div class="invalid-feedback"></div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="addCreditSubmitBtn">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Credit Request Modal -->
    <div class="modal fade" id="editCreditRequestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="editCreditRequestForm" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="credit_request_id" id="edit_credit_request_id">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-edit me-1 text-primary"></i> Edit Credit Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Customer (Name / Phone / Email) <span class="text-danger">*</span></label>
                                <select name="customer_id" id="edit_customer_id" class="form-select" required style="width: 100%;">
                                    <option value="">-- Select Customer --</option>
                                    @foreach ($customers as $cust)
                                        <option value="{{ $cust->customer_id }}">
                                            {{ $cust->name }} | {{ $cust->mobile }} | {{ $cust->email ?? 'No Email' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lead Sources</label>
                                <select name="lead_source_id" id="edit_lead_source_id" class="form-select">
                                    <option value="">-- Select Lead Source --</option>
                                    @foreach ($leadSources as $leadSource)
                                        <option value="{{ $leadSource->lead_sources_id }}">
                                            {{ $leadSource->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lead Requirements</label>
                                <select name="lead_requirement_id" id="edit_lead_requirement_id" class="form-select">
                                    <option value="">-- Select Lead Requirement --</option>
                                    @if(isset($leadRequirements) && count($leadRequirements) > 0)
                                        @foreach ($leadRequirements as $leadReq)
                                            <option value="{{ $leadReq->lead_requirements_id }}">
                                                {{ $leadReq->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Credit Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="credit_amount" id="edit_credit_amount" class="form-control" placeholder="e.g. 5000.00" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label d-block">&nbsp;</label>
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" name="is_estimate" value="1" id="edit_is_estimate">
                                    <label class="form-check-label fw-bold text-primary" for="edit_is_estimate">
                                        <i class="bx bx-calculator me-1"></i> Create as Estimate Credit
                                    </label>
                                </div>
                            </div>

                            @if (isset($customFields) && count($customFields) > 0)
                                @foreach ($customFields as $field)
                                    <div class="col-md-6">
                                        <label class="form-label">{{ $field->field_label }}
                                            @if ($field->is_required === 'Yes') <span class="text-danger">*</span> @endif
                                        </label>
                                        @if ($field->field_type === 'Text')
                                            <input type="text" name="custom_fields[{{ $field->field_name }}]" id="edit_cf_{{ $field->field_name }}" class="form-control"
                                                @if ($field->is_required === 'Yes') required @endif>
                                        @elseif($field->field_type === 'Number')
                                            <input type="number" step="any" name="custom_fields[{{ $field->field_name }}]" id="edit_cf_{{ $field->field_name }}" class="form-control"
                                                @if ($field->is_required === 'Yes') required @endif>
                                        @elseif($field->field_type === 'Date')
                                            <input type="date" name="custom_fields[{{ $field->field_name }}]" id="edit_cf_{{ $field->field_name }}" class="form-control"
                                                @if ($field->is_required === 'Yes') required @endif>
                                        @elseif($field->field_type === 'Textarea')
                                            <textarea name="custom_fields[{{ $field->field_name }}]" id="edit_cf_{{ $field->field_name }}" class="form-control" rows="2"
                                                @if ($field->is_required === 'Yes') required @endif></textarea>
                                        @elseif($field->field_type === 'Dropdown')
                                            <select name="custom_fields[{{ $field->field_name }}]" id="edit_cf_{{ $field->field_name }}" class="form-select"
                                                @if ($field->is_required === 'Yes') required @endif>
                                                <option value="">-- Select {{ $field->field_label }} --</option>
                                                @if ($field->field_options)
                                                    @foreach (explode(',', $field->field_options) as $option)
                                                        <option value="{{ trim($option) }}">{{ trim($option) }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        @elseif($field->field_type === 'Checkbox')
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="custom_fields[{{ $field->field_name }}]" value="1" id="edit_cf_{{ $field->field_name }}">
                                                <label class="form-check-label" for="edit_cf_{{ $field->field_name }}">
                                                    Yes
                                                </label>
                                            </div>
                                        @endif
                                        <div class="invalid-feedback"></div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="editCreditSubmitBtn">Update Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Admin Approval Modal -->
    <div class="modal fade" id="adminApproveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="adminApproveForm" method="POST">
                    @csrf
                    <input type="hidden" id="admin_approve_id">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-check-shield me-1 text-primary"></i> Admin Approval</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Approve credit request and forward to Support Team for final credit addition?</p>
                        <div class="mb-3">
                            <label class="form-label">Admin Remarks (Optional)</label>
                            <textarea name="admin_remarks" class="form-control" rows="2" placeholder="Approved by Admin..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Approve & Forward to Support</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Support Approval Modal -->
    <div class="modal fade" id="supportApproveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="supportApproveForm" method="POST">
                    @csrf
                    <input type="hidden" id="support_approve_id">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-plus-circle me-1 text-success"></i> Support Team Credit Addition</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Add credit balance to customer account?</p>
                        <div class="mb-3">
                            <label class="form-label">Support Remarks (Optional)</label>
                            <textarea name="support_remarks" class="form-control" rows="2" placeholder="Credit added to user balance..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Approve & Add Credit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Credit Request Modal -->
    <div class="modal fade" id="rejectCreditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="rejectCreditForm" method="POST">
                    @csrf
                    <input type="hidden" id="reject_credit_id">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-x-circle me-1 text-danger"></i> Reject Credit Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-danger fw-semibold">Are you sure you want to reject this credit request?</p>
                        <div class="mb-3">
                            <label class="form-label">Rejection Reason / Remarks <span class="text-danger">*</span></label>
                            <textarea name="remarks" class="form-control" rows="2" placeholder="State reason for rejection..." required></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
