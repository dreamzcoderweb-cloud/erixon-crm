@extends('layouts.master')
@section('title', 'Customers - Super Admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div id="alert-container"></div>
         <!-- Analytics KPI Cards -->
            <div class="row g-3 mb-4">
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card shadow-sm border-start border-primary border-4 kpi-card-clickable" id="kpi_card_resellers" style="cursor: pointer;" title="Click to filter Resellers">
                    <div class="card-body p-3 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block">Resellers</small>
                        <h3 class="mb-0 text-primary fw-bold mt-1" id="kpi_resellers">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card shadow-sm border-start border-warning border-4 kpi-card-clickable" id="kpi_card_users" style="cursor: pointer;" title="Click to filter Users">
                    <div class="card-body p-3 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block">Users</small>
                        <h3 class="mb-0 text-warning fw-bold mt-1" id="kpi_users">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card shadow-sm border-start border-warning border-4 kpi-card-clickable" id="kpi_card_staffs" style="cursor: pointer;" title="Click to filter Staff">
                    <div class="card-body p-3 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block">Staff created count</small>
                        <h3 class="mb-0 text-warning fw-bold mt-1" id="kpi_staffs">0</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
        <div class="card">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="card-header p-0 m-0"><i class="bx bx-user me-2"></i>Customers Management</h5>
                <div class="d-flex gap-2">
                    @can('customers.create')
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#importCustomerModal">
                            <i class="bx bx-upload me-1"></i> Import Customers
                        </button>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                            <i class="bx bx-plus me-1"></i> Add Customer
                        </button>
                    @endcan
                </div>
            </div>

            <!-- Customer Filter Bar -->
            <div class="p-3 bg-light border-bottom">
                <form id="customerFilterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12">
                            <label class="form-label fw-semibold d-block">Date Period</label>
                            <div class="btn-group btn-group-sm" role="group" id="periodBtnGroup">
                                <button type="button" class="btn btn-outline-primary btn-customer-period active" data-period="all">All Time</button>
                                <button type="button" class="btn btn-outline-primary btn-customer-period" data-period="daily">Daily</button>
                                <button type="button" class="btn btn-outline-primary btn-customer-period" data-period="weekly">Weekly</button>
                                <button type="button" class="btn btn-outline-primary btn-customer-period" data-period="monthly">Monthly</button>
                                <button type="button" class="btn btn-outline-primary btn-customer-period" data-period="custom">Custom</button>
                            </div>
                            <input type="hidden" name="filter_type" id="customer_filter_period" value="all">
                        </div>

                        <div class="col-md-3 customer-filter-date-group d-none" id="customer_group_daily">
                            <label class="form-label fw-semibold">Date</label>
                            <input type="date" name="date" id="customer_filter_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-3 customer-filter-date-group d-none" id="customer_group_monthly">
                            <label class="form-label fw-semibold">Month</label>
                            <input type="month" name="month" id="customer_filter_month" class="form-control form-control-sm" value="{{ date('Y-m') }}">
                        </div>

                        <div class="col-md-3 customer-filter-date-group d-none" id="customer_group_custom_start">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="date" name="start_date" id="customer_filter_start_date" class="form-control form-control-sm" value="{{ date('Y-m-01') }}">
                        </div>

                        <div class="col-md-3 customer-filter-date-group d-none" id="customer_group_custom_end">
                            <label class="form-label fw-semibold">End Date</label>
                            <input type="date" name="end_date" id="customer_filter_end_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Created By</label>
                            <select name="created_by" id="customer_filter_created_by" class="form-select form-select-sm">
                                <option value="">-- All Staff --</option>
                                @if(isset($staffs) && count($staffs) > 0)
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Customer Type</label>
                            <select name="customer_type" id="customer_filter_type" class="form-select form-select-sm">
                                <option value="">-- All Types --</option>
                                <option value="user">User</option>
                                <option value="reseller">Reseller</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" id="customer_filter_status" class="form-select form-select-sm">
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
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="resetCustomerFilterBtn" title="Reset Filters">
                                    <i class="bx bx-refresh me-1"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table-responsive text-nowrap p-3">
                <table id="customers-table" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Customer Type</th>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>City / State</th>
                            <th>Created Date</th>
                            <th>Created By</th>
                            <th class="text-center">Status</th>
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

    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="addCustomerForm" method="POST" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-user-plus me-1"></i> Add New Customer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Customer Type <span class="text-danger">*</span></label>
                                <select name="customer_type" class="form-select" required>
                                    <option value="user">User</option>
                                    <option value="reseller">Reseller</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Enter full name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company_name" class="form-control" placeholder="Enter company name">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                <input type="text" name="mobile" class="form-control" placeholder="Enter mobile number" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="Enter email address">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Alternate Mobile</label>
                                <input type="text" name="alternate_mobile" class="form-control" placeholder="Enter alternate mobile">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="2" placeholder="Enter address"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" placeholder="City">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">State</label>
                                <input type="text" name="state" class="form-control" placeholder="State">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Country</label>
                                <input type="text" name="country" class="form-control" placeholder="Country" value="India">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pincode</label>
                                <input type="text" name="pincode" class="form-control" placeholder="Pincode">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="addCustomerSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Save Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Customer Modal -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="editCustomerForm" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="customer_id" id="edit_customer_id">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-edit me-1"></i> Edit Customer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Customer Type <span class="text-danger">*</span></label>
                                <select name="customer_type" id="edit_customer_type" class="form-select" required>
                                    <option value="user">User</option>
                                    <option value="reseller">Reseller</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="edit_name" class="form-control" placeholder="Enter full name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company_name" id="edit_company_name" class="form-control" placeholder="Enter company name">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                <input type="text" name="mobile" id="edit_mobile" class="form-control" placeholder="Enter mobile number" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" id="edit_email" class="form-control" placeholder="Enter email address">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Alternate Mobile</label>
                                <input type="text" name="alternate_mobile" id="edit_alternate_mobile" class="form-control" placeholder="Enter alternate mobile">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" id="edit_address" class="form-control" rows="2" placeholder="Enter address"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">City</label>
                                <input type="text" name="city" id="edit_city" class="form-control" placeholder="City">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">State</label>
                                <input type="text" name="state" id="edit_state" class="form-control" placeholder="State">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Country</label>
                                <input type="text" name="country" id="edit_country" class="form-control" placeholder="Country">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pincode</label>
                                <input type="text" name="pincode" id="edit_pincode" class="form-control" placeholder="Pincode">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="edit_status" class="form-select" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="editCustomerSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Update Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Customer Modal -->
    <div class="modal fade" id="deleteCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-trash me-1 text-danger"></i> Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete customer <strong id="delete_customer_name"></strong>?</p>
                </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Customer Modal (Requirement 14) -->
    <div class="modal fade" id="importCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="importCustomerForm" action="{{ route('admin.customers.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-upload me-1"></i> Import Customer Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 text-end">
                            <a href="{{ route('admin.customers.sample-csv') }}" class="btn btn-sm btn-outline-info">
                                <i class="bx bx-download me-1"></i> Download Sample Excel
                            </a>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select Excel / CSV File <span class="text-danger">*</span></label>
                            <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                            <small class="text-muted">Upload an Excel (.xlsx/.xls) or CSV file with headers: Name, Mobile, Email, Company Name, Customer Type, Alternate Mobile, Address, City, State, Country, Pincode.</small>
                        </div>
                        <div id="import-results" class="d-none mt-2 alert alert-info"></div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="importCustomerBtn">Upload & Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#importCustomerForm').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                $('#importCustomerBtn').prop('disabled', true).text('Importing...');
                $.ajax({
                    url: "{{ route('admin.customers.import') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        $('#importCustomerBtn').prop('disabled', false).text('Upload & Import');
                        if (res.status) {
                            $('#import-results').removeClass('d-none').html(`<strong>Import Success!</strong><br>${res.message}`);
                            setTimeout(function() {
                                $('#importCustomerModal').modal('hide');
                                location.reload();
                            }, 2000);
                        }
                    },
                    error: function(xhr) {
                        $('#importCustomerBtn').prop('disabled', false).text('Upload & Import');
                        alert(xhr.responseJSON?.message || 'CSV Import failed.');
                    }
                });
            });
        });
    </script>
    @endpush
@endsection
