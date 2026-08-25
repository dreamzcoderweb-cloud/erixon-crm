$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let customerTable = $('#customers-table').DataTable({
        ajax: {
            url: APP_URL + '/admin/customers/data',
            data: function (d) {
                d.filter_type = $('#customer_filter_period').val();
                d.date = $('#customer_filter_date').val();
                d.month = $('#customer_filter_month').val();
                d.start_date = $('#customer_filter_start_date').val();
                d.end_date = $('#customer_filter_end_date').val();
                d.created_by = $('#customer_filter_created_by').val();
                d.customer_type = $('#customer_filter_type').val();
                d.status = $('#customer_filter_status').val();
            },
            dataSrc: function (json) {
                if (json.resellcount !== undefined) {
                    $('#kpi_resellers').text(json.resellcount);
                }
                if (json.user !== undefined) {
                    $('#kpi_users').text(json.user);
                }
                if (json.staffcount !== undefined) {
                    $('#kpi_staffs').text(json.staffcount);
                }
                return json.data || [];
            }
        },
        columns: [
            {
                data: null,
                className: 'text-center',
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {
                data: 'customer_type',
                render: function (data, type) {
                    if (type !== 'display') return (data || 'user').toUpperCase();
                    let badgeClass = data === 'reseller' ? 'bg-label-info' : 'bg-label-primary';
                    return `<span class="badge ${badgeClass} text-uppercase">${data || 'user'}</span>`;
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
                data: 'company_name',
                render: function (data, type) {
                    if (type !== 'display') return data || 'N/A';
                    return data ? data : '<span class="text-muted">N/A</span>';
                }
            },
            { data: 'mobile' },
            {
                data: 'email',
                render: function (data, type) {
                    if (type !== 'display') return data || 'N/A';
                    return data ? data : '<span class="text-muted">N/A</span>';
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    let location = [row.city, row.state].filter(Boolean).join(', ');
                    if (type !== 'display') return location || 'N/A';
                    return location ? location : '<span class="text-muted">N/A</span>';
                }
            },
            {
                data: 'created_at',
                render: function (data, type) {
                    if (type !== 'display') return formatDate(data) || 'N/A';
                    return data ? formatDate(data) : '<span class="text-muted">N/A</span>';
                }
            },
            {
                data: 'creator',
                render: function (data, type, row) {
                    let creatorName = data && data.name ? data.name : (row.created_by ? 'User #' + row.created_by : 'N/A');
                    if (type !== 'display') return creatorName;
                    return data && data.name ? `<span>${data.name}</span>` : (row.created_by ? `<span class="text-muted">User #${row.created_by}</span>` : '<span class="text-muted">N/A</span>');
                }
            },
            {
                data: 'status',
                className: 'text-center',
                render: function (data, type, row) {
                    let statusText = data == 1 ? 'Active' : 'Inactive';
                    if (type !== 'display') return statusText;
                    let isChecked = data == 1 ? 'checked' : '';
                    let statusLabel = data == 1 ? '<span class="badge bg-label-success">Active</span>' : '<span class="badge bg-label-secondary">Inactive</span>';
                    return `
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            ${statusLabel}
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input btn-toggle-status" type="checkbox" data-id="${row.customer_id}" ${isChecked}>
                            </div>
                        </div>
                    `;
                }
            },
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    return `
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item btn-edit-customer" href="javascript:void(0);" data-id="${row.customer_id}">
                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                </a>
                                <a class="dropdown-item text-danger btn-delete-customer" href="javascript:void(0);" data-id="${row.customer_id}" data-name="${row.name}">
                                    <i class="bx bx-trash me-1"></i> Delete
                                </a>
                            </div>
                        </div>
                    `;
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
                    {
                        extend: 'copy',
                        className: 'btn btn-secondary btn-sm me-1',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-secondary btn-sm me-1',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        }
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-secondary btn-sm me-1',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        }
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-secondary btn-sm me-1',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-secondary btn-sm',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        }
                    }
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
        form.find('.invalid-feedback').text('').hide();
    }

    function showValidationErrors(form, errors) {
        clearValidationErrors(form);

        $.each(errors, function (field, messages) {
            const input = form.find(`[name="${field}"]`);

            if (!input.length) {
                return;
            }

            input.addClass('is-invalid');

            const errorDiv = input
                .siblings('.invalid-feedback')
                .first();

            if (errorDiv.length) {
                errorDiv.text(messages[0]).show();
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

    // Add Customer Form Submit
    $('#addCustomerForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let submitBtn = $('#addCustomerSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/customers/store',
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#addCustomerModal').modal('hide');
                    form[0].reset();
                    customerTable.ajax.reload(null, false);
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

    // Open Edit Customer Modal
    $(document).on('click', '.btn-edit-customer', function () {
        let id = $(this).data('id');
        let form = $('#editCustomerForm');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/customers/edit/' + id,
            type: 'GET',
            success: function (response) {
                if (response.status) {
                    let customer = response.data;
                    $('#edit_customer_id').val(customer.customer_id);
                    $('#edit_customer_type').val(customer.customer_type);
                    $('#edit_name').val(customer.name);
                    $('#edit_company_name').val(customer.company_name);
                    $('#edit_mobile').val(customer.mobile);
                    $('#edit_email').val(customer.email);
                    $('#edit_alternate_mobile').val(customer.alternate_mobile);
                    $('#edit_address').val(customer.address);
                    $('#edit_city').val(customer.city);
                    $('#edit_state').val(customer.state);
                    $('#edit_country').val(customer.country);
                    $('#edit_pincode').val(customer.pincode);
                    $('#edit_status').val(customer.status);

                    $('#editCustomerModal').modal('show');
                }
            },
            error: function () {
                showAlert('danger', 'Failed to fetch customer details.');
            }
        });
    });

    // Update Customer Form Submit
    $('#editCustomerForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let id = $('#edit_customer_id').val();
        let submitBtn = $('#editCustomerSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/customers/update/' + id,
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#editCustomerModal').modal('hide');
                    customerTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', 'An error occurred while updating customer.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Open Delete Confirmation Modal
    let deleteTargetId = null;
    $(document).on('click', '.btn-delete-customer', function () {
        deleteTargetId = $(this).data('id');
        let name = $(this).data('name');
        $('#delete_customer_name').text(name);
        $('#deleteCustomerModal').modal('show');
    });

    // Confirm Delete Customer
    $('#confirmDeleteCustomerBtn').on('click', function () {
        if (!deleteTargetId) return;

        let btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: APP_URL + '/admin/customers/delete/' + deleteTargetId,
            type: 'DELETE',
            success: function (response) {
                if (response.status) {
                    $('#deleteCustomerModal').modal('hide');
                    customerTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Failed to delete customer.');
            },
            complete: function () {
                btn.prop('disabled', false);
                deleteTargetId = null;
            }
        });
    });

    // Toggle Status
    $(document).on('change', '.btn-toggle-status', function () {
        let id = $(this).data('id');
        let checkbox = $(this);

        $.ajax({
            url: APP_URL + '/admin/customers/change-status/' + id,
            type: 'POST',
            success: function (response) {
                if (response.status) {
                    customerTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function () {
                checkbox.prop('checked', !checkbox.is(':checked'));
                showAlert('danger', 'Failed to update status.');
            }
        });
    });

    // Import Customer Form Submit
    $(document).on('submit', '#importCustomerForm', function (e) {
        e.preventDefault();
        let form = $(this);
        let formData = new FormData(this);
        let btn = $('#importCustomerBtn');

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Importing...');

        $.ajax({
            url: APP_URL + '/admin/customers/import',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                btn.prop('disabled', false).text('Upload & Import');
                if (res.status) {
                    $('#importCustomerModal').modal('hide');
                    form[0].reset();
                    customerTable.ajax.reload(null, false);
                    showAlert('success', res.message);
                }
            },
            error: function (xhr) {
                btn.prop('disabled', false).text('Upload & Import');
                showAlert('danger', xhr.responseJSON?.message || 'CSV Import failed. Please check file format.');
            }
        });
    });

    // Period Toggle Buttons
    $('.btn-customer-period').on('click', function () {
        $('.btn-customer-period').removeClass('active');
        $(this).addClass('active');

        let period = $(this).data('period');
        $('#customer_filter_period').val(period);
        $('.customer-filter-date-group').addClass('d-none');

        if (period === 'daily') {
            $('#customer_group_daily').removeClass('d-none');
        } else if (period === 'weekly') {
            $('#customer_group_custom_start').removeClass('d-none');
        } else if (period === 'monthly') {
            $('#customer_group_monthly').removeClass('d-none');
        } else if (period === 'custom') {
            $('#customer_group_custom_start').removeClass('d-none');
            $('#customer_group_custom_end').removeClass('d-none');
        }

        customerTable.ajax.reload();
    });

    // Customer Filter Form Submit
    $('#customerFilterForm').on('submit', function (e) {
        e.preventDefault();
        customerTable.ajax.reload();
    });

    // Reset Filters
    $('#resetCustomerFilterBtn').on('click', function () {
        $('#customerFilterForm')[0].reset();
        $('.btn-customer-period').removeClass('active');
        $('.btn-customer-period[data-period="all"]').addClass('active');
        $('#customer_filter_period').val('all');
        $('#customer_filter_created_by').val('');
        $('#customer_filter_type').val('');
        $('#customer_filter_status').val('');
        $('.customer-filter-date-group').addClass('d-none');
        customerTable.ajax.reload();
    });

    // KPI Card Click Quick-Filters
    $('#kpi_card_resellers').on('click', function () {
        let current = $('#customer_filter_type').val();
        if (current === 'reseller') {
            $('#customer_filter_type').val('');
        } else {
            $('#customer_filter_type').val('reseller');
        }
        customerTable.ajax.reload();
    });

    $('#kpi_card_users').on('click', function () {
        let current = $('#customer_filter_type').val();
        if (current === 'user') {
            $('#customer_filter_type').val('');
        } else {
            $('#customer_filter_type').val('user');
        }
        customerTable.ajax.reload();
    });
});
