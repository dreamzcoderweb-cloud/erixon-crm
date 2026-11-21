@extends('layouts.master')
@section('title', 'Branch Management - Erixon CRM')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div id="alert-container"></div>

    <div class="card">
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom flex-wrap gap-2">
            <div>
                <h5 class="card-header p-0 m-0"><i class="bx bx-buildings me-2 text-primary"></i>Branch Management</h5>
                <small class="text-muted">Manage company branches, locations, and branch managers</small>
            </div>
            @can('branches.create')
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBranchModal">
                    <i class="bx bx-plus me-1"></i> Add Branch
                </button>
            @endcan
        </div>
        <div class="table-responsive text-nowrap p-3">
            <table id="branches-table" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Branch Name</th>
                        <th>Branch Code</th>
                        <th>Manager</th>
                        <th>Contact</th>
                        <th>City / State</th>
                        <th>Teams</th>
                        <th>Staff</th>
                        <th>Customers</th>
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

<!-- Add Branch Modal -->
<div class="modal fade" id="addBranchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="addBranchForm" method="POST" novalidate>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-plus me-1"></i> Add New Branch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Branch Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Chennai Main Branch" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Branch Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" placeholder="e.g. BR-CHE-01" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Branch Manager</label>
                            <select name="manager_id" class="form-select">
                                <option value="">-- Select Manager --</option>
                                @foreach($managers as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->email }})</option>
                                @endforeach
                            </select>
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
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="e.g. +91 9876543210">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="e.g. branch@erixon.com">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" placeholder="e.g. Chennai">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State</label>
                            <input type="text" name="state" class="form-control" placeholder="e.g. Tamil Nadu">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pincode</label>
                            <input type="text" name="pincode" class="form-control" placeholder="e.g. 600001">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Full branch street address"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="addBranchSubmitBtn">
                        <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Save Branch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Branch Modal -->
<div class="modal fade" id="editBranchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="editBranchForm" method="POST" novalidate>
                @csrf
                <input type="hidden" name="branch_id" id="edit_branch_id">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-edit me-1"></i> Edit Branch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Branch Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_branch_name" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Branch Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="edit_branch_code" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Branch Manager</label>
                            <select name="manager_id" id="edit_branch_manager_id" class="form-select">
                                <option value="">-- Select Manager --</option>
                                @foreach($managers as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->email }})</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="edit_branch_status" class="form-select" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" id="edit_branch_phone" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_branch_email" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" name="city" id="edit_branch_city" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State</label>
                            <input type="text" name="state" id="edit_branch_state" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pincode</label>
                            <input type="text" name="pincode" id="edit_branch_pincode" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" id="edit_branch_address" class="form-control" rows="2"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="editBranchSubmitBtn">
                        <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Update Branch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Branch Modal -->
