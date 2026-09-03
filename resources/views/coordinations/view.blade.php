@extends('layouts.master')
@section('title', 'Coordination')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        .select2-container {
            z-index: 1090 !important;
            width: 100% !important;
        }
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #d9ade5 !important;
            border-radius: 0.375rem !important;
            min-height: 44px !important;
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
            padding: 3px 10px !important;
            font-size: 0.8125rem !important;
            font-weight: 500 !important;
            margin-top: 4px !important;
            margin-right: 5px !important;
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
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div id="alert-container"></div>

        <div class="card">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="card-header p-0 m-0"><i class="bx bx-git-repo-forked me-2"></i>Coordination</h5>
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
                            <th>Created Staff</th>
                            <th>Link</th>
                            <th>Joining Staff & Status</th>
                            <th>My Participation</th>
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="addCoordinationForm" method="POST" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-plus me-1"></i> Add Coordination</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Created Staff <span class="text-danger">*</span></label>
                            <select name="staff_id" id="add_staff_id" class="form-select" required>
                                <option value="">-- Select Created Staff --</option>
                                @foreach ($staffList as $staff)
                                    <option value="{{ $staff->id }}" {{ Auth::id() == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }} ({{ $staff->email }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Link <span class="text-danger">*</span></label>
                            <input type="text" name="link" class="form-control" placeholder="e.g. https://example.com/coordination-link" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Joining Staff <span class="text-muted">(Multiple Selection)</span></label>
                            <select name="joining_staff_ids[]" id="add_joining_staff_ids" class="form-select select2-multi" multiple="multiple" data-placeholder="Select Joining Staff members...">
                                @foreach ($staffList as $staff)
                                    <option value="{{ $staff->id }}" {{ Auth::id() == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }} ({{ $staff->email }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">Note: Created Staff is automatically included in Joining Staff.</small>
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
        <div class="modal-dialog modal-lg" role="document">
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
                            <label class="form-label">Created Staff <span class="text-danger">*</span></label>
                            <select name="staff_id" id="edit_staff_id" class="form-select" required>
                                <option value="">-- Select Created Staff --</option>
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

                        <div class="mb-3">
                            <label class="form-label">Joining Staff <span class="text-muted">(Multiple Selection)</span></label>
                            <select name="joining_staff_ids[]" id="edit_joining_staff_ids" class="form-select select2-multi" multiple="multiple" data-placeholder="Select Joining Staff members...">
                                @foreach ($staffList as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">Note: Created Staff is automatically included in Joining Staff.</small>
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
