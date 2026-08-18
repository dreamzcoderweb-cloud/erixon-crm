$(document).ready(function () {
    if (!$('#lead-documents-table').length) return;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let documentTable = $('#lead-documents-table').DataTable({
        ajax: {
            url: APP_URL + '/admin/lead-documents/data',
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
                data: null,
                render: function (data, type, row) {
                    let title = row.lead ? row.lead.lead_title : 'N/A';
                    let customerName = (row.lead && row.lead.customer) ? row.lead.customer.name : '';
                    if (type !== 'display') return title + ' (' + customerName + ')';
                    return `<div><strong>${title}</strong><br><small class="text-muted">${customerName ? '<i class="bx bx-user me-1"></i>' + customerName : ''}</small></div>`;
                }
            },
            {
                data: 'document_type',
                render: function (data, type) {
                    if (type !== 'display') return data || 'N/A';
                    return `<span class="badge bg-label-info"><i class="bx bx-file me-1"></i>${data || 'N/A'}</span>`;
                }
            },
            {
                data: 'file_name',
                render: function (data, type, row) {
                    if (type !== 'display') return data || 'N/A';
                    let fileUrl = APP_URL + '/' + row.file_path;
                    return `
                        <div>
                            <a href="${APP_URL}/admin/lead-documents/download/${row.lead_documents_id}" class="fw-semibold text-primary me-2">
                                <i class="bx bx-download me-1"></i>${data}
                            </a>
                            <a href="${fileUrl}" target="_blank" class="text-muted" title="View / Preview">
                                <i class="bx bx-show-alt"></i>
                            </a>
                        </div>
                    `;
                }
            },
            {
                data: 'uploader',
                render: function (data, type, row) {
                    let name = row.uploader ? row.uploader.name : 'System';
                    if (type !== 'display') return name;
                    return `<span class="badge bg-label-secondary">${name}</span>`;
                }
            },
            {
                data: 'created_at',
                render: function (data, type) {
                    if (!data) return '-';
                    if (type !== 'display') return data;
                    let formatted = new Date(data).toLocaleDateString();
                    return `<small class="text-muted"><i class="bx bx-calendar me-1"></i>${formatted}</small>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: function (data, type, row) {
                    return `
                        <div class="d-flex gap-1">
                            <a href="${APP_URL}/admin/lead-documents/download/${row.lead_documents_id}" class="btn btn-sm btn-outline-success" title="Download">
                                <i class="bx bx-download"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-primary btn-edit-document" data-id="${row.lead_documents_id}" title="Edit">
                                <i class="bx bx-edit-alt"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-delete-document" data-id="${row.lead_documents_id}" title="Delete">
                                <i class="bx bx-trash"></i>
                            </button>
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

    // Add Document submit
    $('#addDocumentForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let formData = new FormData(this);
        let submitBtn = $('#addDocumentSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/lead-documents/store',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.status) {
                    $('#addDocumentModal').modal('hide');
                    form[0].reset();
                    documentTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', 'An error occurred during upload.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Edit Document open
    $(document).on('click', '.btn-edit-document', function () {
        let id = $(this).data('id');
        let form = $('#editDocumentForm');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/lead-documents/edit/' + id,
            type: 'GET',
            success: function (response) {
                if (response.status) {
                    let doc = response.data;
                    $('#edit_document_id').val(doc.lead_documents_id);
                    $('#edit_document_lead_id').val(doc.lead_id);
                    $('#edit_document_type').val(doc.document_type);
                    $('#edit_current_filename').text(doc.file_name);

                    $('#editDocumentModal').modal('show');
                }
            },
            error: function () {
                showAlert('danger', 'Failed to fetch document details.');
            }
        });
    });

    // Update Document submit
    $('#editDocumentForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let id = $('#edit_document_id').val();
        let formData = new FormData(this);
        let submitBtn = $('#editDocumentSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/lead-documents/update/' + id,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.status) {
                    $('#editDocumentModal').modal('hide');
                    documentTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', 'Failed to update document.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Delete Document handler
    let deleteDocumentId = null;
    $(document).on('click', '.btn-delete-document', function () {
        deleteDocumentId = $(this).data('id');
        $('#deleteDocumentModal').modal('show');
    });

    $('#confirmDeleteDocumentBtn').on('click', function () {
        if (!deleteDocumentId) return;

        let btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: APP_URL + '/admin/lead-documents/delete/' + deleteDocumentId,
            type: 'DELETE',
            success: function (response) {
                if (response.status) {
                    $('#deleteDocumentModal').modal('hide');
                    documentTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Failed to delete document.');
            },
            complete: function () {
                btn.prop('disabled', false);
                deleteDocumentId = null;
            }
        });
    });
});
