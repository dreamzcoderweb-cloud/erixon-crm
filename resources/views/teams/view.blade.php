@extends('layouts.master')
@section('title', 'Team Management - Erixon CRM')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div id="alert-container"></div>

    <div class="card">
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom flex-wrap gap-2">
            <div>
                <h5 class="card-header p-0 m-0"><i class="bx bx-group me-2 text-primary"></i>Team Management</h5>
                <small class="text-muted">Manage departmental and sales teams within each branch</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <select id="filter_branch_id" class="form-select form-select-sm" style="min-width: 180px;">
                    <option value="">-- All Branches --</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
                @can('teams.create')
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTeamModal">
                        <i class="bx bx-plus me-1"></i> Add Team
                    </button>
                @endcan
            </div>
        </div>
        <div class="table-responsive text-nowrap p-3">
            <table id="teams-table" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Team Name</th>
                        <th>Team Code</th>
                        <th>Branch</th>
                        <th>Team Leader</th>
                        <th>Staff Count</th>
                        <th>Customers</th>
                        <th>Leads</th>
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

<!-- Add Team Modal -->
<div class="modal fade" id="addTeamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="addTeamForm" method="POST" novalidate>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-plus me-1"></i> Add New Team</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Branch <span class="text-danger">*</span></label>
                        <select name="branch_id" class="form-select" required>
                            <option value="">-- Select Branch --</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->code }})</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Team Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Sales Team Alpha" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Team Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. TM-SALES-01" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Team Leader</label>
                        <select name="leader_id" class="form-select">
                            <option value="">-- Select Leader --</option>
                            @foreach($leaders as $l)
                                <option value="{{ $l->id }}">{{ $l->name }} ({{ $l->email }})</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Brief team description"></textarea>
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
                    <button type="submit" class="btn btn-primary" id="addTeamSubmitBtn">
                        <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Save Team
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Team Modal -->
<div class="modal fade" id="editTeamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editTeamForm" method="POST" novalidate>
                @csrf
                <input type="hidden" name="team_id" id="edit_team_id">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-edit me-1"></i> Edit Team</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Branch <span class="text-danger">*</span></label>
                        <select name="branch_id" id="edit_team_branch_id" class="form-select" required>
                            <option value="">-- Select Branch --</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->code }})</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Team Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_team_name" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Team Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="edit_team_code" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Team Leader</label>
                        <select name="leader_id" id="edit_team_leader_id" class="form-select">
                            <option value="">-- Select Leader --</option>
                            @foreach($leaders as $l)
                                <option value="{{ $l->id }}">{{ $l->name }} ({{ $l->email }})</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="edit_team_description" class="form-control" rows="2"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="edit_team_status" class="form-select" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="editTeamSubmitBtn">
                        <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Update Team
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Team Modal -->
<div class="modal fade" id="deleteTeamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-trash me-1 text-danger"></i> Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete team <strong id="delete_team_name"></strong>?</p>
            </div>
            <div class="modal-footer gap-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteTeamBtn">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#teams-table').DataTable({
        processing: true,
        ajax: {
            url: "{{ route('admin.teams.data') }}",
            data: function(d) {
                d.branch_id = $('#filter_branch_id').val();
            },
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
                render: function(data) {
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
                data: 'branch',
                render: function(data) {
                    return data ? '<span class="badge bg-label-primary"><i class="bx bx-buildings me-1"></i>' + data.name + '</span>' : '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'leader',
                render: function(data) {
                    return data ? '<span class="fw-semibold text-primary"><i class="bx bx-user-check me-1"></i>' + data.name + '</span>' : '<span class="text-muted">Not Assigned</span>';
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
                data: 'leads_count',
                render: function(data) {
                    return '<span class="badge bg-label-info rounded-pill">' + (data || 0) + ' Leads</span>';
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
                    @can('teams.edit')
                        editBtn = '<button class="btn btn-sm btn-icon btn-outline-primary edit-team-btn me-1" data-id="' + row.id + '" title="Edit"><i class="bx bx-edit"></i></button>';
                    @endcan
                    @can('teams.delete')
                        if (row.id != 1) {
                            delBtn = '<button class="btn btn-sm btn-icon btn-outline-danger delete-team-btn" data-id="' + row.id + '" data-name="' + row.name + '" title="Delete"><i class="bx bx-trash"></i></button>';
                        }
                    @endcan
                    return editBtn + delBtn;
                }
            }
        ]
    });

    $('#filter_branch_id').on('change', function() {
        table.ajax.reload();
    });

    // Add Team Form Submit
    $('#addTeamForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = $('#addTeamSubmitBtn');
        btn.prop('disabled', true).find('.spinner-border').removeClass('d-none');
        form.find('.is-invalid').removeClass('is-invalid');

        $.ajax({
            url: "{{ route('admin.teams.store') }}",
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                btn.prop('disabled', false).find('.spinner-border').addClass('d-none');
                if (res.status) {
                    $('#addTeamModal').modal('hide');
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
                    showAlert('danger', 'An error occurred while creating team.');
                }
            }
        });
    });

    // Edit Team Click
    $(document).on('click', '.edit-team-btn', function() {
        var id = $(this).data('id');
        var form = $('#editTeamForm');
        form[0].reset();
        form.find('.is-invalid').removeClass('is-invalid');

        $.get("{{ url('admin/teams/edit') }}/" + id, function(res) {
            if (res.status) {
                var d = res.data;
                $('#edit_team_id').val(d.id);
                $('#edit_team_branch_id').val(d.branch_id);
                $('#edit_team_name').val(d.name);
                $('#edit_team_code').val(d.code);
                $('#edit_team_leader_id').val(d.leader_id);
                $('#edit_team_description').val(d.description);
                $('#edit_team_status').val(d.status);

                $('#editTeamModal').modal('show');
            }
        });
    });

    // Edit Team Submit
    $('#editTeamForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var id = $('#edit_team_id').val();
        var btn = $('#editTeamSubmitBtn');
        btn.prop('disabled', true).find('.spinner-border').removeClass('d-none');
        form.find('.is-invalid').removeClass('is-invalid');

        $.ajax({
            url: "{{ url('admin/teams/update') }}/" + id,
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                btn.prop('disabled', false).find('.spinner-border').addClass('d-none');
                if (res.status) {
                    $('#editTeamModal').modal('hide');
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
                    showAlert('danger', 'An error occurred while updating team.');
                }
            }
        });
    });

    // Delete Team Click & Confirm
    var deleteTeamId = null;
    $(document).on('click', '.delete-team-btn', function() {
        deleteTeamId = $(this).data('id');
        $('#delete_team_name').text($(this).data('name'));
        $('#deleteTeamModal').modal('show');
    });

    $('#confirmDeleteTeamBtn').on('click', function() {
        if (!deleteTeamId) return;
        var btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: "{{ url('admin/teams/delete') }}/" + deleteTeamId,
            type: "DELETE",
            data: { _token: "{{ csrf_token() }}" },
            success: function(res) {
                btn.prop('disabled', false);
                $('#deleteTeamModal').modal('hide');
                if (res.status) {
                    table.ajax.reload(null, false);
                    showAlert('success', res.message);
                } else {
                    showAlert('danger', res.message);
                }
            },
            error: function() {
                btn.prop('disabled', false);
                $('#deleteTeamModal').modal('hide');
                showAlert('danger', 'Failed to delete team.');
            }
        });
    });

    // Toggle Status
    $(document).on('change', '.toggle-status', function() {
        var id = $(this).data('id');
        var switchEl = $(this);
        $.post("{{ url('admin/teams/change-status') }}/" + id, { _token: "{{ csrf_token() }}" }, function(res) {
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
