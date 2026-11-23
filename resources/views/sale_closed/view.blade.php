@extends('layouts.master')
@section('title', 'Sale Closed Leads - Super Admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div id="alert-container"></div>

    <!-- Header Breadcrumb & Title -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold py-1 mb-1">
                <span class="text-muted fw-light">Leads Management /</span> Sale Closed
            </h4>
            <p class="text-muted mb-0 small">Comprehensive list of all closed sales with Daily, Monthly, and Yearly period tracking</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.leads.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bx bx-arrow-back me-1"></i> All Leads
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Sale Closed -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card shadow-sm border-start border-success border-4 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <small class="text-muted text-uppercase fw-semibold">Total Closed</small>
                        <span class="badge bg-label-success p-1"><i class="bx bx-check-double"></i></span>
                    </div>
                    <h4 class="mb-0 text-success fw-bold" id="kpi_total_count">0</h4>
                    <small class="text-muted d-block mt-1">₹<span id="kpi_total_amount">0.00</span></small>
                </div>
            </div>
        </div>

        <!-- Today Closed (Daily) -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card shadow-sm border-start border-info border-4 h-100 kpi-period-card" data-period="daily" style="cursor: pointer;" title="Click to filter by Today">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <small class="text-muted text-uppercase fw-semibold">Today (Daily)</small>
                        <span class="badge bg-label-info p-1"><i class="bx bx-calendar-event"></i></span>
                    </div>
                    <h4 class="mb-0 text-info fw-bold" id="kpi_today_count">0</h4>
                    <small class="text-muted d-block mt-1">₹<span id="kpi_today_amount">0.00</span></small>
                </div>
            </div>
        </div>

        <!-- This Month Closed (Monthly) -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card shadow-sm border-start border-primary border-4 h-100 kpi-period-card" data-period="monthly" style="cursor: pointer;" title="Click to filter by This Month">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <small class="text-muted text-uppercase fw-semibold">This Month</small>
                        <span class="badge bg-label-primary p-1"><i class="bx bx-calendar"></i></span>
                    </div>
                    <h4 class="mb-0 text-primary fw-bold" id="kpi_month_count">0</h4>
                    <small class="text-muted d-block mt-1">₹<span id="kpi_month_amount">0.00</span></small>
                </div>
            </div>
        </div>

        <!-- This Year Closed (Yearly) -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card shadow-sm border-start border-warning border-4 h-100 kpi-period-card" data-period="yearly" style="cursor: pointer;" title="Click to filter by This Year">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <small class="text-muted text-uppercase fw-semibold">This Year</small>
                        <span class="badge bg-label-warning p-1"><i class="bx bx-calendar-star"></i></span>
                    </div>
                    <h4 class="mb-0 text-warning fw-bold" id="kpi_year_count">0</h4>
                    <small class="text-muted d-block mt-1">₹<span id="kpi_year_amount">0.00</span></small>
                </div>
            </div>
        </div>

        <!-- Current Filtered Total -->
        <div class="col-12 col-md-8 col-xl-4">
            <div class="card shadow-sm border-start border-dark border-4 h-100 bg-light">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <small class="text-muted text-uppercase fw-semibold">Active Filter Summary</small>
                        <span class="badge bg-label-dark p-1"><i class="bx bx-filter-alt"></i></span>
                    </div>
                    <div class="d-flex align-items-baseline gap-3 mt-1">
                        <div>
                            <span class="text-muted small">Leads:</span>
                            <span class="fs-4 fw-bold text-dark" id="kpi_filtered_count">0</span>
                        </div>
                        <div class="border-start ps-3">
                            <span class="text-muted small">Total Value:</span>
                            <span class="fs-4 fw-bold text-success">₹<span id="kpi_filtered_amount">0.00</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card">
        <!-- Filter Header -->
        <div class="p-3 bg-light border-bottom">
            <form id="saleClosedFilterForm" onsubmit="return false;">
                <!-- Row 1: Period Selection Buttons & Inputs -->
                <div class="row g-3 align-items-end mb-3">
                    <div class="col-md-auto">
                        <label class="form-label fw-semibold d-block small mb-1">Period Filter</label>
                        <div class="btn-group btn-group-sm" role="group" id="periodBtnGroup">
                            <button type="button" class="btn btn-outline-primary btn-sale-period active" data-period="all">
                                <i class="bx bx-infinite me-1"></i> All Time
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sale-period" data-period="daily">
                                <i class="bx bx-calendar-event me-1"></i> Daily
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sale-period" data-period="monthly">
                                <i class="bx bx-calendar me-1"></i> Monthly
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sale-period" data-period="yearly">
                                <i class="bx bx-calendar-star me-1"></i> Yearly
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sale-period" data-period="custom">
                                <i class="bx bx-slider-alt me-1"></i> Custom Range
                            </button>
                        </div>
                        <input type="hidden" name="filter_type" id="filter_type" value="all">
                    </div>

                    <!-- Daily Date Input -->
                    <div class="col-md-3 period-input-group d-none" id="group_daily">
                        <label class="form-label fw-semibold small mb-1">Select Date</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                            <input type="date" name="date" id="filter_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <!-- Monthly Month Input -->
                    <div class="col-md-3 period-input-group d-none" id="group_monthly">
                        <label class="form-label fw-semibold small mb-1">Select Month</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                            <input type="month" name="month" id="filter_month" class="form-control" value="{{ date('Y-m') }}">
                        </div>
                    </div>

                    <!-- Yearly Year Input -->
                    <div class="col-md-2 period-input-group d-none" id="group_yearly">
                        <label class="form-label fw-semibold small mb-1">Select Year</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bx bx-calendar-star"></i></span>
                            <select name="year" id="filter_year" class="form-select">
                                @foreach($years as $yr)
                                    <option value="{{ $yr }}" {{ $yr == date('Y') ? 'selected' : '' }}>{{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Custom Date Range -->
                    <div class="col-md-2 period-input-group d-none" id="group_custom_start">
                        <label class="form-label fw-semibold small mb-1">From Date</label>
                        <input type="date" name="start_date" id="filter_start_date" class="form-control form-control-sm" value="{{ date('Y-m-01') }}">
                    </div>
                    <div class="col-md-2 period-input-group d-none" id="group_custom_end">
                        <label class="form-label fw-semibold small mb-1">To Date</label>
                        <input type="date" name="end_date" id="filter_end_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <!-- Row 2: Secondary Dropdown Filters -->
                <div class="row g-2 align-items-end">
                    <!-- Branch Filter -->
                    <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                        <label class="form-label fw-semibold small mb-1">Branch</label>
                        <select name="branch_id" id="filter_branch_id" class="form-select form-select-sm">
                            <option value="">All Branches</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Team Filter -->
                    <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                        <label class="form-label fw-semibold small mb-1">Team</label>
                        <select name="team_id" id="filter_team_id" class="form-select form-select-sm">
                            <option value="">All Teams</option>
                            @foreach($teams as $t)
                                <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Assigned Staff Filter -->
                    <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                        <label class="form-label fw-semibold small mb-1">Assigned Staff</label>
                        <select name="assigned_to" id="filter_assigned_to" class="form-select form-select-sm">
                            <option value="">All Staff</option>
                            @foreach($staffs as $st)
                                <option value="{{ $st->id }}">{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Requirement Filter -->
                    <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                        <label class="form-label fw-semibold small mb-1">Requirement</label>
                        <select name="lead_requirement_id" id="filter_requirement_id" class="form-select form-select-sm">
                            <option value="">All Requirements</option>
                            @foreach($leadRequirements as $lr)
                                <option value="{{ $lr->lead_requirements_id }}">{{ $lr->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Source Filter -->
                    <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                        <label class="form-label fw-semibold small mb-1">Lead Source</label>
                        <select name="lead_source_id" id="filter_source_id" class="form-select form-select-sm">
                            <option value="">All Sources</option>
                            @foreach($leadSources as $ls)
                                <option value="{{ $ls->lead_sources_id }}">{{ $ls->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <div class="col-12 col-sm-6 col-md-3 col-lg-2 d-flex gap-2">
                        <button type="button" class="btn btn-secondary btn-sm w-100" id="btnResetFilters">
                            <i class="bx bx-reset me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table View -->
        <div class="card-datatable table-responsive p-3">
            <table class="table table-hover table-striped border-top" id="saleClosedTable" style="width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Customer</th>
                        <th>Lead Title</th>
                        <th>Requirement</th>
                        <th>Branch & Team</th>
                        <th>Sale Amount</th>
                        <th>Assigned Staff</th>
                        <th>Closed Date</th>
                        <th class="text-center" style="width: 80px;">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Lead Details Modal -->
<div class="modal fade" id="viewSaleClosedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-label-success">
                <h5 class="modal-title"><i class="bx bx-check-shield me-2"></i> Sale Closed Lead Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-0">Customer Name</label>
                        <p class="fw-bold mb-2" id="modal_customer_name">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-0">Customer Type</label>
                        <p class="mb-2" id="modal_customer_type">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-0">Mobile Number</label>
                        <p class="mb-2" id="modal_mobile">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-0">Email Address</label>
                        <p class="mb-2" id="modal_email">-</p>
                    </div>
                    <hr class="my-1">
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-0">Lead Title</label>
                        <p class="fw-bold mb-2" id="modal_lead_title">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-0">Sale / Expected Amount</label>
                        <p class="fw-bold text-success fs-5 mb-2" id="modal_amount">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-0">Lead Requirement</label>
                        <p class="mb-2" id="modal_requirement">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-0">Lead Source</label>
                        <p class="mb-2" id="modal_source">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-0">Branch</label>
                        <p class="mb-2" id="modal_branch">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-0">Team</label>
                        <p class="mb-2" id="modal_team">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-0">Assigned Staff</label>
                        <p class="mb-2" id="modal_assigned">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-0">Date Created / Closed</label>
                        <p class="mb-2" id="modal_created_at">-</p>
                    </div>
                    <div class="col-12" id="modal_desc_container">
                        <label class="form-label text-muted small mb-0">Description / Notes</label>
                        <div class="p-2 bg-light rounded" id="modal_description">-</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Current cache of loaded rows for quick modal preview
    var leadRowsData = {};

    // 1. Initialize DataTable
    var table = $('#saleClosedTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "{{ route('admin.sale-closed.data') }}",
            type: "GET",
            data: function(d) {
                d.filter_type         = $('#filter_type').val();
                d.date                = $('#filter_date').val();
                d.month               = $('#filter_month').val();
                d.year                = $('#filter_year').val();
                d.start_date          = $('#filter_start_date').val();
                d.end_date            = $('#filter_end_date').val();
                d.branch_id           = $('#filter_branch_id').val();
                d.team_id             = $('#filter_team_id').val();
                d.assigned_to         = $('#filter_assigned_to').val();
                d.lead_requirement_id = $('#filter_requirement_id').val();
                d.lead_source_id      = $('#filter_source_id').val();
            },
            dataSrc: function(json) {
                // Update KPI Cards from server response
                if (json.kpi) {
                    $('#kpi_total_count').text(json.kpi.total_count);
                    $('#kpi_total_amount').text(json.kpi.total_amount);
                    $('#kpi_today_count').text(json.kpi.today_count);
                    $('#kpi_today_amount').text(json.kpi.today_amount);
                    $('#kpi_month_count').text(json.kpi.month_count);
                    $('#kpi_month_amount').text(json.kpi.month_amount);
                    $('#kpi_year_count').text(json.kpi.year_count);
                    $('#kpi_year_amount').text(json.kpi.year_amount);
                    $('#kpi_filtered_count').text(json.kpi.filtered_count);
                    $('#kpi_filtered_amount').text(json.kpi.filtered_amount);
                }

                // Cache rows
                leadRowsData = {};
                var list = json.data || [];
                list.forEach(function(item) {
                    leadRowsData[item.lead_id] = item;
                });

                return list;
            }
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    var custName = row.customer_name || (row.customer ? row.customer.name : 'Unknown');
                    var custType = (row.customer_type || (row.customer ? row.customer.customer_type : '')).toLowerCase();
                    var badgeType = custType === 'reseller' 
                        ? '<span class="badge bg-label-info text-capitalize me-1">Reseller</span>' 
                        : '<span class="badge bg-label-primary text-capitalize me-1">User</span>';
                    var mobile = row.mobile || (row.customer ? row.customer.mobile : '');
                    var email = row.email || (row.customer ? row.customer.email : '');
                    
                    var out = '<div><strong>' + custName + '</strong> ' + badgeType + '</div>';
                    if (mobile) {
                        out += '<small class="text-muted d-block"><i class="bx bx-phone me-1"></i><a href="tel:' + mobile + '">' + mobile + '</a></small>';
                    }
                    if (email) {
                        out += '<small class="text-muted d-block"><i class="bx bx-envelope me-1"></i>' + email + '</small>';
                    }
                    return out;
                }
            },
            {
                data: 'lead_title',
                render: function(data) {
                    return data ? '<span class="fw-semibold">' + data + '</span>' : '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'lead_requirement',
                render: function(data) {
                    if (data && data.name) {
                        return '<span class="badge bg-label-primary">' + data.name + '</span>';
                    }
                    return '<span class="text-muted">-</span>';
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    var branchHtml = '';
                    if (row.branches && row.branches.length > 0) {
                        branchHtml = row.branches.map(function(b) {
                            return '<span class="badge bg-label-secondary me-1 mb-1"><i class="bx bx-buildings me-1"></i>' + b.name + '</span>';
                        }).join(' ');
                    } else if (row.branch) {
                        branchHtml = '<span class="badge bg-label-secondary me-1 mb-1"><i class="bx bx-buildings me-1"></i>' + row.branch.name + '</span>';
                    } else {
                        branchHtml = '<span class="badge bg-label-secondary mb-1">-</span>';
                    }

                    var teamHtml = '';
                    if (row.teams && row.teams.length > 0) {
                        teamHtml = '<br>' + row.teams.map(function(t) {
                            return '<span class="badge bg-label-info me-1"><i class="bx bx-group me-1"></i>' + t.name + '</span>';
                        }).join(' ');
                    } else if (row.team) {
                        teamHtml = '<br><span class="badge bg-label-info"><i class="bx bx-group me-1"></i>' + row.team.name + '</span>';
                    }

                    return branchHtml + teamHtml;
                }
            },
            {
                data: 'expected_amount',
                render: function(data) {
                    var val = parseFloat(data || 0);
                    return '<span class="fw-bold text-success">₹' + val.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</span>';
                }
            },
            {
                data: 'assigned_user',
                render: function(data) {
                    if (data && data.name) {
                        return '<span class="badge bg-label-dark"><i class="bx bx-user me-1"></i>' + data.name + '</span>';
                    }
                    return '<span class="badge bg-label-secondary">Unassigned</span>';
                }
            },
            {
                data: 'created_at',
                render: function(data) {
                    if (!data) return '<span class="text-muted">-</span>';
                    if (typeof formatDate === 'function') {
                        return '<span class="text-nowrap">' + formatDate(data) + '</span>';
                    }
                    var dt = new Date(data);
                    return '<span class="text-nowrap">' + dt.toLocaleDateString('en-GB') + '</span>';
                }
            },
            {
                data: null,
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    return '<button type="button" class="btn btn-sm btn-icon btn-outline-primary btn-view-lead" data-id="' + row.lead_id + '" title="View Details">' +
                           '<i class="bx bx-show"></i>' +
                           '</button>';
                }
            }
        ],
        order: [[0, 'asc']],
        pageLength: 25,
        dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="bx bx-file me-1"></i> Export Excel',
                className: 'btn btn-outline-success btn-sm me-1',
                title: 'Sale_Closed_Leads_' + new Date().toISOString().slice(0, 10),
                exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] }
            },
            {
                extend: 'print',
                text: '<i class="bx bx-printer me-1"></i> Print',
                className: 'btn btn-outline-secondary btn-sm',
                exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] }
            }
        ]
    });

    // 2. Period Button Group Switching (Daily, Monthly, Yearly, Custom, All)
    function switchPeriod(period) {
        $('.btn-sale-period').removeClass('active');
        $('.btn-sale-period[data-period="' + period + '"]').addClass('active');
        $('#filter_type').val(period);

        // Hide all period input groups first
        $('.period-input-group').addClass('d-none');

        // Show target input group
        if (period === 'daily') {
            $('#group_daily').removeClass('d-none');
        } else if (period === 'monthly') {
            $('#group_monthly').removeClass('d-none');
        } else if (period === 'yearly') {
            $('#group_yearly').removeClass('d-none');
        } else if (period === 'custom') {
            $('#group_custom_start').removeClass('d-none');
            $('#group_custom_end').removeClass('d-none');
        }

        // Reload data
        table.ajax.reload();
    }

    $(document).on('click', '.btn-sale-period', function() {
        var period = $(this).data('period');
        switchPeriod(period);
    });

    // KPI card click shortcut to filter by that period
    $(document).on('click', '.kpi-period-card', function() {
        var period = $(this).data('period');
        if (period) {
            switchPeriod(period);
        }
    });

    // 3. Trigger reload on any filter input change
    $('#filter_date, #filter_month, #filter_year, #filter_start_date, #filter_end_date, #filter_assigned_to, #filter_requirement_id, #filter_source_id, #filter_team_id').on('change', function() {
        table.ajax.reload();
    });

    // 4. Dynamic Team Loading when Branch changes
    $('#filter_branch_id').on('change', function() {
        var branchId = $(this).val();
        var teamSelect = $('#filter_team_id');
        teamSelect.html('<option value="">All Teams</option>');

        if (branchId) {
            $.ajax({
                url: "{{ url('admin/teams/by-branch') }}/" + branchId,
                type: "GET",
                dataType: "json",
                success: function(res) {
                    if (res && res.data && res.data.length > 0) {
                        res.data.forEach(function(tm) {
                            teamSelect.append('<option value="' + tm.id + '">' + tm.name + ' (' + tm.code + ')</option>');
                        });
                    }
                    table.ajax.reload();
                },
                error: function() {
                    table.ajax.reload();
                }
            });
        } else {
            table.ajax.reload();
        }
    });

    // 5. Reset Filter Button
    $('#btnResetFilters').on('click', function() {
        $('#saleClosedFilterForm')[0].reset();
        $('#filter_type').val('all');
        $('.btn-sale-period').removeClass('active');
        $('.btn-sale-period[data-period="all"]').addClass('active');
        $('.period-input-group').addClass('d-none');
        table.ajax.reload();
    });

    // 6. View Details Modal Trigger
    $(document).on('click', '.btn-view-lead', function() {
        var id = $(this).data('id');
        var lead = leadRowsData[id];
        if (!lead) return;

        var custName = lead.customer_name || (lead.customer ? lead.customer.name : 'Unknown');
        var custType = (lead.customer_type || (lead.customer ? lead.customer.customer_type : '')).toUpperCase();
        var mobile = lead.mobile || (lead.customer ? lead.customer.mobile : '-');
        var email = lead.email || (lead.customer ? lead.customer.email : '-');
        var val = parseFloat(lead.expected_amount || 0);

        $('#modal_customer_name').text(custName);
        $('#modal_customer_type').html('<span class="badge bg-label-info">' + custType + '</span>');
        $('#modal_mobile').text(mobile);
        $('#modal_email').text(email);
        $('#modal_lead_title').text(lead.lead_title || '-');
        $('#modal_amount').text('₹' + val.toLocaleString('en-IN', { minimumFractionDigits: 2 }));
        $('#modal_requirement').text(lead.lead_requirement ? lead.lead_requirement.name : '-');
        $('#modal_source').text(lead.lead_source ? lead.lead_source.name : '-');
        var branchText = '-';
        if (lead.branches && lead.branches.length > 0) {
            branchText = lead.branches.map(function(b) { return b.name + ' (' + (b.code || '') + ')'; }).join(', ');
        } else if (lead.branch) {
            branchText = lead.branch.name + ' (' + lead.branch.code + ')';
        }
        $('#modal_branch').text(branchText);

        var teamText = '-';
        if (lead.teams && lead.teams.length > 0) {
            teamText = lead.teams.map(function(t) { return t.name + ' (' + (t.code || '') + ')'; }).join(', ');
        } else if (lead.team) {
            teamText = lead.team.name + ' (' + lead.team.code + ')';
        }
        $('#modal_team').text(teamText);
        $('#modal_assigned').text(lead.assigned_user ? lead.assigned_user.name : 'Unassigned');

        var createdText = lead.created_at;
        if (typeof formatDate === 'function' && lead.created_at) {
            createdText = formatDate(lead.created_at);
        }
        $('#modal_created_at').text(createdText || '-');

        if (lead.description) {
            $('#modal_desc_container').show();
            $('#modal_description').text(lead.description);
        } else {
            $('#modal_desc_container').hide();
        }

        $('#viewSaleClosedModal').modal('show');
    });
});
</script>
@endpush
