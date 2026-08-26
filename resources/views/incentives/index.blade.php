@extends('layouts.master')
@section('title', 'Incentives Management - CRM')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div id="alert-container"></div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0"><i class="bx bx-gift me-2"></i> Incentive Management</h4>
            <div class="d-flex gap-2">
                @can('incentives.create')
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addIncentiveModal">
                        <i class="bx bx-plus me-1"></i> Add Incentive
                    </button>
                @endcan
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="card-title m-0"><i class="bx bx-list-ul me-1"></i> Incentive List</h5>
                <div class="d-flex gap-2">
                    @if(auth()->user()->isSuperAdmin())
                        <select id="filter_incentive_staff_id" class="form-select form-select-sm" style="width: 200px;">
                            <option value="">-- All Staff Members --</option>
                            @foreach ($staffs as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    @else
                        <select id="filter_incentive_staff_id" class="form-select form-select-sm" style="width: 200px;">
                            <option value="{{ auth()->user()->id }}">{{ auth()->user()->name }}</option>
                        </select>
                    @endif
                    <input type="month" id="filter_incentive_month" class="form-control form-control-sm" style="width: 170px;" value="{{ date('Y-m') }}">
                </div>
            </div>
            <div class="table-responsive text-nowrap p-3">
                <table id="incentives-table" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Staff Name</th>
                            <th>Month</th>
                            <th>Amount</th>
                            <th>Remarks</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal: Add Incentive -->
    <div class="modal fade" id="addIncentiveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form id="addIncentiveForm" method="POST" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-plus-circle me-1"></i> Add Incentive</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Staff Member <span class="text-danger">*</span></label>
                                <select name="staff_id" class="form-select" required>
                                    <option value="">-- Select Staff Member --</option>
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Incentive Month <span class="text-danger">*</span></label>
                                <input type="month" name="month" class="form-control" value="{{ date('Y-m') }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="0.00" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Remarks</label>
                                <textarea name="remarks" class="form-control" rows="3" placeholder="Enter remarks or justification..."></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="submitIncentiveBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Save Incentive
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Edit Incentive -->
    <div class="modal fade" id="editIncentiveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form id="editIncentiveForm" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="incentive_id" id="edit_incentive_id">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-edit me-1"></i> Edit Incentive</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Staff Member <span class="text-danger">*</span></label>
                                <select name="staff_id" id="edit_incentive_staff_id" class="form-select" required>
                                    <option value="">-- Select Staff Member --</option>
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Incentive Month <span class="text-danger">*</span></label>
                                <input type="month" name="month" id="edit_incentive_month" class="form-control" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" name="amount" id="edit_incentive_amount" class="form-control" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Remarks</label>
                                <textarea name="remarks" id="edit_incentive_remarks" class="form-control" rows="3"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="updateIncentiveBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Update Incentive
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

