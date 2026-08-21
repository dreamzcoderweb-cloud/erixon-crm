$(document).ready(function () {
    if (!$('#templates-table').length) return;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let templateTable = $('#templates-table').DataTable({
        ajax: {
            url: APP_URL + '/admin/templates/data',
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
                data: 'type',
                render: function (data, type) {
                    if (type !== 'display') return data || 'N/A';
                    let badgeClass = 'bg-label-primary';
                    if (data === 'Email') badgeClass = 'bg-label-info';
                    else if (data === 'WhatsApp') badgeClass = 'bg-label-success';
                    else if (data === 'SMS') badgeClass = 'bg-label-warning';
                    else if (data === 'Proposal') badgeClass = 'bg-label-dark';
                    return `<span class="badge ${badgeClass}">${data || 'N/A'}</span>`;
                }
            },
            {
                data: 'title',
                render: function (data, type) {
                    if (type !== 'display') return data;
                    return `<strong>${data}</strong>`;
                }
            },
            {
                data: 'content',
                render: function (data, type) {
                    if (type !== 'display') return data || '';
                    let text = $('<div>').html(data).text();
                    return text.length > 40 ? text.substring(0, 40) + '...' : text;
                }
            },
            {
                data: 'status',
                render: function (data, type, row) {
                    if (type !== 'display') return data;
                    let isChecked = data === 'Active' ? 'checked' : '';
                    return `
                        <div class="form-check form-switch">
                            <input class="form-check-input btn-toggle-template-status" type="checkbox" data-id="${row.template_id}" ${isChecked}>
                        </div>
                    `;
                }
            },
            {
                data: 'created_at',
                render: function (data, type) {
                    if (!data) return '-';
                    if (type !== 'display') return data;
                    let formatted = formatDateTime(data);
                    return `<small class="text-muted"><i class="bx bx-calendar me-1"></i>${formatted}</small>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: function (data, type, row) {
                    return `
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item btn-edit-template" href="javascript:void(0);" data-id="${row.template_id}">
                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                </a>
                                <a class="dropdown-item text-danger btn-delete-template" href="javascript:void(0);" data-id="${row.template_id}">
                                    <i class="bx bx-trash me-1"></i> Delete
                                </a>
                            </div>
                        </div>
                    `;
                }
            }
        ],
        layout: {
            topStart: ['pageLength', { buttons: ['copy', 'csv', 'excel', 'pdf', 'print'] }],
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging'
        },
        pageLength: 10,
        ordering: false
    });

    function showAlert(type, message) {
        let alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show mb-3" role="alert">
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
                    errorDiv = $('<div class="invalid-feedback"></div>');
                    input.after(errorDiv);
                }
                errorDiv.text(messages[0]).css('display', 'block');
            }
        });
    }

    $('.modal').on('hidden.bs.modal', function () {
        let form = $(this).find('form');
        if (form.length) {
            clearValidationErrors(form);
        }
    });

    // Add Template Submit
    $('#addTemplateForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let submitBtn = $('#addTemplateSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/templates/store',
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#addTemplateModal').modal('hide');
                    form[0].reset();
                    templateTable.ajax.reload(null, false);
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

    // Edit Template Open
    $(document).on('click', '.btn-edit-template', function () {
        let id = $(this).data('id');
        let form = $('#editTemplateForm');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/templates/edit/' + id,
            type: 'GET',
            success: function (response) {
                if (response.status) {
                    let tmpl = response.data;
                    $('#edit_template_id').val(tmpl.template_id);
                    $('#edit_template_type').val(tmpl.type);
                    $('#edit_template_title').val(tmpl.title);
                    $('#edit_template_content').val(tmpl.content);
                    $('#edit_template_status').val(tmpl.status);

                    $('#editTemplateModal').modal('show');
                }
            },
            error: function () {
                showAlert('danger', 'Failed to fetch template details.');
            }
        });
    });

    // Update Template Submit
    $('#editTemplateForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let id = $('#edit_template_id').val();
        let submitBtn = $('#editTemplateSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/templates/update/' + id,
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#editTemplateModal').modal('hide');
                    templateTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', 'Failed to update template.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Toggle Status
    $(document).on('change', '.btn-toggle-template-status', function () {
        let id = $(this).data('id');
        $.ajax({
            url: APP_URL + '/admin/templates/change-status/' + id,
            type: 'POST',
            success: function (response) {
                if (response.status) {
                    showAlert('success', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Failed to toggle status.');
                templateTable.ajax.reload(null, false);
            }
        });
    });

    // Delete Template
    let deleteTemplateId = null;
    $(document).on('click', '.btn-delete-template', function () {
        deleteTemplateId = $(this).data('id');
        $('#deleteTemplateModal').modal('show');
    });

    $('#confirmDeleteTemplateBtn').on('click', function () {
        if (!deleteTemplateId) return;

        let btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: APP_URL + '/admin/templates/delete/' + deleteTemplateId,
            type: 'DELETE',
            success: function (response) {
                if (response.status) {
                    $('#deleteTemplateModal').modal('hide');
                    templateTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Failed to delete template.');
            },
            complete: function () {
                btn.prop('disabled', false);
                deleteTemplateId = null;
            }
        });
    });
});
