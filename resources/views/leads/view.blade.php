@extends('layouts.master')
@section('title', 'Leads Management - Super Admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div id="alert-container"></div>

        <!-- Analytics KPI Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card shadow-sm border-start border-primary border-4 kpi-card-clickable" id="kpi_card_total_leads" style="cursor: pointer;" title="Click to reset filters">
                    <div class="card-body p-3 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block">Lead Count</small>
                        <h3 class="mb-0 text-primary fw-bold mt-1" id="kpi_total_leads">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card shadow-sm border-start border-warning border-4 kpi-card-clickable" id="kpi_card_staff_leads" style="cursor: pointer;" title="Click to view staff created leads">
                    <div class="card-body p-3 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block">Staff Created Count</small>
                        <h3 class="mb-0 text-warning fw-bold mt-1" id="kpi_staff_created_leads">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="card-header p-0 m-0"><i class="bx bx-git-pull-request me-2"></i>Leads Management</h5>
                @can('leads.create')
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLeadModal">
                        <i class="bx bx-plus me-1"></i> Add Lead
                    </button>
                @endcan
            </div>

            <!-- Leads Filter Bar -->
            <div class="p-3 bg-light border-bottom">
                <form id="leadFilterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12">
                            <label class="form-label fw-semibold d-block">Date Period</label>
                            <div class="btn-group btn-group-sm" role="group" id="leadPeriodBtnGroup">
                                <button type="button" class="btn btn-outline-primary btn-lead-period active" data-period="all">All Time</button>
                                <button type="button" class="btn btn-outline-primary btn-lead-period" data-period="daily">Daily</button>
                                <button type="button" class="btn btn-outline-primary btn-lead-period" data-period="weekly">Weekly</button>
                                <button type="button" class="btn btn-outline-primary btn-lead-period" data-period="monthly">Monthly</button>
                                <button type="button" class="btn btn-outline-primary btn-lead-period" data-period="custom">Custom</button>
                            </div>
                            <input type="hidden" name="filter_type" id="lead_filter_period" value="all">
                        </div>

                        <div class="col-md-3 lead-filter-date-group d-none" id="lead_group_daily">
                            <label class="form-label fw-semibold">Date</label>
                            <input type="date" name="date" id="lead_filter_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-3 lead-filter-date-group d-none" id="lead_group_monthly">
                            <label class="form-label fw-semibold">Month</label>
                            <input type="month" name="month" id="lead_filter_month" class="form-control form-control-sm" value="{{ date('Y-m') }}">
                        </div>

                        <div class="col-md-3 lead-filter-date-group d-none" id="lead_group_custom_start">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="date" name="start_date" id="lead_filter_start_date" class="form-control form-control-sm" value="{{ date('Y-m-01') }}">
                        </div>

                        <div class="col-md-3 lead-filter-date-group d-none" id="lead_group_custom_end">
                            <label class="form-label fw-semibold">End Date</label>
                            <input type="date" name="end_date" id="lead_filter_end_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Lead Title</label>
                            <select name="lead_title" id="lead_filter_title" class="form-select form-select-sm">
                                <option value="">-- All Lead Titles --</option>
                                @if(isset($leadTitles) && count($leadTitles) > 0)
                                    @foreach ($leadTitles as $title)
                                        <option value="{{ $title }}">{{ $title }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Customer</label>
                            <select name="customer_id" id="lead_filter_customer_id" class="form-select form-select-sm">
                                <option value="">-- All Customers --</option>
                                @if(isset($customers) && count($customers) > 0)
                                    @foreach ($customers as $cust)
                                        <option value="{{ $cust->customer_id }}">{{ $cust->name }} ({{ $cust->mobile }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Source</label>
                            <select name="lead_source_id" id="lead_filter_source_id" class="form-select form-select-sm">
                                <option value="">-- All Sources --</option>
                                @if(isset($leadSources) && count($leadSources) > 0)
                                    @foreach ($leadSources as $src)
                                        <option value="{{ $src->lead_sources_id }}">{{ $src->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Created By</label>
                            <select name="created_by" id="lead_filter_created_by" class="form-select form-select-sm">
                                <option value="">-- All Staff --</option>
                                @if(isset($allStaffs) && count($allStaffs) > 0)
                                    @foreach ($allStaffs as $stf)
                                        <option value="{{ $stf->id }}">{{ $stf->name }} ({{ $stf->email }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" id="lead_filter_status" class="form-select form-select-sm">
                                <option value="">-- All Statuses --</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="bx bx-filter-alt me-1"></i> Apply Filter
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="resetLeadFilterBtn" title="Reset Filters">
                                    <i class="bx bx-refresh me-1"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table-responsive text-nowrap p-3">
                <table id="leads-table" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">#</th>
                            @if (isset($visibleColumns) && count($visibleColumns) > 0)
                                @foreach ($visibleColumns as $col)
                                    <th class="{{ in_array($col['key'], ['created_at', 'created_by', 'status']) ? 'text-center' : '' }}">
                                        {{ $col['label'] }}
                                    </th>
                                @endforeach
                            @endif
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

    <!-- Add Lead Modal -->
    <div class="modal fade" id="addLeadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="addLeadForm" method="POST" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-plus-circle me-1"></i> Add New Lead</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Customer <span class="text-danger">*</span></label>
                                <select name="customer_id" class="form-select" required>
                                    <option value="">-- Select Customer --</option>
                                    @foreach ($customers as $cust)
                                        <option value="{{ $cust->customer_id }}">{{ $cust->name }} ({{ $cust->mobile }})</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lead Title <span class="text-danger">*</span></label>
                                <input type="text" name="lead_title" class="form-control" placeholder="Enter lead title" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lead Source</label>
                                <select name="lead_source_id" class="form-select">
                                    <option value="">-- Select Lead Source --</option>
                                    @foreach ($leadSources as $src)
                                        <option value="{{ $src->lead_sources_id }}">{{ $src->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Assigned To (Staff)</label>
                                <select name="assigned_to" class="form-select">
                                    <option value="">-- Unassigned --</option>
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Priority <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select" required>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Expected Amount (₹)</label>
                                <input type="number" step="0.01" name="expected_amount" class="form-control" placeholder="0.00">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Next Follow-up Date</label>
                                <input type="date" name="next_followup_date" class="form-control">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description / Requirement</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Enter lead details or requirements"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Lead Stage</label>
                                <select name="lead_stage_id" class="form-select">
                                    <option value="">-- Select Stage --</option>
                                    @foreach ($leadStages as $stg)
                                        <option value="{{ $stg->lead_stage_id }}">{{ $stg->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Lead Requirement</label>
                                <select name="lead_requirement_id" class="form-select">
                                    <option value="">-- Select Requirement --</option>
                                    @foreach ($leadRequirements as $req)
                                        <option value="{{ $req->lead_requirements_id }}">{{ $req->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Lost Reason</label>
                                <select name="lost_reason_id" class="form-select">
                                    <option value="">-- Select Lost Reason --</option>
                                    @foreach ($lostReasons as $lr)
                                        <option value="{{ $lr->lost_reason_id }}">{{ $lr->reason }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="1">Active / In-Progress</option>
                                    <option value="0">Inactive / Closed</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            @if (isset($customFields) && count($customFields) > 0)
                                <div class="col-12 border-top pt-3 mt-3">
                                    <h6 class="fw-bold mb-3 text-primary"><i class="bx bx-list-plus me-1"></i> Additional Fields</h6>
                                    <div class="row g-3">
                                        @foreach ($customFields as $field)
                                            @php
                                                $isReq = $field->is_required === 'Yes';
                                                $inputName = "custom_fields[{$field->field_name}]";
                                                $options = array_map('trim', explode(',', $field->field_options ?? ''));
                                            @endphp
                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    {{ $field->field_label }}
                                                    @if ($isReq) <span class="text-danger">*</span> @endif
                                                </label>

                                                @if ($field->field_type === 'Number')
                                                    <input type="number" step="any" name="{{ $inputName }}" class="form-control" placeholder="Enter {{ strtolower($field->field_label) }}" {{ $isReq ? 'required' : '' }}>
                                                @elseif ($field->field_type === 'Date')
                                                    <input type="date" name="{{ $inputName }}" class="form-control" {{ $isReq ? 'required' : '' }}>
                                                @elseif ($field->field_type === 'Dropdown')
                                                    <select name="{{ $inputName }}" class="form-select" {{ $isReq ? 'required' : '' }}>
                                                        <option value="">-- Select {{ $field->field_label }} --</option>
                                                        @foreach ($options as $opt)
                                                            @if(!empty($opt))
                                                                <option value="{{ $opt }}">{{ $opt }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                @elseif ($field->field_type === 'Textarea')
                                                    <textarea name="{{ $inputName }}" class="form-control" rows="2" placeholder="Enter {{ strtolower($field->field_label) }}" {{ $isReq ? 'required' : '' }}></textarea>
                                                @elseif ($field->field_type === 'Checkbox')
                                                    <div class="form-check pt-2">
                                                        <input class="form-check-input" type="checkbox" name="{{ $inputName }}" value="1" id="cf_add_{{ $field->field_name }}">
                                                        <label class="form-check-label" for="cf_add_{{ $field->field_name }}">{{ $field->field_label }}</label>
                                                    </div>
                                                @else
                                                    <input type="text" name="{{ $inputName }}" class="form-control" placeholder="Enter {{ strtolower($field->field_label) }}" {{ $isReq ? 'required' : '' }}>
                                                @endif

                                                <div class="invalid-feedback"></div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="addLeadSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Save Lead
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Lead Modal -->
    <div class="modal fade" id="editLeadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="editLeadForm" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="lead_id" id="edit_lead_id">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-edit me-1"></i> Edit Lead</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Customer <span class="text-danger">*</span></label>
                                <select name="customer_id" id="edit_lead_customer_id" class="form-select" required>
                                    <option value="">-- Select Customer --</option>
                                    @foreach ($customers as $cust)
                                        <option value="{{ $cust->customer_id }}">{{ $cust->name }} ({{ $cust->mobile }})</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lead Title <span class="text-danger">*</span></label>
                                <input type="text" name="lead_title" id="edit_lead_title" class="form-control" placeholder="Enter lead title" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lead Source</label>
                                <select name="lead_source_id" id="edit_lead_source_id" class="form-select">
                                    <option value="">-- Select Lead Source --</option>
                                    @foreach ($leadSources as $src)
                                        <option value="{{ $src->lead_sources_id }}">{{ $src->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Assigned To (Staff)</label>
                                <select name="assigned_to" id="edit_lead_assigned_to" class="form-select">
                                    <option value="">-- Unassigned --</option>
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Priority <span class="text-danger">*</span></label>
                                <select name="priority" id="edit_lead_priority" class="form-select" required>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Expected Amount (₹)</label>
                                <input type="number" step="0.01" name="expected_amount" id="edit_lead_expected_amount" class="form-control" placeholder="0.00">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Next Follow-up Date</label>
                                <input type="date" name="next_followup_date" id="edit_lead_next_followup_date" class="form-control">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description / Requirement</label>
                                <textarea name="description" id="edit_lead_description" class="form-control" rows="3" placeholder="Enter lead details"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Lead Stage</label>
                                <select name="lead_stage_id" id="edit_lead_stage_id" class="form-select">
                                    <option value="">-- Select Stage --</option>
                                    @foreach ($leadStages as $stg)
                                        <option value="{{ $stg->lead_stage_id }}">{{ $stg->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Lead Requirement</label>
                                <select name="lead_requirement_id" id="edit_lead_requirement_id" class="form-select">
                                    <option value="">-- Select Requirement --</option>
                                    @foreach ($leadRequirements as $req)
                                        <option value="{{ $req->lead_requirements_id }}">{{ $req->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Lost Reason</label>
                                <select name="lost_reason_id" id="edit_lost_reason_id" class="form-select">
                                    <option value="">-- Select Lost Reason --</option>
                                    @foreach ($lostReasons as $lr)
                                        <option value="{{ $lr->lost_reason_id }}">{{ $lr->reason }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="edit_lead_status" class="form-select" required>
                                    <option value="1">Active / In-Progress</option>
                                    <option value="0">Inactive / Closed</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            @if (isset($customFields) && count($customFields) > 0)
                                <div class="col-12 border-top pt-3 mt-3">
                                    <h6 class="fw-bold mb-3 text-primary"><i class="bx bx-list-plus me-1"></i> Additional Fields</h6>
                                    <div class="row g-3">
                                        @foreach ($customFields as $field)
                                            @php
                                                $isReq = $field->is_required === 'Yes';
                                                $inputName = "custom_fields[{$field->field_name}]";
                                                $options = array_map('trim', explode(',', $field->field_options ?? ''));
                                            @endphp
                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    {{ $field->field_label }}
                                                    @if ($isReq) <span class="text-danger">*</span> @endif
                                                </label>

                                                @if ($field->field_type === 'Number')
                                                    <input type="number" step="any" name="{{ $inputName }}" id="edit_cf_{{ $field->field_name }}" class="form-control" placeholder="Enter {{ strtolower($field->field_label) }}" {{ $isReq ? 'required' : '' }}>
                                                @elseif ($field->field_type === 'Date')
                                                    <input type="date" name="{{ $inputName }}" id="edit_cf_{{ $field->field_name }}" class="form-control" {{ $isReq ? 'required' : '' }}>
                                                @elseif ($field->field_type === 'Dropdown')
                                                    <select name="{{ $inputName }}" id="edit_cf_{{ $field->field_name }}" class="form-select" {{ $isReq ? 'required' : '' }}>
                                                        <option value="">-- Select {{ $field->field_label }} --</option>
                                                        @foreach ($options as $opt)
                                                            @if(!empty($opt))
                                                                <option value="{{ $opt }}">{{ $opt }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                @elseif ($field->field_type === 'Textarea')
                                                    <textarea name="{{ $inputName }}" id="edit_cf_{{ $field->field_name }}" class="form-control" rows="2" placeholder="Enter {{ strtolower($field->field_label) }}" {{ $isReq ? 'required' : '' }}></textarea>
                                                @elseif ($field->field_type === 'Checkbox')
                                                    <div class="form-check pt-2">
                                                        <input class="form-check-input" type="checkbox" name="{{ $inputName }}" value="1" id="edit_cf_{{ $field->field_name }}">
                                                        <label class="form-check-label" for="edit_cf_{{ $field->field_name }}">{{ $field->field_label }}</label>
                                                    </div>
                                                @else
                                                    <input type="text" name="{{ $inputName }}" id="edit_cf_{{ $field->field_name }}" class="form-control" placeholder="Enter {{ strtolower($field->field_label) }}" {{ $isReq ? 'required' : '' }}>
                                                @endif

                                                <div class="invalid-feedback"></div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="editLeadSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Update Lead
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Lead Modal -->
    <div class="modal fade" id="deleteLeadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-trash me-1 text-danger"></i> Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete lead <strong id="delete_lead_title"></strong>?</p>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteLeadBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.customLeadFields = @json($customFields ?? []);
        window.configuredLeadColumns = @json($visibleColumns ?? []);
    </script>
@endsection