<div class="modal fade" id="deleteBranchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-trash me-1 text-danger"></i> Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete branch <strong id="delete_branch_name"></strong>?</p>
            </div>
            <div class="modal-footer gap-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBranchBtn">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#branches-table').DataTable({
        processing: true,
        ajax: {
            url: "{{ route('admin.branches.data') }}",
            dataSrc: 'data'
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {
                data: 'name',
                render: function(data, type, row) {
                    return '<span class="fw-bold text-dark">' + (data || '-') + '</span>';
                }
            },
            {
                data: 'code',
                render: function(data) {
                    return '<span class="badge bg-label-info">' + (data || '-') + '</span>';
                }
            },
            {
                data: 'manager',
                render: function(data) {
                    return data ? '<span class="fw-semibold text-primary"><i class="bx bx-user-check me-1"></i>' + data.name + '</span>' : '<span class="text-muted">Not Assigned</span>';
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    var str = '';
                    if (row.phone) str += '<div><i class="bx bx-phone me-1 small"></i>' + row.phone + '</div>';
                    if (row.email) str += '<div><i class="bx bx-envelope me-1 small"></i>' + row.email + '</div>';
                    return str || '<span class="text-muted">-</span>';
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    var loc = [];
                    if (row.city) loc.push(row.city);
                    if (row.state) loc.push(row.state);
                    return loc.length ? loc.join(', ') : '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'teams_count',
                render: function(data) {
                    return '<span class="badge bg-label-primary rounded-pill">' + (data || 0) + ' Teams</span>';
                }
            },
            {
                data: 'users_count',
                render: function(data) {
                    return '<span class="badge bg-label-warning rounded-pill">' + (data || 0) + ' Staff</span>';
                }
            },
            {
                data: 'customers_count',
                render: function(data) {
                    return '<span class="badge bg-label-success rounded-pill">' + (data || 0) + ' Customers</span>';
                }
            },
            {
                data: 'status',
                render: function(data, type, row) {
                    var checked = data == 1 ? 'checked' : '';
                    return '<div class="form-check form-switch d-inline-block">' +
                           '<input class="form-check-input toggle-status" type="checkbox" data-id="' + row.id + '" ' + checked + '>' +
                           '</div>';
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    var editBtn = '';
                    var delBtn = '';
                    @can('branches.edit')
                        editBtn = '<button class="btn btn-sm btn-icon btn-outline-primary edit-branch-btn me-1" data-id="' + row.id + '" title="Edit"><i class="bx bx-edit"></i></button>';
                    @endcan
                    @can('branches.delete')
                        if (row.id != 1) {
                            delBtn = '<button class="btn btn-sm btn-icon btn-outline-danger delete-branch-btn" data-id="' + row.id + '" data-name="' + row.name + '" title="Delete"><i class="bx bx-trash"></i></button>';
                        }
                    @endcan
                    return editBtn + delBtn;
                }
            }
        ]
    });

    // Add Branch Form Submit
    $('#addBranchForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = $('#addBranchSubmitBtn');
        btn.prop('disabled', true).find('.spinner-border').removeClass('d-none');
        form.find('.is-invalid').removeClass('is-invalid');

        $.ajax({
            url: "{{ route('admin.branches.store') }}",
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                btn.prop('disabled', false).find('.spinner-border').addClass('d-none');
                if (res.status) {
                    $('#addBranchModal').modal('hide');
                    form[0].reset();
                    table.ajax.reload(null, false);
                    showAlert('success', res.message);
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).find('.spinner-border').addClass('d-none');
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, messages) {
                        var input = form.find('[name="' + field + '"]');
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(messages[0]);
                    });
                } else {
                    showAlert('danger', 'An error occurred while creating branch.');
                }
            }
        });
    });

    // Edit Branch Click
    $(document).on('click', '.edit-branch-btn', function() {
        var id = $(this).data('id');
        var form = $('#editBranchForm');
        form[0].reset();
        form.find('.is-invalid').removeClass('is-invalid');

        $.get("{{ url('admin/branches/edit') }}/" + id, function(res) {
            if (res.status) {
                var d = res.data;
                $('#edit_branch_id').val(d.id);
                $('#edit_branch_name').val(d.name);
                $('#edit_branch_code').val(d.code);
                $('#edit_branch_manager_id').val(d.manager_id);
                $('#edit_branch_status').val(d.status);
                $('#edit_branch_phone').val(d.phone);
                $('#edit_branch_email').val(d.email);
                $('#edit_branch_city').val(d.city);
                $('#edit_branch_state').val(d.state);
                $('#edit_branch_pincode').val(d.pincode);
                $('#edit_branch_address').val(d.address);

                $('#editBranchModal').modal('show');
            }
        });
    });

    // Edit Branch Submit
    $('#editBranchForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var id = $('#edit_branch_id').val();
        var btn = $('#editBranchSubmitBtn');
        btn.prop('disabled', true).find('.spinner-border').removeClass('d-none');
        form.find('.is-invalid').removeClass('is-invalid');

        $.ajax({
            url: "{{ url('admin/branches/update') }}/" + id,
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                btn.prop('disabled', false).find('.spinner-border').addClass('d-none');
                if (res.status) {
                    $('#editBranchModal').modal('hide');
                    table.ajax.reload(null, false);
                    showAlert('success', res.message);
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).find('.spinner-border').addClass('d-none');
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, messages) {
                        var input = form.find('[name="' + field + '"]');
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(messages[0]);
                    });
                } else {
                    showAlert('danger', 'An error occurred while updating branch.');
                }
            }
        });
    });

    // Delete Branch Click & Confirm
    var deleteBranchId = null;
    $(document).on('click', '.delete-branch-btn', function() {
        deleteBranchId = $(this).data('id');
        $('#delete_branch_name').text($(this).data('name'));
        $('#deleteBranchModal').modal('show');
    });

    $('#confirmDeleteBranchBtn').on('click', function() {
        if (!deleteBranchId) return;
        var btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: "{{ url('admin/branches/delete') }}/" + deleteBranchId,
            type: "DELETE",
            data: { _token: "{{ csrf_token() }}" },
            success: function(res) {
                btn.prop('disabled', false);
                $('#deleteBranchModal').modal('hide');
                if (res.status) {
                    table.ajax.reload(null, false);
                    showAlert('success', res.message);
                } else {
                    showAlert('danger', res.message);
                }
            },
            error: function() {
                btn.prop('disabled', false);
                $('#deleteBranchModal').modal('hide');
                showAlert('danger', 'Failed to delete branch.');
            }
        });
    });

    // Toggle Status
    $(document).on('change', '.toggle-status', function() {
        var id = $(this).data('id');
        var switchEl = $(this);
        $.post("{{ url('admin/branches/change-status') }}/" + id, { _token: "{{ csrf_token() }}" }, function(res) {
            if (res.status) {
                showAlert('success', res.message);
            } else {
                switchEl.prop('checked', !switchEl.prop('checked'));
                showAlert('danger', 'Failed to change status.');
            }
        }).fail(function() {
            switchEl.prop('checked', !switchEl.prop('checked'));
            showAlert('danger', 'Server error occurred.');
        });
    });

    function showAlert(type, message) {
        var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                        message +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>';
        $('#alert-container').html(alertHtml);
        setTimeout(function() {
            $('#alert-container .alert').alert('close');
        }, 4000);
    }
});
</script>
@endpush
