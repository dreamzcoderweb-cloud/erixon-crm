$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let creditTable = $('#credit-requests-table').DataTable({
        ajax: {
            url: APP_URL + '/admin/credit-requests/data',
            dataSrc: 'data',
            data: function (d) {
                d.status = $('#statusFilter').val();
            }
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
                    let name = row.username || row.customer?.name || 'N/A';
                    if (type !== 'display') return name;
                    return `<strong>${name}</strong>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    let phone = row.phone || row.customer?.mobile || 'N/A';
                    let email = row.email || row.customer?.email || '';
                    if (type !== 'display') return phone + ' ' + email;
                    return `<div><i class="bx bx-phone me-1"></i>${phone}</div><small class="text-muted">${email}</small>`;
                }
            },
            {
                data: 'credit_amount',
                render: function (val, type) {
                    if (type !== 'display') return val;
                    return `<span class="fw-bold text-success">₹${parseFloat(val).toFixed(2)}</span>`;
                }
            },
            {
                data: 'is_estimate',
                render: function (isEst, type) {
                    let text = isEst ? 'Estimate' : 'Standard';
                    if (type !== 'display') return text;
                    return isEst ? '<span class="badge bg-label-info">Estimate</span>' : '<span class="badge bg-label-primary">Standard</span>';
                }
            },
            {
                data: 'status',
                render: function (status, type) {
                    if (type !== 'display') return status;
                    if (status === 'Pending Admin Approval') return '<span class="badge bg-warning">Pending Admin</span>';
                    if (status === 'Forwarded to Support') return '<span class="badge bg-info">Forwarded to Support</span>';
                    if (status === 'Credit Added') return '<span class="badge bg-success">Credit Added</span>';
                    return '<span class="badge bg-danger">' + status + '</span>';
                }
            },
            {
                data: 'requester.name',
                defaultContent: 'System'
            },
            {
                data: 'created_at',
                render: function (date, type) {
                    if (type !== 'display') return date;
                    return date ? new Date(date).toLocaleDateString('en-GB') : '-';
                }
            },
            {
                data: null,
                orderable: false,
                render: function (data, type, row) {
                    let btns = '';
                    if (row.status === 'Pending Admin Approval') {
                        btns += `<button class="btn btn-xs btn-outline-primary me-1 admin-approve-btn" data-id="${row.credit_request_id}"><i class="bx bx-check"></i> Admin Approve</button>`;
                    }
                    if (row.status === 'Forwarded to Support') {
                        btns += `<button class="btn btn-xs btn-outline-success me-1 support-approve-btn" data-id="${row.credit_request_id}"><i class="bx bx-plus"></i> Add Credit</button>`;
                    }
                    btns += `<button class="btn btn-xs btn-outline-danger btn-delete-credit" data-id="${row.credit_request_id}"><i class="bx bx-trash"></i></button>`;
                    return btns;
                }
            }
        ],
        layout: {
            topStart: [
                'pageLength',
                {
                    buttons: [
                        {
                            extend: 'colvis',
                            text: '<i class="bx bx-columns me-1"></i> Column Visibility',
                            className: 'btn btn-secondary btn-sm me-1',
                            columns: ':not(:first-child):not(:last-child)'
                        },
                        { extend: 'copy', className: 'btn btn-secondary btn-sm me-1', exportOptions: { columns: ':visible:not(:last-child)' } },
                        { extend: 'csv', className: 'btn btn-secondary btn-sm me-1', exportOptions: { columns: ':visible:not(:last-child)' } },
                        { extend: 'excel', className: 'btn btn-secondary btn-sm me-1', exportOptions: { columns: ':visible:not(:last-child)' } },
                        { extend: 'pdf', className: 'btn btn-secondary btn-sm me-1', exportOptions: { columns: ':visible:not(:last-child)' } },
                        { extend: 'print', className: 'btn btn-secondary btn-sm', exportOptions: { columns: ':visible:not(:last-child)' } }
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
        ordering: false
    });

    $('#statusFilter').on('change', function () {
        creditTable.ajax.reload();
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

    $('.modal').on('hidden.bs.modal', function () {
        let form = $(this).find('form');
        if (form.length) {
            clearValidationErrors(form);
        }
    });

    // Add Credit Request Form Submit
    $('#addCreditRequestForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let submitBtn = $('#addCreditSubmitBtn');

        submitBtn.prop('disabled', true);
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/credit-requests/store',
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#addCreditRequestModal').modal('hide');
                    form[0].reset();
                    creditTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', xhr.responseJSON?.message || 'Error creating credit request.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Admin Approval Modal
    $(document).on('click', '.admin-approve-btn', function () {
        $('#admin_approve_id').val($(this).data('id'));
        $('#adminApproveModal').modal('show');
    });

    $('#adminApproveForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let id = $('#admin_approve_id').val();

        $.ajax({
            url: APP_URL + '/admin/credit-requests/approve-admin/' + id,
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#adminApproveModal').modal('hide');
                    creditTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                showAlert('danger', xhr.responseJSON?.message || 'Failed to approve request.');
            }
        });
    });

    // Support Approval Modal
    $(document).on('click', '.support-approve-btn', function () {
        $('#support_approve_id').val($(this).data('id'));
        $('#supportApproveModal').modal('show');
    });

    $('#supportApproveForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let id = $('#support_approve_id').val();

        $.ajax({
            url: APP_URL + '/admin/credit-requests/approve-support/' + id,
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#supportApproveModal').modal('hide');
                    creditTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                showAlert('danger', xhr.responseJSON?.message || 'Failed to process credit addition.');
            }
        });
    });

    // Delete Credit Request
    $(document).on('click', '.btn-delete-credit', function () {
        if (confirm('Delete this credit request?')) {
            let id = $(this).data('id');
            $.ajax({
                url: APP_URL + '/admin/credit-requests/delete/' + id,
                type: 'DELETE',
                success: function (response) {
                    if (response.status) {
                        creditTable.ajax.reload(null, false);
                        showAlert('success', response.message);
                    }
                },
                error: function () {
                    showAlert('danger', 'Failed to delete credit request.');
                }
            });
        }
    });
});
