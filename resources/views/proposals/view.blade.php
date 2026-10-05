@extends('layouts.master')
@section('title', 'Proposal List')

@section('content')
<style>
    /* Prevent number spinner buttons from covering the digits like '18' */
    input.item-tax-pct::-webkit-outer-spin-button,
    input.item-tax-pct::-webkit-inner-spin-button,
    input.no-spinners::-webkit-outer-spin-button,
    input.no-spinners::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input.item-tax-pct,
    input.no-spinners {
        -moz-appearance: textfield;
        appearance: textfield;
    }
    #proposalItemsTable th, 
    #proposalItemsTable td {
        vertical-align: middle;
    }
</style>
<div class="container-xxl flex-grow-1 container-p-y">
    <div id="alert-container"></div>

    <!-- Header & Breadcrumbs -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Proposals</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 text-dark"><i class="bx bx-file me-2 text-primary"></i>Proposal Management</h4>
        </div>
        @can('proposals.create')
            <button class="btn btn-primary" id="openAddProposalModalBtn" data-bs-toggle="modal" data-bs-target="#proposalModal">
                <i class="bx bx-plus me-1"></i> Add Proposal
            </button>
        @endcan
    </div>

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block mb-1 fs-tiny text-uppercase fw-semibold">Total Proposals</span>
                        <h4 class="mb-0 fw-bold" id="kpi_total_proposals">0</h4>
                    </div>
                    <div class="avatar avatar-md bg-label-primary rounded p-2">
                        <i class="bx bx-file fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block mb-1 fs-tiny text-uppercase fw-semibold">Total Value</span>
                        <h4 class="mb-0 fw-bold text-success" id="kpi_total_amount">₹0.00</h4>
                    </div>
                    <div class="avatar avatar-md bg-label-success rounded p-2">
                        <i class="bx bx-rupee fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block mb-1 fs-tiny text-uppercase fw-semibold">Total Packages</span>
                        <h4 class="mb-0 fw-bold text-info" id="kpi_total_packages">0</h4>
                    </div>
                    <div class="avatar avatar-md bg-label-info rounded p-2">
                        <i class="bx bx-package fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block mb-1 fs-tiny text-uppercase fw-semibold">Sales Managers</span>
                        <h4 class="mb-0 fw-bold text-warning" id="kpi_active_managers">0</h4>
                    </div>
                    <div class="avatar avatar-md bg-label-warning rounded p-2">
                        <i class="bx bx-user-check fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-list-ul me-2"></i>All Proposals</h5>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="p-3 bg-light border-bottom">
            <form id="proposalFilterForm">
                <div class="row g-3 align-items-end">
                    <!-- Period Filter Buttons -->
                    <div class="col-12">
                        <label class="form-label fw-semibold d-block text-muted small text-uppercase">Date Period</label>
                        <div class="btn-group btn-group-sm flex-wrap" role="group" id="proposalPeriodBtnGroup">
                            <button type="button" class="btn btn-outline-primary btn-proposal-period active" data-period="all">All Time</button>
                            <button type="button" class="btn btn-outline-primary btn-proposal-period" data-period="daily">Daily</button>
                            <button type="button" class="btn btn-outline-primary btn-proposal-period" data-period="weekly">Weekly</button>
                            <button type="button" class="btn btn-outline-primary btn-proposal-period" data-period="monthly">Monthly</button>
                            <button type="button" class="btn btn-outline-primary btn-proposal-period" data-period="custom">Custom Range</button>
                        </div>
                        <input type="hidden" name="filter_type" id="proposal_filter_period" value="all">
                    </div>

                    <!-- Date inputs for dynamic periods -->
                    <div class="col-md-3 proposal-filter-date-group d-none" id="proposal_group_daily">
                        <label class="form-label fw-semibold small">Date</label>
                        <input type="date" name="date" id="proposal_filter_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="col-md-3 proposal-filter-date-group d-none" id="proposal_group_monthly">
                        <label class="form-label fw-semibold small">Month</label>
                        <input type="month" name="month" id="proposal_filter_month" class="form-control form-control-sm" value="{{ date('Y-m') }}">
                    </div>

                    <div class="col-md-3 proposal-filter-date-group d-none" id="proposal_group_custom_start">
                        <label class="form-label fw-semibold small">From Date</label>
                        <input type="date" name="start_date" id="proposal_filter_start_date" class="form-control form-control-sm" value="{{ date('Y-m-01') }}">
                    </div>

                    <div class="col-md-3 proposal-filter-date-group d-none" id="proposal_group_custom_end">
                        <label class="form-label fw-semibold small">To Date</label>
                        <input type="date" name="end_date" id="proposal_filter_end_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                    </div>

                    <!-- Lead Requirement Filter -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Lead Requirement</label>
                        <select name="lead_requirement_id" id="proposal_filter_lead_requirement_id" class="form-select form-select-sm">
                            <option value="">-- All Lead Requirements --</option>
                            @if(isset($leadRequirements))
                                @foreach ($leadRequirements as $req)
                                    <option value="{{ $req->lead_requirements_id }}">{{ $req->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Sales Manager Filter -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Sales Manager</label>
                        <select name="sales_manager_id" id="proposal_filter_sales_manager_id" class="form-select form-select-sm">
                            <option value="">-- All Sales Managers --</option>
                            @if(isset($salesManagers))
                                @foreach ($salesManagers as $sm)
                                    <option value="{{ $sm->id }}">{{ $sm->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Search</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" name="search" id="proposal_filter_search" class="form-control form-control-sm" placeholder="Search customer, mobile, package...">
                        </div>
                    </div>

                    <!-- Filter Actions -->
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                <i class="bx bx-filter-alt me-1"></i> Filter
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="resetProposalFilterBtn" title="Reset Filters">
                                <i class="bx bx-refresh me-1"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="table-responsive p-3">
            <table id="proposals-table" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;" class="text-center">#</th>
                        <th style="width: 140px;">Proposal No</th>
                        <th>Customer Details</th>
                        <th>Lead Requirement</th>
                        <th>Product Packages</th>
                        <th>Sales Manager</th>
                        <th>Total Amount</th>
                        <th class="text-center" style="width: 90px;">Status</th>
                        <th style="width: 130px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: ADD / EDIT PROPOSAL                 -->
<!-- ========================================== -->
<div class="modal fade" id="proposalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form id="proposalForm" class="modal-content border-0 shadow" style="max-height: 90vh; display: flex; flex-direction: column;">
            @csrf
            <input type="hidden" name="proposal_id" id="proposal_id">

            <div class="modal-header bg-light border-bottom py-3 flex-shrink-0">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm bg-label-primary rounded me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-file fs-4" id="proposalModalIcon"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="proposalModalTitle">Create New Proposal</h5>
                        <small class="text-muted" id="proposalModalSubtitle">Enter proposal details, product packages, and sales manager</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4" style="overflow-y: auto; flex: 1 1 auto;">
                <!-- SECTION 1: CUSTOMER & LEAD INFORMATION -->
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <h6 class="fw-bold text-primary mb-1 border-bottom pb-2">
                            <i class="bx bx-user me-1"></i> Customer & Lead Information
                        </h6>
                    </div>

                    <!-- Customer Name Searchable Dropdown -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="customer_select">
                            Customer Name <span class="text-danger">*</span>
                        </label>
                        <select id="customer_select" class="form-select" style="width: 100%;" required>
                            <option value="">-- Search Customer by Name or Mobile --</option>
                            @if(isset($customers))
                                @foreach($customers as $c)
                                    <option value="{{ $c->customer_id }}" data-id="{{ $c->customer_id }}" data-name="{{ $c->name }}" data-mobile="{{ $c->mobile }}">
                                        {{ $c->name }} {{ !empty($c->mobile) ? '(' . $c->mobile . ')' : '' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <input type="hidden" name="customer_id" id="customer_id">
                        <input type="hidden" name="customer_name" id="customer_name">
                    </div>

                    <!-- Mobile Number -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="customer_mobile">
                            Mobile Number <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bx bx-phone"></i></span>
                            <input type="text" class="form-control" id="customer_mobile" name="customer_mobile" placeholder="Enter 10-digit mobile number" required>
                        </div>
                    </div>

                    <!-- Lead Requirement -->
                    <div class="col-md-12">
                        <label class="form-label fw-semibold" for="lead_requirement_id">
                            Lead Requirement
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bx bx-list-check"></i></span>
                            <select class="form-select" id="lead_requirement_id" name="lead_requirement_id">
                                <option value="">-- Select Lead Requirement --</option>
                                @if(isset($leadRequirements))
                                    @foreach($leadRequirements as $lr)
                                        <option value="{{ $lr->lead_requirements_id }}">{{ $lr->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: PRODUCT PACKAGES (DYNAMIC TABLE) -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <h6 class="fw-bold text-primary mb-0">
                            <i class="bx bx-package me-1"></i> Product Package & Pricing
                        </h6>
                        <!-- ( Add product - button) as explicitly requested -->
                        <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" id="addProductRowBtn">
                            <i class="bx bx-plus me-1"></i> Add Product
                        </button>
                    </div>

                    <div class="table-responsive border rounded bg-white">
                        <table class="table table-bordered align-middle mb-0" id="proposalItemsTable" style="min-width: 900px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 240px;">Product Package <span class="text-danger">*</span></th>
                                    <th style="width: 170px; min-width: 160px;">Paisa (Price ₹) <span class="text-danger">*</span></th>
                                    <th style="width: 135px; min-width: 125px;" class="text-center">Tax (%)</th>
                                    <th style="width: 160px; min-width: 150px;">Tax Amount (₹)</th>
                                    <th style="width: 170px; min-width: 160px;">Amount (₹)</th>
                                    <th style="width: 50px;" class="text-center"><i class="bx bx-trash"></i></th>
                                </tr>
                            </thead>
                            <tbody id="proposalItemsTbody">
                                <!-- Dynamic Rows rendered via JS -->
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th class="text-end fw-bold">Subtotal (Paisa):</th>
                                    <th colspan="4" class="fw-bold text-dark fs-6" id="summarySubtotal">₹0.00</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th class="text-end fw-bold">Total Tax:</th>
                                    <th colspan="4" class="fw-bold text-muted fs-6" id="summaryTax">₹0.00</th>
                                    <th></th>
                                </tr>
                                <tr class="table-primary">
                                    <th class="text-end fw-bolder fs-5 text-primary">Grand Total Amount:</th>
                                    <th colspan="4" class="fw-bolder fs-5 text-primary" id="summaryTotalAmount">₹0.00</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <small class="text-muted d-block mt-2">
                        <i class="bx bx-info-circle me-1"></i> Click <strong>Add Product</strong> to append multiple packages. Amount is auto-calculated as: <code>Paisa + Tax Amount</code>.
                    </small>
                </div>

                <!-- SECTION 3: SALES MANAGER & NOTES -->
                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="fw-bold text-primary mb-1 border-bottom pb-2">
                            <i class="bx bx-user-check me-1"></i> Sales Manager & Notes
                        </h6>
                    </div>

                    <!-- Sales Manager Person -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="sales_manager_id">
                            Sales Manager Person
                        </label>
                        <select class="form-select" id="sales_manager_id" name="sales_manager_id" style="width: 100%;">
                            <option value="" data-id="" data-name="" data-mobile="">-- Select Sales Manager --</option>
                            @if(isset($salesManagers))
                                @foreach($salesManagers as $sm)
                                    <option value="{{ $sm->id }}" data-id="{{ $sm->id }}" data-name="{{ $sm->name }}" data-mobile="{{ $sm->mobile_number }}">
                                        {{ $sm->name }} {{ !empty($sm->mobile_number) ? '(' . $sm->mobile_number . ')' : '' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <input type="hidden" name="sales_manager_name" id="sales_manager_name">
                    </div>

                    <!-- Sales Manager Mobile Number -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="sales_manager_mobile">
                            Sales Manager Mobile Number
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bx bx-mobile"></i></span>
                            <input type="text" class="form-control" id="sales_manager_mobile" name="sales_manager_mobile" placeholder="Auto-filled on selection or enter mobile">
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="notes">
                            Notes / Remarks / Terms
                        </label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Enter proposal notes, warranty details, payment milestones, or additional instructions..."></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light border-top py-3 flex-shrink-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary" id="saveProposalBtn">
                    <i class="bx bx-check me-1"></i> <span id="saveProposalBtnText">Save Proposal</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: VIEW PROPOSAL DETAILS               -->
<!-- ========================================== -->
<div class="modal fade" id="viewProposalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="max-height: 90vh; display: flex; flex-direction: column;">
            <div class="modal-header bg-light border-bottom py-3 flex-shrink-0">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm bg-label-info rounded me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-file fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="view_proposal_number">Proposal Details</h5>
                        <small class="text-muted" id="view_created_at"></small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="javascript:void(0);" id="printProposalModalBtn" class="btn btn-sm btn-outline-primary" target="_blank">
                        <i class="bx bx-printer me-1"></i> Print
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <div class="modal-body p-4" id="viewProposalBody" style="overflow-y: auto; flex: 1 1 auto;">
                <!-- Proposal Metadata Card -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 border rounded bg-light">
                            <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Customer Information</span>
                            <h6 class="fw-bold mb-1" id="view_customer_name">-</h6>
                            <div class="text-muted small mb-1"><i class="bx bx-phone me-1"></i><span id="view_customer_mobile">-</span></div>
                            <div class="text-muted small"><i class="bx bx-tag-alt me-1"></i>Lead Requirement: <span class="badge bg-label-primary" id="view_lead_requirement">-</span></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded bg-light">
                            <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Sales Manager Information</span>
                            <h6 class="fw-bold mb-1" id="view_sales_manager_name">-</h6>
                            <div class="text-muted small mb-1"><i class="bx bx-mobile me-1"></i><span id="view_sales_manager_mobile">-</span></div>
                            <div class="text-muted small"><i class="bx bx-calendar me-1"></i>Created: <span id="view_proposal_date">-</span></div>
                        </div>
                    </div>
                </div>

                <!-- Products Table -->
                <h6 class="fw-bold text-dark mb-2"><i class="bx bx-package me-1 text-primary"></i> Product Packages</h6>
                <div class="table-responsive border rounded mb-3">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Product Package</th>
                                <th class="text-end">Paisa (Price ₹)</th>
                                <th class="text-center">Tax (%)</th>
                                <th class="text-end">Tax Amount (₹)</th>
                                <th class="text-end">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody id="view_items_tbody">
                            <!-- Items populated via JS -->
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end fw-bold">Subtotal:</th>
                                <th class="text-end fw-bold" id="view_subtotal">₹0.00</th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-end fw-bold">Total Tax:</th>
                                <th class="text-end fw-bold text-muted" id="view_total_tax">₹0.00</th>
                            </tr>
                            <tr class="table-primary">
                                <th colspan="5" class="text-end fw-bolder fs-6 text-primary">Grand Total:</th>
                                <th class="text-end fw-bolder fs-6 text-primary" id="view_total_amount">₹0.00</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Notes Section -->
                <div id="view_notes_container" class="mt-3">
                    <h6 class="fw-bold text-dark mb-1"><i class="bx bx-note me-1 text-primary"></i> Notes / Terms</h6>
                    <div class="p-3 bg-light border rounded text-muted small" id="view_notes" style="white-space: pre-wrap;">-</div>
                </div>
            </div>

            <div class="modal-footer bg-light border-top py-2 flex-shrink-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: DELETE CONFIRMATION                 -->
<!-- ========================================== -->
<div class="modal fade" id="deleteProposalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="avatar avatar-lg bg-label-danger mx-auto mb-3 rounded-circle p-2">
                    <i class="bx bx-trash fs-1 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-1">Delete Proposal?</h5>
                <p class="text-muted small mb-4">Are you sure you want to delete proposal <strong id="delete_proposal_number"></strong>? This action cannot be undone.</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm px-3" id="confirmDeleteProposalBtn">
                        <i class="bx bx-trash me-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
