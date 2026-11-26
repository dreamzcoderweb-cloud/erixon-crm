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
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLeadModal">
                            <i class="bx bx-plus me-1"></i> Add Lead
                        </button>
                        <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#importLeadModal">
                            <i class="bx bx-import me-1"></i> Import
                        </button>
                    </div>
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
                            <label class="form-label fw-semibold">Requirement</label>
                            <select name="lead_requirement_id" id="lead_filter_requirement_id" class="form-select form-select-sm">
                                <option value="">-- All Requirements --</option>
                                @if(isset($leadRequirements) && count($leadRequirements) > 0)
                                    @foreach ($leadRequirements as $req)
                                        <option value="{{ $req->lead_requirements_id }}">{{ $req->name }}</option>
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
                            <label class="form-label fw-semibold">Stage</label>
                            <select name="lead_stage_id" id="lead_filter_stage_id" class="form-select form-select-sm">
                                <option value="">-- All Stages --</option>
                                @if(isset($leadStages) && count($leadStages) > 0)
                                    @foreach ($leadStages as $stg)
                                        <option value="{{ $stg->lead_stage_id }}">{{ $stg->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Lost Reason</label>
                            <select name="lost_reason_id" id="lead_filter_lost_reason_id" class="form-select form-select-sm">
                                <option value="">-- All Lost Reasons --</option>
                                @if(isset($lostReasons) && count($lostReasons) > 0)
                                    @foreach ($lostReasons as $lr)
                                        <option value="{{ $lr->lost_reason_id }}">{{ $lr->reason }}</option>
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
                                <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" id="add_lead_customer_name" class="form-control" placeholder="Enter customer name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Customer Type <span class="text-danger">*</span></label>
                                <select name="customer_type" id="add_lead_customer_type" class="form-select" required>
                                    <option value="user" selected>User</option>
                                    <option value="reseller">Reseller</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile <span class="text-danger">*</span></label>
                                <input type="text" name="mobile" id="add_lead_mobile" class="form-control" placeholder="Enter mobile number" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" id="add_lead_email" class="form-control" placeholder="Enter email address">
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
                            <div class="col-md-6">
                                <label class="form-label">Branch <span class="text-muted small">(Multiple Select)</span></label>
                                <select name="branch_ids[]" id="add_lead_branch_id" class="form-select select2-multiple" multiple data-placeholder="Select Branch(es)">
                                    @if(isset($branches) && count($branches) > 0)
                                        @foreach ($branches as $br)
                                            <option value="{{ $br->id }}">{{ $br->name }} ({{ $br->code }})</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Team <span class="text-muted small">(Multiple Select)</span></label>
                                <select name="team_ids[]" id="add_lead_team_id" class="form-select select2-multiple" multiple data-placeholder="Select Team(s)">
                                    @if(isset($teams) && count($teams) > 0)
                                        @foreach ($teams as $tm)
                                            <option value="{{ $tm->id }}" data-branch="{{ $tm->branch_id }}">{{ $tm->name }} ({{ $tm->code }})</option>
                                        @endforeach
                                    @endif
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
                            <div class="col-md-4" id="add_next_followup_group">
                                <label class="form-label">Next Follow-up Date</label>
                                <input type="date" name="next_followup_date" id="add_next_followup_date" class="form-control">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description / Requirement</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Enter lead details or requirements"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4" id="add_lead_stage_group">
                                <label class="form-label">Lead Stage</label>
                                <select name="lead_stage_id" id="add_lead_stage_id" class="form-select">
                                    <option value="">-- Select Stage --</option>
                                    @foreach ($leadStages as $stg)
                                        <option value="{{ $stg->lead_stage_id }}" data-name="{{ strtolower($stg->name) }}">{{ $stg->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4" id="add_lead_requirement_group">
                                <label class="form-label">Lead Requirement</label>
                                <select name="lead_requirement_id" class="form-select">
                                    <option value="">-- Select Requirement --</option>
                                    @foreach ($leadRequirements as $req)
                                        <option value="{{ $req->lead_requirements_id }}">{{ $req->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4" id="add_lost_reason_group">
                                <label class="form-label">Lost Reason</label>
                                <select name="lost_reason_id" id="add_lost_reason_id" class="form-select">
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
                                                @if ($field->field_type !== 'Checkbox')
                                                    <label class="form-label">
                                                        {{ $field->field_label }}
                                                        @if ($isReq) <span class="text-danger">*</span> @endif
                                                    </label>
                                                @endif

                                                @if ($field->field_type === 'Number')
                                                    <input type="number" step="any" name="{{ $inputName }}" class="form-control" placeholder="Enter {{ strtolower($field->field_label) }}" {{ $isReq ? 'required' : '' }}>
                                                    <div class="invalid-feedback"></div>
                                                @elseif ($field->field_type === 'Date')
                                                    <input type="date" name="{{ $inputName }}" class="form-control" {{ $isReq ? 'required' : '' }}>
                                                    <div class="invalid-feedback"></div>
                                                @elseif ($field->field_type === 'Dropdown')
                                                    <select name="{{ $inputName }}" class="form-select" {{ $isReq ? 'required' : '' }}>
                                                        <option value="">-- Select {{ $field->field_label }} --</option>
                                                        @foreach ($options as $opt)
                                                            @if(!empty($opt))
                                                                <option value="{{ $opt }}">{{ $opt }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    <div class="invalid-feedback"></div>
                                                @elseif ($field->field_type === 'Textarea')
                                                    <textarea name="{{ $inputName }}" class="form-control" rows="2" placeholder="Enter {{ strtolower($field->field_label) }}" {{ $isReq ? 'required' : '' }}></textarea>
                                                    <div class="invalid-feedback"></div>
                                                @elseif ($field->field_type === 'Checkbox')
                                                    <div class="form-check pt-2">
                                                        <input class="form-check-input" type="checkbox" name="{{ $inputName }}" value="1" id="cf_add_{{ $field->field_name }}">
                                                        <label class="form-check-label fw-medium" for="cf_add_{{ $field->field_name }}">{{ $field->field_label }} @if ($isReq) <span class="text-danger">*</span> @endif</label>
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                @else
                                                    <input type="text" name="{{ $inputName }}" class="form-control" placeholder="Enter {{ strtolower($field->field_label) }}" {{ $isReq ? 'required' : '' }}>
                                                    <div class="invalid-feedback"></div>
                                                @endif
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
                            <input type="hidden" name="customer_id" id="edit_lead_customer_id">
                            <div class="col-md-6">
                                <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" id="edit_lead_customer_name" class="form-control" placeholder="Enter customer name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Customer Type <span class="text-danger">*</span></label>
                                <select name="customer_type" id="edit_lead_customer_type" class="form-select" required>
                                    <option value="user">User</option>
                                    <option value="reseller">Reseller</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile <span class="text-danger">*</span></label>
                                <input type="text" name="mobile" id="edit_lead_mobile" class="form-control" placeholder="Enter mobile number" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" id="edit_lead_email" class="form-control" placeholder="Enter email address">
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
                            <div class="col-md-6">
                                <label class="form-label">Branch <span class="text-muted small">(Multiple Select)</span></label>
                                <select name="branch_ids[]" id="edit_lead_branch_id" class="form-select select2-multiple" multiple data-placeholder="Select Branch(es)">
                                    @if(isset($branches) && count($branches) > 0)
                                        @foreach ($branches as $br)
                                            <option value="{{ $br->id }}">{{ $br->name }} ({{ $br->code }})</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Team <span class="text-muted small">(Multiple Select)</span></label>
                                <select name="team_ids[]" id="edit_lead_team_id" class="form-select select2-multiple" multiple data-placeholder="Select Team(s)">
                                    @if(isset($teams) && count($teams) > 0)
                                        @foreach ($teams as $tm)
                                            <option value="{{ $tm->id }}" data-branch="{{ $tm->branch_id }}">{{ $tm->name }} ({{ $tm->code }})</option>
                                        @endforeach
                                    @endif
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
                            <div class="col-md-4" id="edit_next_followup_group">
                                <label class="form-label">Next Follow-up Date</label>
                                <input type="date" name="next_followup_date" id="edit_lead_next_followup_date" class="form-control">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description / Requirement</label>
                                <textarea name="description" id="edit_lead_description" class="form-control" rows="3" placeholder="Enter lead details"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4" id="edit_lead_stage_group">
                                <label class="form-label">Lead Stage</label>
                                <select name="lead_stage_id" id="edit_lead_stage_id" class="form-select">
                                    <option value="">-- Select Stage --</option>
                                    @foreach ($leadStages as $stg)
                                        <option value="{{ $stg->lead_stage_id }}" data-name="{{ strtolower($stg->name) }}">{{ $stg->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4" id="edit_lead_requirement_group">
                                <label class="form-label">Lead Requirement</label>
                                <select name="lead_requirement_id" id="edit_lead_requirement_id" class="form-select">
                                    <option value="">-- Select Requirement --</option>
                                    @foreach ($leadRequirements as $req)
                                        <option value="{{ $req->lead_requirements_id }}">{{ $req->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4" id="edit_lost_reason_group">
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
                                                @if ($field->field_type !== 'Checkbox')
                                                    <label class="form-label">
                                                        {{ $field->field_label }}
                                                        @if ($isReq) <span class="text-danger">*</span> @endif
                                                    </label>
                                                @endif

                                                @if ($field->field_type === 'Number')
                                                    <input type="number" step="any" name="{{ $inputName }}" id="edit_cf_{{ $field->field_name }}" class="form-control" placeholder="Enter {{ strtolower($field->field_label) }}" {{ $isReq ? 'required' : '' }}>
                                                    <div class="invalid-feedback"></div>
                                                @elseif ($field->field_type === 'Date')
                                                    <input type="date" name="{{ $inputName }}" id="edit_cf_{{ $field->field_name }}" class="form-control" {{ $isReq ? 'required' : '' }}>
                                                    <div class="invalid-feedback"></div>
                                                @elseif ($field->field_type === 'Dropdown')
                                                    <select name="{{ $inputName }}" id="edit_cf_{{ $field->field_name }}" class="form-select" {{ $isReq ? 'required' : '' }}>
                                                        <option value="">-- Select {{ $field->field_label }} --</option>
                                                        @foreach ($options as $opt)
                                                            @if(!empty($opt))
                                                                <option value="{{ $opt }}">{{ $opt }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    <div class="invalid-feedback"></div>
                                                @elseif ($field->field_type === 'Textarea')
                                                    <textarea name="{{ $inputName }}" id="edit_cf_{{ $field->field_name }}" class="form-control" rows="2" placeholder="Enter {{ strtolower($field->field_label) }}" {{ $isReq ? 'required' : '' }}></textarea>
                                                    <div class="invalid-feedback"></div>
                                                @elseif ($field->field_type === 'Checkbox')
                                                    <div class="form-check pt-2">
                                                        <input class="form-check-input" type="checkbox" name="{{ $inputName }}" value="1" id="edit_cf_{{ $field->field_name }}">
                                                        <label class="form-check-label fw-medium" for="edit_cf_{{ $field->field_name }}">{{ $field->field_label }} @if ($isReq) <span class="text-danger">*</span> @endif</label>
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                @else
                                                    <input type="text" name="{{ $inputName }}" id="edit_cf_{{ $field->field_name }}" class="form-control" placeholder="Enter {{ strtolower($field->field_label) }}" {{ $isReq ? 'required' : '' }}>
                                                    <div class="invalid-feedback"></div>
                                                @endif
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


    <!-- Lead Import Modal -->
    @can('leads.create')
    <div class="modal fade" id="importLeadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form id="importLeadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-import me-1"></i> Import Leads</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info mb-3 py-2 small">
                            <i class="bx bx-info-circle me-1"></i>
                            Upload an Excel file (.xlsx, .xls) or CSV. Required columns: <strong>customer_name, mobile, lead_title</strong>.
                            <a href="{{ route('admin.leads.import-template') }}" class="fw-bold ms-1" target="_blank">
                                <i class="bx bx-download me-1"></i>Download Template
                            </a>
                        </div>
                        <div class="mb-3">
                            <label for="lead_import_file" class="form-label fw-semibold">Select File <span class="text-danger">*</span></label>
                            <input type="file" name="excel_file" id="lead_import_file" class="form-control"
                                accept=".xlsx,.xls,.csv" required />
                            <div class="form-text text-muted">Allowed: .xlsx, .xls, .csv — Max 5 MB</div>
                        </div>
                        <div id="importLeadResult" class="d-none"></div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success" id="importLeadSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" id="importLeadSpinner" role="status"></span>
                            <i class="bx bx-upload me-1"></i> Upload & Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    <!-- Customer Full Details Modal (Calls, Sale Closed & Profile) -->
    <div class="modal fade" id="customerDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light border-bottom py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md bg-primary text-white rounded p-2 d-flex align-items-center justify-content-center shadow-sm">
                            <i class="bx bx-user fs-3"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 fw-bold" id="cdm_name">Customer Details</h5>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge bg-label-primary" id="cdm_type">User</span>
                                <span id="cdm_sale_closed_badge"></span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-0">
                    <!-- Loading State -->
                    <div id="cdm_loading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h6 class="text-muted mt-3 mb-0">Loading customer history & call details...</h6>
                    </div>

                    <!-- Content State -->
                    <div id="cdm_content" class="d-none">
                        <!-- KPI Row (Calls, Leads, Sale Closed, Demos, Proposals, Follow-ups) -->
                        <div class="p-3 bg-light border-bottom">
                            <div class="row g-2">
                                <div class="col-md-2 col-sm-4 col-6">
                                    <div class="card shadow-none border bg-white p-2 h-100">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-label-primary rounded me-2 d-flex align-items-center justify-content-center">
                                                <i class="bx bx-phone-call fs-4"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block text-uppercase" style="font-size: 10px;">Total Calls</small>
                                                <h6 class="mb-0 fw-bold text-primary" id="cdm_stat_calls">0</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-4 col-6">
                                    <div class="card shadow-none border bg-white p-2 h-100">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-label-warning rounded me-2 d-flex align-items-center justify-content-center">
                                                <i class="bx bx-target-lock fs-4"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block text-uppercase" style="font-size: 10px;">Total Leads</small>
                                                <h6 class="mb-0 fw-bold text-warning" id="cdm_stat_leads">0</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-4 col-6">
                                    <div class="card shadow-none border bg-white p-2 h-100">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-label-success rounded me-2 d-flex align-items-center justify-content-center">
                                                <i class="bx bx-check-double fs-4"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block text-uppercase" style="font-size: 10px;">Sale Closed</small>
                                                <h6 class="mb-0 fw-bold" id="cdm_stat_closed">-</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-4 col-6">
                                    <div class="card shadow-none border bg-white p-2 h-100">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-label-danger rounded me-2 d-flex align-items-center justify-content-center">
                                                <i class="bx bx-slideshow fs-4"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block text-uppercase" style="font-size: 10px;">Demo Process</small>
                                                <h6 class="mb-0 fw-bold text-danger" id="cdm_stat_demos">0</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-4 col-6">
                                    <div class="card shadow-none border bg-white p-2 h-100">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-label-secondary rounded me-2 d-flex align-items-center justify-content-center">
                                                <i class="bx bx-file fs-4"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block text-uppercase" style="font-size: 10px;">Proposals</small>
                                                <h6 class="mb-0 fw-bold text-dark" id="cdm_stat_proposals">0</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-4 col-6">
                                    <div class="card shadow-none border bg-white p-2 h-100">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-label-info rounded me-2 d-flex align-items-center justify-content-center">
                                                <i class="bx bx-calendar-event fs-4"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block text-uppercase" style="font-size: 10px;">Follow-ups</small>
                                                <h6 class="mb-0 fw-bold text-info" id="cdm_stat_followups">0</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sale Closed Highlight Banner -->
                        <div id="cdm_sale_closed_banner" class="m-3 p-3 bg-label-success border border-success rounded d-none">
                            <div class="d-flex align-items-start gap-3">
                                <i class="bx bx-check-circle text-success" style="font-size: 2.2rem;"></i>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="text-success fw-bold mb-1">
                                            <i class="bx bx-party me-1"></i> Sale Successfully Closed!
                                        </h6>
                                        <span class="badge bg-success" id="cdm_closed_date_badge"></span>
                                    </div>
                                    <p class="mb-1 text-dark" id="cdm_sale_closed_text"></p>
                                    <small class="text-muted" id="cdm_sale_closed_subtext"></small>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Tabs -->
                        <div class="p-3">
                            <ul class="nav nav-pills nav-fill mb-3" role="tablist">
                                <li class="nav-item">
                                    <button type="button" class="nav-link active fw-semibold" role="tab" data-bs-toggle="tab" data-bs-target="#cdm_tab_calls">
                                        <i class="bx bx-phone-call me-1"></i> Calls (<span id="cdm_tab_count_calls">0</span>)
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link fw-semibold" role="tab" data-bs-toggle="tab" data-bs-target="#cdm_tab_sale">
                                        <i class="bx bx-target-lock me-1"></i> Leads & Closed (<span id="cdm_tab_count_leads">0</span>)
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link fw-semibold" role="tab" data-bs-toggle="tab" data-bs-target="#cdm_tab_demos">
                                        <i class="bx bx-slideshow me-1"></i> Demo Process (<span id="cdm_tab_count_demos">0</span>)
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link fw-semibold" role="tab" data-bs-toggle="tab" data-bs-target="#cdm_tab_proposals">
                                        <i class="bx bx-file me-1"></i> Proposals (<span id="cdm_tab_count_proposals">0</span>)
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link fw-semibold" role="tab" data-bs-toggle="tab" data-bs-target="#cdm_tab_profile">
                                        <i class="bx bx-user me-1"></i> Customer Info
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link fw-semibold" role="tab" data-bs-toggle="tab" data-bs-target="#cdm_tab_followups">
                                        <i class="bx bx-calendar-event me-1"></i> Follow-ups (<span id="cdm_tab_count_followups">0</span>)
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content border rounded p-3 bg-white">
                                <!-- Tab 1: Calls History ("yatha staff yepo call paniruka") -->
                                <div class="tab-pane fade show active" id="cdm_tab_calls" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 fw-bold"><i class="bx bx-list-ul me-1"></i> Staff Call History</h6>
                                        <small class="text-muted">Calls placed to/from this customer</small>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped table-hover align-middle w-100" id="cdm_calls_table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 40px;">#</th>
                                                    <th>Staff Member</th>
                                                    <th>Date & Time</th>
                                                    <th>Call Type</th>
                                                    <th>Status</th>
                                                    <th>Duration</th>
                                                    <th>Lead / Context</th>
                                                    <th>Recording</th>
                                                </tr>
                                            </thead>
                                            <tbody id="cdm_calls_tbody">
                                                <!-- Dynamic rows -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Tab 2: Sale Closed & Leads History ("yepo sale close aagiruch") -->
                                <div class="tab-pane fade" id="cdm_tab_sale" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 fw-bold"><i class="bx bx-target-lock me-1"></i> Leads & Sale Closed Timeline</h6>
                                        <small class="text-muted">Stages and closure details</small>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped table-hover align-middle w-100" id="cdm_leads_table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 40px;">#</th>
                                                    <th>Lead Title</th>
                                                    <th>Current Stage</th>
                                                    <th>Requirement</th>
                                                    <th>Source</th>
                                                    <th>Assigned Staff</th>
                                                    <th>Created Date</th>
                                                    <th>Last Update / Closed Date</th>
                                                </tr>
                                            </thead>
                                            <tbody id="cdm_leads_tbody">
                                                <!-- Dynamic rows -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Tab 3: Demo Process -->
                                <div class="tab-pane fade" id="cdm_tab_demos" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 fw-bold"><i class="bx bx-slideshow me-1"></i> Demo Process Details</h6>
                                        <small class="text-muted">Scheduled and completed customer demos</small>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped table-hover align-middle w-100" id="cdm_demos_table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 40px;">#</th>
                                                    <th>Demo Date & Time</th>
                                                    <th>Requirement</th>
                                                    <th>Lead Source</th>
                                                    <th>Assigned Staff</th>
                                                    <th>Status</th>
                                                    <th>Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody id="cdm_demos_tbody">
                                                <!-- Dynamic rows -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Tab 4: Proposals -->
                                <div class="tab-pane fade" id="cdm_tab_proposals" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 fw-bold"><i class="bx bx-file me-1"></i> Customer Proposals</h6>
                                        <small class="text-muted">Proposals generated for this customer</small>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped table-hover align-middle w-100" id="cdm_proposals_table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 40px;">#</th>
                                                    <th>Proposal #</th>
                                                    <th>Date</th>
                                                    <th>Requirement</th>
                                                    <th>Sales Manager</th>
                                                    <th>Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="cdm_proposals_tbody">
                                                <!-- Dynamic rows -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Tab 5: Customer Profile -->
                                <div class="tab-pane fade" id="cdm_tab_profile" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 h-100 bg-light">
                                                <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary"><i class="bx bx-id-card me-1"></i> Basic Information</h6>
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tr>
                                                        <td class="text-muted fw-semibold" style="width: 35%;">Customer Name:</td>
                                                        <td class="fw-bold" id="cdm_p_name">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted fw-semibold">Customer Type:</td>
                                                        <td id="cdm_p_type">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted fw-semibold">Mobile:</td>
                                                        <td><a href="#" id="cdm_p_mobile_link" class="fw-bold text-primary"><span id="cdm_p_mobile">-</span></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted fw-semibold">Email:</td>
                                                        <td><a href="#" id="cdm_p_email_link"><span id="cdm_p_email">-</span></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted fw-semibold">Company:</td>
                                                        <td id="cdm_p_company">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted fw-semibold">City:</td>
                                                        <td id="cdm_p_city">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted fw-semibold">Address:</td>
                                                        <td id="cdm_p_address">-</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="border rounded p-3 h-100 bg-light">
                                                <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary"><i class="bx bx-buildings me-1"></i> Branch, Team & Assignment</h6>
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tr>
                                                        <td class="text-muted fw-semibold" style="width: 35%;">Branch(es):</td>
                                                        <td id="cdm_p_branch">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted fw-semibold">Team(s):</td>
                                                        <td id="cdm_p_team">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted fw-semibold">Owner / Assigned:</td>
                                                        <td id="cdm_p_owner">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted fw-semibold">Created By:</td>
                                                        <td id="cdm_p_creator">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted fw-semibold">Created Date:</td>
                                                        <td id="cdm_p_created_at">-</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 6: Follow-ups -->
                                <div class="tab-pane fade" id="cdm_tab_followups" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 fw-bold"><i class="bx bx-calendar-event me-1"></i> Follow-up History</h6>
                                        <small class="text-muted">Follow-up actions and reminders</small>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped table-hover align-middle w-100" id="cdm_followups_table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 40px;">#</th>
                                                    <th>Lead / Context</th>
                                                    <th>Staff</th>
                                                    <th>Next Follow-up Date</th>
                                                    <th>Status</th>
                                                    <th>Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody id="cdm_followups_tbody">
                                                <!-- Dynamic rows -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.customLeadFields = @json($customFields ?? []);
        window.configuredLeadColumns = @json($visibleColumns ?? []);

        // Lead Import
        (function () {
            const form = document.getElementById('importLeadForm');
            if (!form) return;
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const btn     = document.getElementById('importLeadSubmitBtn');
                const spinner = document.getElementById('importLeadSpinner');
                const result  = document.getElementById('importLeadResult');

                btn.disabled = true;
                spinner.classList.remove('d-none');
                result.className = 'd-none';

                const data = new FormData(form);
                fetch('{{ route("admin.leads.import") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    },
                    body: data
                })
                .then(r => r.json())
                .then(res => {
                    btn.disabled = false;
                    spinner.classList.add('d-none');
                    if (res.status) {
                        let html = `<div class="alert alert-success py-2 small mb-0"><i class="bx bx-check-circle me-1"></i>${res.message}</div>`;
                        if (res.errors && res.errors.length) {
                            html += `<ul class="small text-warning mt-2 mb-0">${res.errors.map(e => `<li>${e}</li>`).join('')}</ul>`;
                        }
                        result.innerHTML = html;
                        result.className = '';
                        form.reset();
                        // Reload lead table if function exists
                        if (window.leadTable) window.leadTable.ajax.reload(null, false);
                    } else {
                        result.innerHTML = `<div class="alert alert-danger py-2 small mb-0"><i class="bx bx-error-circle me-1"></i>${res.message || 'Import failed.'}</div>`;
                        result.className = '';
                    }
                })
                .catch(() => {
                    btn.disabled = false;
                    spinner.classList.add('d-none');
                    result.innerHTML = `<div class="alert alert-danger py-2 small mb-0">Network error. Please try again.</div>`;
                    result.className = '';
                });
            });
        })();
    </script>
@endsection
