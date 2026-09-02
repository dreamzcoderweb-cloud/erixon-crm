$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let standardRenderers = {
        'customer_info': function (data, type, row) {
            let name = row.username || row.customer?.name || 'N/A';
            if (type !== 'display') return name;
            return `<strong>${name}</strong>`;
        },
        'contact_info': function (data, type, row) {
            let phone = row.phone || row.customer?.mobile || 'N/A';
            let email = row.email || row.customer?.email || '';
            if (type !== 'display') return phone + ' ' + email;
            return `<div><i class="bx bx-phone me-1"></i>${phone}</div><small class="text-muted">${email}</small>`;
        },
        'credit_amount': function (val, type, row) {
            let amount = row.credit_amount;
            if (type !== 'display') return amount;
            return `<span class="fw-bold text-success">₹${parseFloat(amount).toFixed(2)}</span>`;
        },
        'is_estimate': function (data, type, row) {
            let isEst = row.is_estimate;
            let text = isEst ? 'Estimate' : 'Standard';
            if (type !== 'display') return text;
            return isEst ? '<span class="badge bg-label-info">Estimate</span>' : '<span class="badge bg-label-primary">Standard</span>';
        },
        'status': function (data, type, row) {
            let status = row.status;
            if (type !== 'display') return status;
            if (status === 'Pending Admin Approval') return '<span class="badge bg-warning">Pending Admin</span>';
            if (status === 'Forwarded to Support') return '<span class="badge bg-info">Forwarded to Support</span>';
            if (status === 'Credit Added') return '<span class="badge bg-success">Credit Added</span>';
            return '<span class="badge bg-danger">' + status + '</span>';
        },
        'requested_by': function (data, type, row) {
            return row.requester ? row.requester.name : 'System';
        },
        'lead_source': function (data, type, row) {
            let name = row.lead_source ? row.lead_source.name : 'N/A';
            if (type !== 'display') return name;
            return name !== 'N/A' ? `<span class="badge bg-label-secondary">${name}</span>` : '<span class="text-muted">N/A</span>';
        },
        'created_at': function (data, type, row) {
            let date = row.created_at;
            if (type !== 'display') return date;
            return date ? new Date(date).toLocaleDateString('en-GB') : '-';
        }
    };

    let columnsConfig = [
        {
            data: null,
            render: function (data, type, row, meta) {
                return meta.row + 1;
            }
        }
    ];

    if (window.visibleCreditRequestColumns && window.visibleCreditRequestColumns.length > 0) {
        window.visibleCreditRequestColumns.forEach(function (col) {
            if (col.type === 'standard') {
                let renderer = standardRenderers[col.key];
                if (renderer) {
                    columnsConfig.push({
                        data: null,
                        render: renderer
                    });
                } else {
                    columnsConfig.push({
                        data: col.key,
                        defaultContent: '-'
                    });
                }
            } else if (col.type === 'custom') {
                columnsConfig.push({
                    data: null,
                    render: function (data, type, row) {
                        let cfVal = (row.custom_fields && row.custom_fields[col.key] !== undefined) ? row.custom_fields[col.key] : '-';
                        if (cfVal === '1' && col.field && col.field.field_type === 'Checkbox') cfVal = 'Yes';
                        if (cfVal === '0' && col.field && col.field.field_type === 'Checkbox') cfVal = 'No';
                        return cfVal !== null && cfVal !== '' ? cfVal : '-';
                    }
                });
            }
        });
    } else {
        columnsConfig.push({ data: null, render: standardRenderers['customer_info'] });
        columnsConfig.push({ data: null, render: standardRenderers['contact_info'] });
        columnsConfig.push({ data: null, render: standardRenderers['lead_source'] });
        columnsConfig.push({ data: null, render: standardRenderers['credit_amount'] });
        columnsConfig.push({ data: null, render: standardRenderers['is_estimate'] });
        columnsConfig.push({ data: null, render: standardRenderers['status'] });
        columnsConfig.push({ data: null, render: standardRenderers['requested_by'] });
        columnsConfig.push({ data: null, render: standardRenderers['created_at'] });
    }

    columnsConfig.push({
        data: null,
        orderable: false,
        className: 'text-center',
        render: function (data, type, row) {
            let menuItems = '';

            if (row.status === 'Pending Admin Approval') {
                menuItems += `
                    <a class="dropdown-item admin-approve-btn text-primary" href="javascript:void(0);" data-id="${row.credit_request_id}">
                        <i class="bx bx-check me-1"></i> Admin Approve
                    </a>
                `;
            }

            if (row.status === 'Forwarded to Support') {
                menuItems += `
                    <a class="dropdown-item support-approve-btn text-success" href="javascript:void(0);" data-id="${row.credit_request_id}">
                        <i class="bx bx-plus me-1"></i> Add Credit
                    </a>
                `;
            }

            if (row.status === 'Pending Admin Approval' || row.status === 'Forwarded to Support') {
                menuItems += `
                    <a class="dropdown-item reject-credit-btn text-warning" href="javascript:void(0);" data-id="${row.credit_request_id}">
                        <i class="bx bx-x-circle me-1"></i> Reject
                    </a>
                `;
            }

            menuItems += `
                <a class="dropdown-item text-danger btn-delete-credit" href="javascript:void(0);" data-id="${row.credit_request_id}">
                    <i class="bx bx-trash me-1"></i> Delete
                </a>
            `;

            return `
                <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        ${menuItems}
                    </div>
                </div>
            `;
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
        columns: columnsConfig,
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
            let fieldSelector = field;
            if (field.indexOf('.') !== -1) {
                let parts = field.split('.');
                fieldSelector = parts[0] + '[' + parts.slice(1).join('][') + ']';
            }
            let input = form.find(`[name="${fieldSelector}"]`);
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

    // Reject Credit Request Modal Open
    $(document).on('click', '.reject-credit-btn', function () {
        let id = $(this).data('id');
        let form = $('#rejectCreditForm');
        clearValidationErrors(form);
        form[0].reset();
        $('#reject_credit_id').val(id);
        $('#rejectCreditModal').modal('show');
    });

    // Reject Credit Request Form Submit
    $('#rejectCreditForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let id = $('#reject_credit_id').val();
        let submitBtn = form.find('button[type="submit"]');

        submitBtn.prop('disabled', true);
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/credit-requests/reject/' + id,
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#rejectCreditModal').modal('hide');
                    creditTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', xhr.responseJSON?.message || 'Failed to reject credit request.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
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
