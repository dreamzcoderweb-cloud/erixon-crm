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
            <div class="table-responsive text-nowrap p-3">
                <table id="payments-table" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
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
                                <label class="form-label">Lead (Optional)</label>
                                <select name="lead_id" class="form-select">
                                    <option value="">-- Select Lead --</option>
                                    @foreach ($leads as $lead)
                                        <option value="{{ $lead->lead_id }}">{{ $lead->lead_title }}</option>
                                    @endforeach
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

@endsection
