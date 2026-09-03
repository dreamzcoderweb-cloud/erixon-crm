@extends('layouts.master')

@section('title', 'Demo Process Management')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #d9ade5 !important;
            border-radius: 0.375rem !important;
            min-height: 38px !important;
            padding: 3px 8px !important;
            background-color: #fff !important;
        }
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: var(--theme-color, #6747c7) !important;
            box-shadow: 0 0 0 0.25rem rgba(103, 71, 199, 0.25) !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: var(--theme-color, #6747c7) !important;
            border: none !important;
            color: #ffffff !important;
            border-radius: 0.25rem !important;
            padding: 2px 8px !important;
            margin-top: 3px !important;
            margin-bottom: 3px !important;
            font-size: 0.8125rem !important;
            display: inline-flex !important;
            align-items: center !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #ffffff !important;
            margin-right: 6px !important;
            border: none !important;
            background: transparent !important;
            font-weight: bold !important;
            font-size: 1rem !important;
            line-height: 1 !important;
            cursor: pointer !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #ffd1d1 !important;
            background: transparent !important;
        }
        .select2-dropdown {
            z-index: 1095 !important;
            border-color: #d9ade5 !important;
            box-shadow: 0 0.25rem 1rem rgba(161, 172, 184, 0.45) !important;
            border-radius: 0.375rem !important;
        }
        .select2-results__option--highlighted[aria-selected] {
            background-color: var(--theme-color, #6747c7) !important;
            color: #fff !important;
        }
        .dt-layout-start, div.dt-container div.dt-layout-cell.dt-layout-start {
            display: flex !important;
            align-items: center !important;
            gap: 1rem !important;
            flex-wrap: wrap !important;
        }
        .dt-buttons, div.dt-buttons {
            margin-left: 0.75rem !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.25rem !important;
        }

        /* Select2 Validation Styling */
        .select2-container.is-invalid .select2-selection,
        .is-invalid + .select2-container .select2-selection {
            border-color: #ff3e1d !important;
        }
        .is-invalid ~ .invalid-feedback {
            display: block !important;
            color: #ff3e1d !important;
            font-size: 0.8125rem !important;
            margin-top: 0.25rem !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div id="alert-container"></div>

        <div class="card">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="card-header p-0 m-0"><i class="bx bx-slideshow me-2"></i>Demo Process</h5>
                @can('demo-processes.create')
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDemoProcessModal">
                        <i class="bx bx-plus me-1"></i> Add Demo Process
                    </button>
                @endcan
            </div>

            <!-- Demo Process Filter Bar -->
            <div class="p-3 bg-light border-bottom">
                <form id="demoProcessFilterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12">
                            <label class="form-label fw-semibold d-block">Date Period</label>
                            <div class="btn-group btn-group-sm" role="group" id="demoProcessPeriodBtnGroup">
                                <button type="button" class="btn btn-outline-primary btn-demo-period active" data-period="all">All Time</button>
                                <button type="button" class="btn btn-outline-primary btn-demo-period" data-period="daily">Daily</button>
                                <button type="button" class="btn btn-outline-primary btn-demo-period" data-period="weekly">Weekly</button>
                                <button type="button" class="btn btn-outline-primary btn-demo-period" data-period="monthly">Monthly</button>
                                <button type="button" class="btn btn-outline-primary btn-demo-period" data-period="custom">Custom</button>
                            </div>
                            <input type="hidden" name="filter_type" id="demo_filter_period" value="all">
                        </div>

                        <div class="col-md-3 demo-filter-date-group d-none" id="demo_group_daily">
                            <label class="form-label fw-semibold">Date</label>
                            <input type="date" name="date" id="demo_filter_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-3 demo-filter-date-group d-none" id="demo_group_monthly">
                            <label class="form-label fw-semibold">Month</label>
                            <input type="month" name="month" id="demo_filter_month" class="form-control form-control-sm" value="{{ date('Y-m') }}">
                        </div>

                        <div class="col-md-3 demo-filter-date-group d-none" id="demo_group_custom_start">
                            <label class="form-label fw-semibold">From Date</label>
                            <input type="date" name="start_date" id="demo_filter_start_date" class="form-control form-control-sm" value="{{ date('Y-m-01') }}">
                        </div>

                        <div class="col-md-3 demo-filter-date-group d-none" id="demo_group_custom_end">
                            <label class="form-label fw-semibold">To Date</label>
                            <input type="date" name="end_date" id="demo_filter_end_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" id="demo_filter_status" class="form-select form-select-sm">
                                <option value="">-- All Statuses --</option>
                                <option value="Pending">Pending</option>
                                <option value="Finished">Finished</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Created By</label>
                            <select name="created_by" id="demo_filter_created_by" class="form-select form-select-sm">
                                <option value="">-- All Staff --</option>
                                @if(isset($staffList) && count($staffList) > 0)
                                    @foreach ($staffList as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="bx bx-filter-alt me-1"></i> Apply Filter
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="resetDemoFilterBtn" title="Reset Filters">
                                    <i class="bx bx-refresh me-1"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive text-nowrap p-3">
                <table id="demo-processes-table" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Customer Name</th>
                            <th>Lead Source </th>
                            <th>Demo Schedule</th>
                            <th>Customer Type</th>
                            <th>Created By (Sales)</th>
                            <th>Assigned Team (PM / Support)</th>
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

    <!-- Add Demo Process Modal -->
    <div class="modal fade" id="addDemoProcessModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-plus me-1"></i> Add Demo Process</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addDemoProcessForm" novalidate>
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Customer Name <span class="text-danger">*</span></label>
                                <select name="customer_name" id="add_customer_name" class="form-select select2-search" required>
                                    <option value="">-- Select Customer --</option>
                                    @if(isset($customers) && count($customers) > 0)
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->name }}" data-phone="{{ $customer->mobile }}" data-type="{{ $customer->customer_type }}">
                                                {{ $customer->name }} ({{ $customer->mobile }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Customer Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" name="customer_phone" id="add_customer_phone" class="form-control" placeholder="e.g. 9876543210" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Lead Source</label>
                                <select name="lead_source_id" id="add_lead_source_id" class="form-select">
                                    <option value="">-- Select Lead Source --</option>
                                    @foreach ($leadSources as $ls)
                                        <option value="{{ $ls->lead_sources_id }}">{{ $ls->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Demo Date <span class="text-danger">*</span></label>
                                <input type="date" name="demo_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Demo Timing <span class="text-danger">*</span></label>
                                <input type="time" name="demo_time" class="form-control" value="10:00" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Customer Type</label>
                                <input type="text" name="customer_type" id="add_customer_type" class="form-control" placeholder="Autofilled from customer selection">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Assigned By <span class="text-muted">(Product Manager)</span></label>
                                <select name="assigned_by" class="form-select">
                                    <option value="">-- Select Product Manager --</option>
                                    @foreach ($productManagers as $pm)
                                        <option value="{{ $pm->id }}">{{ $pm->name }} ({{ $pm->email }})</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Sub Assigned By <span class="text-muted">(Support Team)</span></label>
                                <select name="sub_assigned_by" class="form-select">
                                    <option value="">-- Select Support Team --</option>
                                    @foreach ($supportTeam as $st)
                                        <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->email }})</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Remarks / Comments</label>
                                <textarea name="remarks" class="form-control" rows="2" placeholder="Add optional demo notes or customer requirements..."></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="addDemoProcessSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Save Demo Process
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Demo Process Modal -->
    <div class="modal fade" id="editDemoProcessModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-edit-alt me-1"></i> Edit Demo Process</h5>
                    <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editDemoProcessForm" novalidate>
                    @csrf
                    <input type="hidden" id="edit_demo_process_id">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Customer Name <span class="text-danger">*</span></label>
                                <select name="customer_name" id="edit_customer_name" class="form-select select2-search" required>
                                    <option value="">-- Select Customer --</option>
                                    @if(isset($customers) && count($customers) > 0)
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->name }}" data-phone="{{ $customer->mobile }}" data-type="{{ $customer->customer_type }}">
                                                {{ $customer->name }} ({{ $customer->mobile }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Customer Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" name="customer_phone" id="edit_customer_phone" class="form-control" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Lead Source</label>
                                <select name="lead_source_id" id="edit_lead_source_id" class="form-select">
                                    <option value="">-- Select Lead Source --</option>
                                    @foreach ($leadSources as $ls)
                                        <option value="{{ $ls->lead_sources_id }}">{{ $ls->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>



                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Demo Date <span class="text-danger">*</span></label>
                                <input type="date" name="demo_date" id="edit_demo_date" class="form-control" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Demo Timing <span class="text-danger">*</span></label>
                                <input type="time" name="demo_time" id="edit_demo_time" class="form-control" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Customer Type</label>
                                <input type="text" name="customer_type" id="edit_customer_type" class="form-control" placeholder="Customer type">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" id="edit_status" class="form-select" required>
                                    <option value="Pending">Pending </option>
                                    <option value="Finished">Finished </option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Assigned By <span class="text-muted">(PM)</span></label>
                                <select name="assigned_by" id="edit_assigned_by" class="form-select">
                                    <option value="">-- Select Product Manager --</option>
                                    @foreach ($productManagers as $pm)
                                        <option value="{{ $pm->id }}">{{ $pm->name }} ({{ $pm->email }})</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Sub Assigned By <span class="text-muted">(Support)</span></label>
                                <select name="sub_assigned_by" id="edit_sub_assigned_by" class="form-select">
                                    <option value="">-- Select Support Team --</option>
                                    @foreach ($supportTeam as $st)
                                        <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->email }})</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Remarks / Comments</label>
                                <textarea name="remarks" id="edit_remarks" class="form-control" rows="2"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="editDemoProcessSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Update Demo Process
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteDemoProcessModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white"><i class="bx bx-trash me-1"></i> Delete Demo Process</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p>Are you sure you want to delete this Demo Process record for <strong id="delete_customer_name"></strong>?</p>
                </div>
                <div class="modal-footer gap-2 border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteDemoProcessBtn">Delete Record</button>
                </div>
            </div>
        </div>
    </div>
@endsection

