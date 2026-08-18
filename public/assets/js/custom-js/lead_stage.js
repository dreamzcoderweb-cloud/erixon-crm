$(document).ready(function () {
    if (!$('#lead-stages-table').length) return;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let stageTable = $('#lead-stages-table').DataTable({
        ajax: {
            url: APP_URL + '/admin/lead-stages/data',
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
                render: function (data, type) {
                    if (type !== 'display') return data || '';
                    return `<strong>${data}</strong>`;
                }
            },
            {
                data: 'sort_order',
                render: function (data, type) {
                    if (type !== 'display') return data ?? 0;
                    return `<span class="badge bg-label-info">${data ?? 0}</span>`;
                }
            },
            {
                data: 'status',
                render: function (data, type, row) {
                    let statusText = data == 1 ? 'Active' : 'Inactive';
                    if (type !== 'display') return statusText;
                    let isChecked = data == 1 ? 'checked' : '';
                    let statusLabel = data == 1 ? '<span class="badge bg-label-success">Active</span>' : '<span class="badge bg-label-secondary">Inactive</span>';
                    return `
                        <div class="d-flex align-items-center gap-2">
                            ${statusLabel}
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input btn-toggle-stage-status" type="checkbox" data-id="${row.lead_stage_id}" ${isChecked}>
                            </div>
                        </div>
                    `;
                }
            },
            {
                data: null,
                orderable: false,
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-sm btn-outline-primary btn-edit-stage me-1" data-id="${row.lead_stage_id}">
                            <i class="bx bx-edit-alt"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger btn-delete-stage" data-id="${row.lead_stage_id}" data-name="${row.name}">
                            <i class="bx bx-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        layout: {
            topStart: [
                'pageLength',
                {
                    buttons: [
                        { extend: 'copy', className: 'btn btn-secondary btn-sm me-1', exportOptions: { columns: ':not(:last-child)' } },
                        { extend: 'csv', className: 'btn btn-secondary btn-sm me-1', exportOptions: { columns: ':not(:last-child)' } },
                        { extend: 'excel', className: 'btn btn-secondary btn-sm me-1', exportOptions: { columns: ':not(:last-child)' } },
                        { extend: 'pdf', className: 'btn btn-secondary btn-sm me-1', exportOptions: { columns: ':not(:last-child)' } },
                        { extend: 'print', className: 'btn btn-secondary btn-sm', exportOptions: { columns: ':not(:last-child)' } }
                    ]
                }
            ],
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging'
        },
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        pageLength: 10,
        responsive: true,
        // Disable sorting completely
            ordering: false
    });

    function showAlert(type, message) {
        let alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <span>${message}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('#alert-container').html(alertHtml);
        setTimeout(function () {
            $('.alert').alert('close');
        }, 4000);
    }

    function clearValidationErrors(form) {
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('').css('display', 'none');
    }

    function showValidationErrors(form, errors) {
        clearValidationErrors(form);
        $.each(errors, function (field, messages) {
            let input = form.find(`[name="${field}"]`);
            if (input.length) {
                input.addClass('is-invalid');
                let errorDiv = input.siblings('.invalid-feedback');
                if (!errorDiv.length) {
                    errorDiv = input.parent().find('.invalid-feedback');
                }
                if (!errorDiv.length) {
                    errorDiv = $('<div class="invalid-feedback"></div>');
                    input.after(errorDiv);
                }
                errorDiv.text(messages[0]).css('display', 'block');
            }
        });
    }

    // Reset validation errors on modal hide
    $('.modal').on('hidden.bs.modal', function () {
        let form = $(this).find('form');
        if (form.length) {
            clearValidationErrors(form);
        }
    });

    // Add Lead Stage Form Submit
    $('#addLeadStageForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let submitBtn = $('#addLeadStageSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/lead-stages/store',
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#addLeadStageModal').modal('hide');
                    form[0].reset();
                    stageTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', 'An error occurred. Please try again.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Open Edit Lead Stage Modal
    $(document).on('click', '.btn-edit-stage', function () {
        let id = $(this).data('id');
        let form = $('#editLeadStageForm');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/lead-stages/edit/' + id,
            type: 'GET',
            success: function (response) {
                if (response.status) {
                    let stage = response.data;
                    $('#edit_lead_stage_id').val(stage.lead_stage_id);
                    $('#edit_stage_name').val(stage.name);
                    $('#edit_stage_sort_order').val(stage.sort_order);
                    $('#edit_stage_status').val(stage.status);

                    $('#editLeadStageModal').modal('show');
                }
            },
            error: function () {
                showAlert('danger', 'Failed to fetch lead stage details.');
            }
        });
    });

    // Update Lead Stage Form Submit
    $('#editLeadStageForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let id = $('#edit_lead_stage_id').val();
        let submitBtn = $('#editLeadStageSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/lead-stages/update/' + id,
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#editLeadStageModal').modal('hide');
                    stageTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', 'An error occurred while updating lead stage.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Open Delete Confirmation Modal
    let deleteStageId = null;
    $(document).on('click', '.btn-delete-stage', function () {
        deleteStageId = $(this).data('id');
        let name = $(this).data('name');
        $('#delete_stage_name').text(name);
        $('#deleteLeadStageModal').modal('show');
    });

    // Confirm Delete Lead Stage
    $('#confirmDeleteStageBtn').on('click', function () {
        if (!deleteStageId) return;

        let btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: APP_URL + '/admin/lead-stages/delete/' + deleteStageId,
            type: 'DELETE',
            success: function (response) {
                if (response.status) {
                    $('#deleteLeadStageModal').modal('hide');
                    stageTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Failed to delete lead stage.');
            },
            complete: function () {
                btn.prop('disabled', false);
                deleteStageId = null;
            }
        });
    });

    // Toggle Status
    $(document).on('change', '.btn-toggle-stage-status', function () {
        let id = $(this).data('id');
        let checkbox = $(this);

        $.ajax({
            url: APP_URL + '/admin/lead-stages/change-status/' + id,
            type: 'POST',
            success: function (response) {
                if (response.status) {
                    stageTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function () {
                checkbox.prop('checked', !checkbox.is(':checked'));
                showAlert('danger', 'Failed to update status.');
            }
        });
    });
});
