$(document).ready(function () {
    if (!$('#lost-reasons-table').length) return;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let reasonTable = $('#lost-reasons-table').DataTable({
        ajax: {
            url: APP_URL + '/admin/lost-reasons/data',
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
                data: 'reason',
                render: function (data, type) {
                    if (type !== 'display') return data || '';
                    return `<strong>${data}</strong>`;
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
                                <input class="form-check-input btn-toggle-reason-status" type="checkbox" data-id="${row.lost_reason_id}" ${isChecked}>
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
                        <button class="btn btn-sm btn-outline-primary btn-edit-reason me-1" data-id="${row.lost_reason_id}">
                            <i class="bx bx-edit-alt"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger btn-delete-reason" data-id="${row.lost_reason_id}" data-name="${row.reason}">
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

    // Add Lost Reason Form Submit
    $('#addLostReasonForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let submitBtn = $('#addLostReasonSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/lost-reasons/store',
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#addLostReasonModal').modal('hide');
                    form[0].reset();
                    reasonTable.ajax.reload(null, false);
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

    // Open Edit Lost Reason Modal
    $(document).on('click', '.btn-edit-reason', function () {
        let id = $(this).data('id');
        let form = $('#editLostReasonForm');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/lost-reasons/edit/' + id,
            type: 'GET',
            success: function (response) {
                if (response.status) {
                    let reason = response.data;
                    $('#edit_lost_reason_id').val(reason.lost_reason_id);
                    $('#edit_lost_reason').val(reason.reason);
                    $('#edit_reason_status').val(reason.status);

                    $('#editLostReasonModal').modal('show');
                }
            },
            error: function () {
                showAlert('danger', 'Failed to fetch lost reason details.');
            }
        });
    });

    // Update Lost Reason Form Submit
    $('#editLostReasonForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let id = $('#edit_lost_reason_id').val();
        let submitBtn = $('#editLostReasonSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/lost-reasons/update/' + id,
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#editLostReasonModal').modal('hide');
                    reasonTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', 'An error occurred while updating lost reason.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Open Delete Confirmation Modal
    let deleteReasonId = null;
    $(document).on('click', '.btn-delete-reason', function () {
        deleteReasonId = $(this).data('id');
        let name = $(this).data('name');
        $('#delete_reason_text').text(name);
        $('#deleteLostReasonModal').modal('show');
    });

    // Confirm Delete Lost Reason
    $('#confirmDeleteReasonBtn').on('click', function () {
        if (!deleteReasonId) return;

        let btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: APP_URL + '/admin/lost-reasons/delete/' + deleteReasonId,
            type: 'DELETE',
            success: function (response) {
                if (response.status) {
                    $('#deleteLostReasonModal').modal('hide');
                    reasonTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Failed to delete lost reason.');
            },
            complete: function () {
                btn.prop('disabled', false);
                deleteReasonId = null;
            }
        });
    });

    // Toggle Status
    $(document).on('change', '.btn-toggle-reason-status', function () {
        let id = $(this).data('id');
        let checkbox = $(this);

        $.ajax({
            url: APP_URL + '/admin/lost-reasons/change-status/' + id,
            type: 'POST',
            success: function (response) {
                if (response.status) {
                    reasonTable.ajax.reload(null, false);
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
