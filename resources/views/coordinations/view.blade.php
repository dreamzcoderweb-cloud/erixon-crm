@extends('layouts.master')
@section('title', 'Coordination - Super Admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div id="alert-container"></div>

        <div class="card">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="card-header p-0 m-0"><i class="bx bx-link-external me-2"></i>Coordination</h5>
                @can('coordinations.create')
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCoordinationModal">
                        <i class="bx bx-plus me-1"></i> Add Coordination
                    </button>
                @endcan
            </div>
            <div class="table-responsive text-nowrap p-3">
                <table id="coordinations-table" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Staff Name</th>
                            <th>Link</th>
                            <th>Created By</th>
                            <th>Date</th>
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

    <!-- Add Coordination Modal -->
    <div class="modal fade" id="addCoordinationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="addCoordinationForm" method="POST" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-plus me-1"></i> Add Coordination</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Staff Member <span class="text-danger">*</span></label>
                            <select name="staff_id" class="form-select" required>
                                <option value="">-- Select Staff Member --</option>
                                @foreach ($staffList as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Link <span class="text-danger">*</span></label>
                            <input type="text" name="link" class="form-control" placeholder="e.g. https://example.com/coordination-link" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="addCoordinationSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Save Coordination
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Coordination Modal -->
    <div class="modal fade" id="editCoordinationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editCoordinationForm" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="coordination_id" id="edit_coordination_id">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-edit me-1"></i> Edit Coordination</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Staff Member <span class="text-danger">*</span></label>
                            <select name="staff_id" id="edit_staff_id" class="form-select" required>
                                <option value="">-- Select Staff Member --</option>
                                @foreach ($staffList as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Link <span class="text-danger">*</span></label>
                            <input type="text" name="link" id="edit_link" class="form-control" placeholder="Enter link" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="editCoordinationSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Update Coordination
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Coordination Modal -->
    <div class="modal fade" id="deleteCoordinationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-trash me-1 text-danger"></i> Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this coordination record for <strong id="delete_staff_name"></strong>?</p>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteCoordinationBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection
