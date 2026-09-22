@extends('layouts.master')
@section('title', 'Payment List')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div id="alert-container"></div>

        <div class="card">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="card-header p-0 m-0"><i class="bx bx-receipt me-2"></i>Payment List</h5>
                @can('payments.create')
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                        <i class="bx bx-plus me-1"></i> Add Payment
                    </button>
                @endcan
            </div>

            <!-- Payment Date Period Filter Bar -->
            <div class="p-3 bg-light border-bottom">
                <form id="paymentFilterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12">
                            <label class="form-label fw-semibold d-block">Date Period</label>
                            <div class="btn-group btn-group-sm" role="group" id="paymentPeriodBtnGroup">
                                <button type="button" class="btn btn-outline-primary btn-payment-period active"
                                    data-period="all">All Time</button>
                                <button type="button" class="btn btn-outline-primary btn-payment-period"
                                    data-period="daily">Daily</button>
                                <button type="button" class="btn btn-outline-primary btn-payment-period"
                                    data-period="weekly">Weekly</button>
                                <button type="button" class="btn btn-outline-primary btn-payment-period"
                                    data-period="monthly">Monthly</button>
                                <button type="button" class="btn btn-outline-primary btn-payment-period"
                                    data-period="custom">Custom</button>
                            </div>
                            <input type="hidden" name="filter_type" id="payment_filter_period" value="all">
                        </div>

                        <div class="col-md-3 payment-filter-date-group d-none" id="payment_group_daily">
                            <label class="form-label fw-semibold">Date</label>
                            <input type="date" name="date" id="payment_filter_date"
                                class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-3 payment-filter-date-group d-none" id="payment_group_monthly">
                            <label class="form-label fw-semibold">Month</label>
                            <input type="month" name="month" id="payment_filter_month"
                                class="form-control form-control-sm" value="{{ date('Y-m') }}">
                        </div>

                        <div class="col-md-3 payment-filter-date-group d-none" id="payment_group_custom_start">
                            <label class="form-label fw-semibold">From / Start Date</label>
                            <input type="date" name="start_date" id="payment_filter_start_date"
                                class="form-control form-control-sm" value="{{ date('Y-m-01') }}">
                        </div>

                        <div class="col-md-3 payment-filter-date-group d-none" id="payment_group_custom_end">
                            <label class="form-label fw-semibold">To / End Date</label>
                            <input type="date" name="end_date" id="payment_filter_end_date"
                                class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>

                        <!-- Lead Requirement Filter -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Lead Requirement</label>
                            <select name="lead_requirement_id" id="payment_filter_lead_requirement_id" class="form-select form-select-sm">
                                <option value="">-- All Lead Requirements --</option>
                                @if(isset($leadRequirements) && count($leadRequirements) > 0)
                                    @foreach ($leadRequirements as $req)
                                        <option value="{{ $req->lead_requirements_id }}">{{ $req->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="bx bx-filter-alt me-1"></i> Apply Filter
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    id="resetPaymentFilterBtn" title="Reset Filters">
                                    <i class="bx bx-refresh me-1"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive text-nowrap p-3">
                <table id="payments-table" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Lead Requirement</th>
                            <th>Amount</th>
                            <th>Tax %</th>
                            <th>Tax Amount</th>
                            <th>Total Amount</th>
                            <th>Payment Method</th>
                            <th>Payment Date</th>
                            <th>Screenshot</th>
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

    <!-- Add Payment Modal -->
    <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="addPaymentForm" action="{{ url('admin/payments/store') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-plus-circle me-1"></i> Add New Payment</h5>
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
                                <label class="form-label">Lead Requirement</label>
                                <select name="lead_requirement_id" class="form-select">
                                    <option value="">-- Select Lead Requirement --</option>
                                    @if(isset($leadRequirements) && count($leadRequirements) > 0)
                                        @foreach ($leadRequirements as $req)
                                            <option value="{{ $req->lead_requirements_id }}">{{ $req->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Base Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" id="add_amount" name="amount" class="form-control" placeholder="1000.00" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tax (%) <span class="text-danger">* Mandatory</span></label>
                                <input type="number" step="0.01" id="add_tax_percentage" name="tax_percentage" class="form-control" value="18.00" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tax Amount (₹) <span class="text-danger">* Mandatory</span></label>
                                <input type="number" step="0.01" id="add_tax_amount" name="tax_amount" class="form-control" readonly required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Total Amount (₹) <span class="text-danger">* Mandatory</span></label>
                                <input type="number" step="0.01" id="add_total_amount" name="total_amount" class="form-control" readonly required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="UPI / QR">UPI / QR</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Credit Card">Credit Card</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tax / GST Number</label>
                                <input type="text" name="tax_number" class="form-control" placeholder="GSTIN123456789">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Payment Receipt / Screenshot <span class="text-danger">* Mandatory Screenshot</span></label>
                                <input type="file" name="payment_screenshot" class="form-control" accept="image/*,.pdf" required>
                                <small class="text-muted">Upload screenshot / payment proof (JPG, PNG, WEBP, PDF)</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Screenshot Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-image me-1"></i> Payment Screenshot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-3">
                    <img id="previewImage" src="" class="img-fluid rounded" style="max-height: 450px;" alt="Screenshot">
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Payment Modal -->
    <div class="modal fade" id="editPaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="editPaymentForm" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    <input type="hidden" name="payment_id" id="edit_payment_id">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-edit me-1"></i> Edit Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Customer <span class="text-danger">*</span></label>
                                <select name="customer_id" id="edit_customer_id" class="form-select" required>
                                    <option value="">-- Select Customer --</option>
                                    @foreach ($customers as $cust)
                                        <option value="{{ $cust->customer_id }}">{{ $cust->name }} ({{ $cust->mobile }})</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lead Requirement</label>
                                <select name="lead_requirement_id" id="edit_lead_requirement_id" class="form-select">
                                    <option value="">-- Select Lead Requirement --</option>
                                    @if(isset($leadRequirements) && count($leadRequirements) > 0)
                                        @foreach ($leadRequirements as $req)
                                            <option value="{{ $req->lead_requirements_id }}">{{ $req->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Base Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" id="edit_amount" name="amount" class="form-control" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tax (%) <span class="text-danger">* Mandatory</span></label>
                                <input type="number" step="0.01" id="edit_tax_percentage" name="tax_percentage" class="form-control" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tax Amount (₹) <span class="text-danger">* Mandatory</span></label>
                                <input type="number" step="0.01" id="edit_tax_amount" name="tax_amount" class="form-control" readonly required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Total Amount (₹) <span class="text-danger">* Mandatory</span></label>
                                <input type="number" step="0.01" id="edit_total_amount" name="total_amount" class="form-control" readonly required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select name="payment_method" id="edit_payment_method" class="form-select" required>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="UPI / QR">UPI / QR</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Credit Card">Credit Card</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" id="edit_payment_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tax / GST Number</label>
                                <input type="text" name="tax_number" id="edit_tax_number" class="form-control" placeholder="GSTIN123456789">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Payment Receipt / Screenshot (Optional to replace)</label>
                                <input type="file" name="payment_screenshot" class="form-control" accept="image/*,.pdf">
                                <small class="text-muted">Leave empty to keep existing proof.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="editPaymentSubmitBtn">Update Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
