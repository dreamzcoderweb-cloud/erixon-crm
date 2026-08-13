@extends('layouts.master')
@section('title', 'Lost Reasons - Super Admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div id="alert-container"></div>

        <div class="card">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="card-header p-0 m-0"><i class="bx bx-x-circle me-2"></i>Lost Reasons</h5>
                @can('lost-reasons.create')
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLostReasonModal">
                        <i class="bx bx-plus me-1"></i> Add Lost Reason
                    </button>
                @endcan
            </div>
            <div class="table-responsive text-nowrap p-3">
                <table id="lost-reasons-table" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Reason</th>
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

    <!-- Add Lost Reason Modal -->
    <div class="modal fade" id="addLostReasonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="addLostReasonForm" method="POST" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-plus me-1"></i> Add Lost Reason</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Lost Reason <span class="text-danger">*</span></label>
                            <input type="text" name="reason" class="form-control" placeholder="e.g. High Price, Competitor Chosen, Budget Issue" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="addLostReasonSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Save Reason
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Lost Reason Modal -->
    <div class="modal fade" id="editLostReasonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editLostReasonForm" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="lost_reason_id" id="edit_lost_reason_id">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-edit me-1"></i> Edit Lost Reason</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Lost Reason <span class="text-danger">*</span></label>
                            <input type="text" name="reason" id="edit_lost_reason" class="form-control" placeholder="Enter reason" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="edit_reason_status" class="form-select" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="editLostReasonSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Update Reason
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Lost Reason Modal -->
    <div class="modal fade" id="deleteLostReasonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-trash me-1 text-danger"></i> Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete lost reason <strong id="delete_reason_text"></strong>?</p>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteReasonBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
