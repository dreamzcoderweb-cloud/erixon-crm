$(document).ready(function () {
    if (!$('#followups-table').length) return;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let followupTable = $('#followups-table').DataTable({
        ajax: {
            url: APP_URL + '/admin/followups/data',
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
                data: 'followup_type',
                render: function (data, type) {
                    if (type !== 'display') return data || 'N/A';
                    let badgeClass = 'bg-label-primary';
                    if (data === 'Call') badgeClass = 'bg-label-info';
                    else if (data === 'Meeting') badgeClass = 'bg-label-warning';
                    else if (data === 'WhatsApp') badgeClass = 'bg-label-success';
                    else if (data === 'Email') badgeClass = 'bg-label-secondary';
                    return `<span class="badge ${badgeClass}">${data || 'N/A'}</span>`;
                }
            },
            {
                data: 'next_followup_date',
                render: function (data, type) {
                    if (!data) return type === 'display' ? '<span class="text-muted">N/A</span>' : '';
                    if (type !== 'display') return data;
                    let formatted = new Date(data).toLocaleString();
                    return `<span class="badge bg-label-dark"><i class="bx bx-calendar me-1"></i>${formatted}</span>`;
                }
            },
            {
                data: 'followup_status',
                render: function (data, type, row) {
                    if (type !== 'display') return data || 'Pending';
                    let badgeClass = 'bg-label-warning';
                    if (data === 'Completed') badgeClass = 'bg-label-success';
                    else if (data === 'Cancelled') badgeClass = 'bg-label-danger';
                    return `<span class="badge ${badgeClass}">${data || 'Pending'}</span>`;
                }
            },
            {
                data: 'forward_to_user',
                render: function (data, type, row) {
                    let name = row.forward_to_user ? row.forward_to_user.name : null;
                    if (type !== 'display') return name || 'N/A';
                    return name ? `<span class="badge bg-label-info">${name}</span>` : '<span class="text-muted">N/A</span>';
                }
            },
            {
                data: 'creator',
                render: function (data, type, row) {
                    let name = row.creator ? row.creator.name : null;
                    if (type !== 'display') return name || 'System';
                    return name ? name : '<span class="text-muted">System</span>';
                }
            },
            {
                data: 'remarks',
                render: function (data, type) {
                    if (type !== 'display') return data || '';
                    return data ? `<span title="${data}">${data.length > 30 ? data.substring(0, 30) + '...' : data}</span>` : '<span class="text-muted">-</span>';
                }
            },
            {
                data: null,
                orderable: false,
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-sm btn-outline-primary btn-edit-followup me-1" data-id="${row.followups_id}">
                            <i class="bx bx-edit-alt"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger btn-delete-followup" data-id="${row.followups_id}">
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

    // Add Follow-up Form Submit
    $('#addFollowupForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let submitBtn = $('#addFollowupSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/followups/store',
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#addFollowupModal').modal('hide');
                    form[0].reset();
                    followupTable.ajax.reload(null, false);
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

    // Open Edit Follow-up Modal
    $(document).on('click', '.btn-edit-followup', function () {
        let id = $(this).data('id');
        let form = $('#editFollowupForm');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/followups/edit/' + id,
            type: 'GET',
            success: function (response) {
                if (response.status) {
                    let followup = response.data;
                    $('#edit_followups_id').val(followup.followups_id);
                    $('#edit_followup_lead_id').val(followup.lead_id);
                    $('#edit_followup_type').val(followup.followup_type);
                    if (followup.next_followup_date) {
                        let dt = new Date(followup.next_followup_date);
                        let isoStr = new Date(dt.getTime() - (dt.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
                        $('#edit_followup_next_date').val(isoStr);
                    } else {
                        $('#edit_followup_next_date').val('');
                    }
                    $('#edit_followup_status').val(followup.followup_status);
                    $('#edit_followup_forward_to').val(followup.forward_to || '');
                    $('#edit_followup_remarks').val(followup.remarks || '');

                    $('#editFollowupModal').modal('show');
                }
            },
            error: function () {
                showAlert('danger', 'Failed to fetch follow-up details.');
            }
        });
    });

    // Update Follow-up Form Submit
    $('#editFollowupForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let id = $('#edit_followups_id').val();
        let submitBtn = $('#editFollowupSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/followups/update/' + id,
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#editFollowupModal').modal('hide');
                    followupTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', 'An error occurred while updating follow-up.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Open Delete Confirmation Modal
    let deleteFollowupId = null;
    $(document).on('click', '.btn-delete-followup', function () {
        deleteFollowupId = $(this).data('id');
        $('#deleteFollowupModal').modal('show');
    });

    // Confirm Delete Follow-up
    $('#confirmDeleteFollowupBtn').on('click', function () {
        if (!deleteFollowupId) return;

        let btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: APP_URL + '/admin/followups/delete/' + deleteFollowupId,
            type: 'DELETE',
            success: function (response) {
                if (response.status) {
                    $('#deleteFollowupModal').modal('hide');
                    followupTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Failed to delete follow-up.');
            },
            complete: function () {
                btn.prop('disabled', false);
                deleteFollowupId = null;
            }
        });
    });
});
