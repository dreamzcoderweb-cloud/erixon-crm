$(document).ready(function () {
    if (!$('#call-recordings-table').length) return;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let recordingTable = $('#call-recordings-table').DataTable({
        ajax: {
            url: APP_URL + '/admin/call-recordings/data',
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
                data: 'duration',
                render: function (data, type) {
                    if (type !== 'display') return data || 'N/A';
                    return data ? `<span class="badge bg-label-info"><i class="bx bx-time-five me-1"></i>${data}</span>` : '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'recording_file',
                render: function (data, type) {
                    if (!data) return '<span class="text-muted">No Audio</span>';
                    if (type !== 'display') return data;
                    let fileUrl = APP_URL + '/' + data;
                    return `
                        <div style="min-width: 220px;">
                            <audio controls style="height: 35px; width: 100%; max-width: 260px;">
                                <source src="${fileUrl}">
                                Your browser does not support the audio element.
                            </audio>
                        </div>
                    `;
                }
            },
            {
                data: 'creator',
                render: function (data, type, row) {
                    let name = row.creator ? row.creator.name : 'System';
                    if (type !== 'display') return name;
                    return `<span class="badge bg-label-secondary">${name}</span>`;
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
                    let downloadBtn = row.recording_file ? `
                        <a class="dropdown-item" href="${APP_URL}/${row.recording_file}" download>
                            <i class="bx bx-download me-1"></i> Download Audio
                        </a>
                    ` : '';
                    return `
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                ${downloadBtn}
                                <a class="dropdown-item btn-edit-recording" href="javascript:void(0);" data-id="${row.call_id}">
                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                </a>
                                <a class="dropdown-item text-danger btn-delete-recording" href="javascript:void(0);" data-id="${row.call_id}">
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

    // Add Recording submit
    $('#addRecordingForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let formData = new FormData(this);
        let submitBtn = $('#addRecordingSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/call-recordings/store',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.status) {
                    $('#addRecordingModal').modal('hide');
                    form[0].reset();
                    recordingTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', 'An error occurred during audio upload.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Edit Recording open
    $(document).on('click', '.btn-edit-recording', function () {
        let id = $(this).data('id');
        let form = $('#editRecordingForm');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/call-recordings/edit/' + id,
            type: 'GET',
            success: function (response) {
                if (response.status) {
                    let item = response.data;
                    $('#edit_call_id').val(item.call_id);
                    $('#edit_recording_lead_id').val(item.lead_id);
                    $('#edit_recording_duration').val(item.duration || '');

                    $('#editRecordingModal').modal('show');
                }
            },
            error: function () {
                showAlert('danger', 'Failed to fetch recording details.');
            }
        });
    });

    // Update Recording submit
    $('#editRecordingForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let id = $('#edit_call_id').val();
        let formData = new FormData(this);
        let submitBtn = $('#editRecordingSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/call-recordings/update/' + id,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.status) {
                    $('#editRecordingModal').modal('hide');
                    recordingTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', 'Failed to update recording.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Delete Recording handler
    let deleteRecordingId = null;
    $(document).on('click', '.btn-delete-recording', function () {
        deleteRecordingId = $(this).data('id');
        $('#deleteRecordingModal').modal('show');
    });

    $('#confirmDeleteRecordingBtn').on('click', function () {
        if (!deleteRecordingId) return;

        let btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: APP_URL + '/admin/call-recordings/delete/' + deleteRecordingId,
            type: 'DELETE',
            success: function (response) {
                if (response.status) {
                    $('#deleteRecordingModal').modal('hide');
                    recordingTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Failed to delete recording.');
            },
            complete: function () {
                btn.prop('disabled', false);
                deleteRecordingId = null;
            }
        });
    });
});
