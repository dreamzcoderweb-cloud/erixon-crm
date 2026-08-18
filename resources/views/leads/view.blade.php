@extends('layouts.master')
@section('title', 'Leads Management - Super Admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div id="alert-container"></div>

        <div class="card">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="card-header p-0 m-0"><i class="bx bx-git-pull-request me-2"></i>Leads Management</h5>
                @can('leads.create')
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLeadModal">
                        <i class="bx bx-plus me-1"></i> Add Lead
                    </button>
                @endcan
            </div>
            <div class="table-responsive text-nowrap p-3">
                <table id="leads-table" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Lead Title</th>
                            <th>Customer</th>
                            <th>Source</th>
                            <th>Priority</th>
                            <th>Expected Amount</th>
                            <th>Assigned To</th>
                            <th>Next Follow-up</th>
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
@endsection
