@extends('layouts.master')
@section('title', 'Lead Requirements - Super Admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div id="alert-container"></div>

        <div class="card">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="card-header p-0 m-0"><i class="bx bx-task me-2"></i>Lead Requirements</h5>
                @can('lead-requirements.create')
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLeadRequirementModal">
                        <i class="bx bx-plus me-1"></i> Add Lead Requirement
                    </button>
                @endcan
            </div>
            <div class="table-responsive text-nowrap p-3">
                <table id="lead-requirements-table" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Requirement Name</th>
                            <th>Pricing / Slabs</th>
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

    <!-- Add Lead Requirement Modal -->
    <div class="modal fade" id="addLeadRequirementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content shadow border-0">
                <form id="addLeadRequirementForm" method="POST" novalidate>
                    @csrf
                    <div class="modal-header bg-light border-bottom py-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm bg-label-primary rounded me-2 d-flex align-items-center justify-content-center">
                                <i class="bx bx-plus fs-4"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-dark mb-0">Add Lead Requirement</h5>
                                <small class="text-muted">Create service requirement and its pricing tiers</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Requirement Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Bulk Voicecall, Bulk SMS, CRM Software, IVR" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Pricing & Package Slabs Section -->
                        <div class="border-top pt-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                <div>
                                    <h6 class="fw-bold mb-0 text-primary"><i class="bx bx-purchase-tag-alt me-1"></i> Pricing & Package Slabs</h6>
                                    <small class="text-muted">Configure customer specific slabs (User / Reseller / All)</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary add-price-row-btn">
                                    <i class="bx bx-plus me-1"></i> Add Price Slab
                                </button>
                            </div>

                            <div class="table-responsive border rounded bg-white">
                                <table class="table table-bordered table-sm align-middle mb-0 price-slabs-table" style="min-width: 900px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="min-width: 140px;">Customer Type <span class="text-danger">*</span></th>
                                            <th style="min-width: 130px;">Package Name <span class="text-danger">*</span></th>
                                            <th style="min-width: 110px;">Quantity <span class="text-danger">*</span></th>
                                            <th style="min-width: 120px;">Paisa (Price ₹) <span class="text-danger">*</span></th>
                                            <th style="min-width: 120px;">Subtotal (₹)</th>
                                            <th style="min-width: 90px;">Tax (%)</th>
                                            <th style="min-width: 110px;">Tax Amt (₹)</th>
                                            <th style="min-width: 130px;">Total (₹)</th>
                                            <th style="width: 45px;" class="text-center"><i class="bx bx-trash"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody class="price-slabs-tbody">
                                        <!-- Dynamic rows -->
                                    </tbody>
                                </table>
                            </div>
                            <small class="text-muted d-block mt-2"><i class="bx bx-info-circle me-1"></i>Subtotal = Quantity × Paisa (Price ₹); Total = Subtotal + Tax Amount</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top py-3 gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="addLeadRequirementSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Save Requirement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Lead Requirement Modal -->
    <div class="modal fade" id="editLeadRequirementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content shadow border-0">
                <form id="editLeadRequirementForm" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="lead_requirements_id" id="edit_lead_requirements_id">
                    <div class="modal-header bg-light border-bottom py-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm bg-label-primary rounded me-2 d-flex align-items-center justify-content-center">
                                <i class="bx bx-edit fs-4"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-dark mb-0">Edit Lead Requirement</h5>
                                <small class="text-muted">Update requirement details and customer rate slabs</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Requirement Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="edit_requirement_name" class="form-control" placeholder="Enter requirement name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" id="edit_requirement_status" class="form-select" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Pricing & Package Slabs Section -->
                        <div class="border-top pt-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                <div>
                                    <h6 class="fw-bold mb-0 text-primary"><i class="bx bx-purchase-tag-alt me-1"></i> Pricing & Package Slabs</h6>
                                    <small class="text-muted">Configure customer specific slabs (User / Reseller / All)</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary add-price-row-btn">
                                    <i class="bx bx-plus me-1"></i> Add Price Slab
                                </button>
                            </div>

                            <div class="table-responsive border rounded bg-white">
                                <table class="table table-bordered table-sm align-middle mb-0 price-slabs-table" style="min-width: 900px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="min-width: 140px;">Customer Type <span class="text-danger">*</span></th>
                                            <th style="min-width: 130px;">Package Name <span class="text-danger">*</span></th>
                                            <th style="min-width: 110px;">Quantity <span class="text-danger">*</span></th>
                                            <th style="min-width: 120px;">Paisa (Price ₹) <span class="text-danger">*</span></th>
                                            <th style="min-width: 120px;">Subtotal (₹)</th>
                                            <th style="min-width: 90px;">Tax (%)</th>
                                            <th style="min-width: 110px;">Tax Amt (₹)</th>
                                            <th style="min-width: 130px;">Total (₹)</th>
                                            <th style="width: 45px;" class="text-center"><i class="bx bx-trash"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody class="price-slabs-tbody">
                                        <!-- Dynamic rows -->
                                    </tbody>
                                </table>
                            </div>
                            <small class="text-muted d-block mt-2"><i class="bx bx-info-circle me-1"></i>Subtotal = Quantity × Paisa (Price ₹); Total = Subtotal + Tax Amount</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top py-3 gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="editLeadRequirementSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Update Requirement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Lead Requirement Modal -->
    <div class="modal fade" id="deleteLeadRequirementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-trash me-1 text-danger"></i> Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete lead requirement <strong id="delete_requirement_name"></strong>?</p>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteRequirementBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection
