$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let deleteDemoProcessId = null;

    // Helper: Show Bootstrap Alerts
    function showAlert(type, message) {
        let alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('#alert-container').html(alertHtml);
        setTimeout(function () {
            $('.alert').alert('close');
        }, 5000);
    }

    // Helper: Clear Form Validation Errors
    function clearValidationErrors(form) {
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.select2-container').removeClass('is-invalid');
        form.find('.invalid-feedback').text('').css('display', 'none');
    }

    // Helper: Display Form Validation Errors
    function displayValidationErrors(form, errors) {
        clearValidationErrors(form);
        $.each(errors, function (field, messages) {
            let inputName = field;
            if (field.indexOf('.') !== -1) {
                let parts = field.split('.');
                inputName = parts[0] + '[' + parts.slice(1).join('][') + ']';
            }

            let input = form.find(`[name="${inputName}"]`);
            if (!input.length) {
                input = form.find(`[name="${field}"], [name="${field}[]"]`);
            }
            if (!input.length) {
                input = form.find(`#add_${field}, #edit_${field}`);
            }

            if (input.length) {
                input.addClass('is-invalid');
                if (input.hasClass('select2-hidden-accessible') || input.next('.select2-container').length) {
                    input.next('.select2-container').addClass('is-invalid');
                }
                let errorDiv = input.siblings('.invalid-feedback').first();
                if (!errorDiv.length) {
                    errorDiv = input.parent().find('.invalid-feedback').first();
                }
                if (!errorDiv.length) {
                    errorDiv = $('<div class="invalid-feedback"></div>');
                    input.after(errorDiv);
                }
                errorDiv.text(messages[0] || 'This field is required').css('display', 'block');
            }
        });
    }

    // Helper: Validate Required Fields Client-Side
    function validateDemoProcessForm(form) {
        clearValidationErrors(form);
        let isValid = true;
        let errors = {};

        // 1. Check specific standard fields
        let customerName = form.find('[name="customer_name"]').val();
        let customerPhone = form.find('[name="customer_phone"]').val();
        let demoDate = form.find('[name="demo_date"]').val();
        let demoTime = form.find('[name="demo_time"]').val();

        if (!customerName || customerName.trim() === '') {
            errors['customer_name'] = ['This field is required'];
            isValid = false;
        }
        if (!customerPhone || customerPhone.trim() === '') {
            errors['customer_phone'] = ['This field is required'];
            isValid = false;
        }
        if (!demoDate || demoDate.trim() === '') {
            errors['demo_date'] = ['This field is required'];
            isValid = false;
        }
        if (!demoTime || demoTime.trim() === '') {
            errors['demo_time'] = ['This field is required'];
            isValid = false;
        }

        // 2. Check any input, select, textarea with required attribute (including all required custom fields)
        form.find('input[required], select[required], textarea[required]').each(function () {
            let el = $(this);
            let name = el.attr('name');
            if (!name) return;

            if (el.is(':checkbox')) {
                if (!el.is(':checked')) {
                    errors[name] = ['This field is required'];
                    isValid = false;
                }
            } else {
                let val = el.val();
                if (!val || (typeof val === 'string' && val.trim() === '')) {
                    errors[name] = ['This field is required'];
                    isValid = false;
                }
            }
        });

        if (!isValid) {
            displayValidationErrors(form, errors);
        }
        return isValid;
    }

    // Initialize Select2 with Search option for Customer Name inside Modals
    $('#addDemoProcessModal').on('shown.bs.modal', function () {
        $('#add_customer_name').select2({
            dropdownParent: $('#addDemoProcessModal'),
            width: '100%',
            placeholder: '-- Select Customer --'
        });
    });

    $('#editDemoProcessModal').on('shown.bs.modal', function () {
        $('#edit_customer_name').select2({
            dropdownParent: $('#editDemoProcessModal'),
            width: '100%',
            placeholder: '-- Select Customer --'
        });
    });

    // Autofill Phone Number & Customer Type when Customer Name is selected
    $(document).on('change', '#add_customer_name', function () {
        let selectedOpt = $(this).find(':selected');
        let phone = selectedOpt.data('phone') || '';
        let type = selectedOpt.data('type') || 'User';
        $('#add_customer_phone').val(phone);
        $('#add_customer_type').val(type);
    });

    $(document).on('change', '#edit_customer_name', function () {
        let selectedOpt = $(this).find(':selected');
        let phone = selectedOpt.data('phone') || '';
        let type = selectedOpt.data('type') || 'User';
        if (phone) {
            $('#edit_customer_phone').val(phone);
        }
        if (type) {
            $('#edit_customer_type').val(type);
        }
    });

    // DataTables Initialization
    let demoProcessTable = $('#demo-processes-table').DataTable({
        order: [],
        ajax: {
            url: APP_URL + '/admin/demo-processes/data',
            data: function (d) {
                d.filter_type = $('#demo_filter_period').val();
                d.date = $('#demo_filter_date').val();
                d.month = $('#demo_filter_month').val();
                d.start_date = $('#demo_filter_start_date').val();
                d.end_date = $('#demo_filter_end_date').val();
                d.status = $('#demo_filter_status').val();
                d.created_by = $('#demo_filter_created_by').val();
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
                data: 'customer_name',
                render: function (data, type, row) {
                    if (type !== 'display') return data;
                    return `<strong>${data}</strong><br><small class="text-muted"><i class="bx bx-phone me-1"></i>${row.customer_phone || 'N/A'}</small>`;
                }
            },
            {
                data: 'product_text',
                render: function (data, type, row) {
                    if (type !== 'display') return data;
                    let sourceBadge = `<span class="badge bg-label-info mb-1">${row.lead_source || 'N/A'}</span>`;
                    return `${sourceBadge}<br><small class="text-dark">${data || 'N/A'}</small>`;
                }
            },
            {
                data: 'demo_date_formatted',
                render: function (data, type, row) {
                    if (type !== 'display') return data;
                    return `<i class="bx bx-calendar me-1"></i>${data}<br><small class="text-muted"><i class="bx bx-time me-1"></i>${row.demo_time || 'N/A'}</small>`;
                }
            },
            {
                data: 'customer_type',
                render: function (data, type) {
                    if (type !== 'display') return data || 'User';
                    return `<span class="badge bg-label-primary text-uppercase">${data || 'User'}</span>`;
                }
            },
            {
                data: 'creator',
                render: function (data, type) {
                    let name = data && data.name ? data.name : 'Sales Staff';
                    if (type !== 'display') return name;
                    return `<strong>${name}</strong>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    let pmName = row.assigned_user ? row.assigned_user.name : '<span class="text-muted">Unassigned</span>';
                    let supName = row.sub_assigned_user ? row.sub_assigned_user.name : '<span class="text-muted">Unassigned</span>';
                    if (type !== 'display') return `${pmName} / ${supName}`;
                    return `<small><strong>PM:</strong> ${pmName}</small><br><small><strong>Support:</strong> ${supName}</small>`;
                }
            },
            {
                data: 'status',
                render: function (data, type, row) {
                    let status = data || 'Pending';
                    if (type !== 'display') return status;

                    if (status === 'Finished') {
                        return `<button type="button" class="btn btn-xs btn-success btn-toggle-demo-status" data-id="${row.demo_process_id}" data-status="Pending" title="Click to change to Pending">
                                    <i class="bx bx-check-circle me-1"></i> Finished
                                </button>`;
                    } else {
                        return `<button type="button" class="btn btn-xs btn-outline-warning btn-toggle-demo-status" data-id="${row.demo_process_id}" data-status="Finished" title="Click to mark as Finished">
                                    <i class="bx bx-time me-1"></i> Pending
                                </button>`;
                    }
                }
            },
            {
                data: null,
                orderable: false,
                render: function (data, type, row) {
                    let custName = row.customer_name ? row.customer_name.replace(/'/g, "\\'") : '';
                    return `
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item btn-edit-demo" href="javascript:void(0);" data-id="${row.demo_process_id}">
                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                </a>
                                <a class="dropdown-item text-danger btn-delete-demo" href="javascript:void(0);" data-id="${row.demo_process_id}" data-name="${custName}">
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
                            className: 'btn btn-secondary btn-sm me-1 ms-2',
                            columns: ':not(:last-child)'
                        },
                        {
                            extend: 'copy',
                            className: 'btn btn-secondary btn-sm me-1',
                            exportOptions: { columns: ':not(:last-child)' }
                        },
                        {
                            extend: 'csv',
                            className: 'btn btn-secondary btn-sm me-1',
                            exportOptions: { columns: ':not(:last-child)' }
                        },
                        {
                            extend: 'excel',
                            className: 'btn btn-secondary btn-sm me-1',
                            exportOptions: { columns: ':not(:last-child)' }
                        },
                        {
                            extend: 'pdf',
                            className: 'btn btn-secondary btn-sm me-1',
                            exportOptions: { columns: ':not(:last-child)' }
                        },
                        {
                            extend: 'print',
                            className: 'btn btn-secondary btn-sm',
                            exportOptions: { columns: ':not(:last-child)' }
                        }
                    ]
                }
            ],
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging',
        },
         // Disable sorting completely
            ordering: false,
        language: {
            emptyTable: 'No Demo Process records found'
        }
    });

    // Clear validation on modal hide
    $('.modal').on('hidden.bs.modal', function () {
        let form = $(this).find('form');
        if (form.length) {
            clearValidationErrors(form);
            form[0].reset();
        }
    });

    // Submit Add Demo Process Form
    $('#addDemoProcessForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        if (!validateDemoProcessForm(form)) {
            return false;
        }

        let submitBtn = $('#addDemoProcessSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        clearValidationErrors(form);
        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');

        $.ajax({
            url: APP_URL + '/admin/demo-processes/store',
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#addDemoProcessModal').modal('hide');
                    form[0].reset();
                    $('#add_product_names').val(null).trigger('change');
                    demoProcessTable.ajax.reload();
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    displayValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', xhr.responseJSON?.message || 'Failed to create Demo Process.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Open Edit Demo Process Modal
    $(document).on('click', '.btn-edit-demo', function () {
        let id = $(this).data('id');
        $.ajax({
            url: APP_URL + '/admin/demo-processes/edit/' + id,
            type: 'GET',
            success: function (response) {
                if (response.status && response.data) {
                    let d = response.data;
                    $('#edit_demo_process_id').val(d.demo_process_id);
                    $('#edit_customer_name').val(d.customer_name).trigger('change');
                    $('#edit_customer_phone').val(d.customer_phone);
                    $('#edit_lead_source_id').val(d.lead_source_id);
                    $('#edit_demo_date').val(d.demo_date);
                    $('#edit_demo_time').val(d.demo_time);
                    $('#edit_customer_type').val(d.customer_type || 'User');
                    $('#edit_status').val(d.status || 'Pending');
                    $('#edit_assigned_by').val(d.assigned_by || '');
                    $('#edit_sub_assigned_by').val(d.sub_assigned_by || '');
                    $('#edit_remarks').val(d.remarks || '');

                    if (d.product_names) {
                        $('#edit_product_names').val(d.product_names).trigger('change');
                    } else {
                        $('#edit_product_names').val(null).trigger('change');
                    }

                    // Populate custom fields
                    let editForm = $('#editDemoProcessForm');
                    editForm.find('[name^="custom_fields["]').val('');
                    editForm.find('input[type="checkbox"][name^="custom_fields["]').prop('checked', false);
                    if (d.custom_fields && typeof d.custom_fields === 'object') {
                        $.each(d.custom_fields, function (cfKey, cfVal) {
                            let cfInput = editForm.find(`[name="custom_fields[${cfKey}]"]`);
                            if (cfInput.is(':checkbox')) {
                                cfInput.prop('checked', cfVal == 1 || cfVal === true || cfVal === '1');
                            } else {
                                cfInput.val(cfVal);
                            }
                        });
                    }

                    $('#editDemoProcessModal').modal('show');
                }
            },
            error: function () {
                showAlert('danger', 'Failed to fetch Demo Process details.');
            }
        });
    });

    // Submit Edit Demo Process Form
    $('#editDemoProcessForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        if (!validateDemoProcessForm(form)) {
            return false;
        }

        let id = $('#edit_demo_process_id').val();
        let submitBtn = $('#editDemoProcessSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        clearValidationErrors(form);
        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');

        $.ajax({
            url: APP_URL + '/admin/demo-processes/update/' + id,
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#editDemoProcessModal').modal('hide');
                    demoProcessTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    displayValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', xhr.responseJSON?.message || 'Failed to update Demo Process.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Toggle Demo Process Status
    $(document).on('click', '.btn-toggle-demo-status', function () {
        let btn = $(this);
        let id = btn.data('id');
        let newStatus = btn.data('status');
        btn.prop('disabled', true);

        $.ajax({
            url: APP_URL + '/admin/demo-processes/update-status/' + id,
            type: 'POST',
            data: { status: newStatus },
            success: function (response) {
                if (response.status) {
                    demoProcessTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Failed to update status.');
            },
            complete: function () {
                btn.prop('disabled', false);
            }
        });
    });

    // Open Delete Demo Process Modal
    $(document).on('click', '.btn-delete-demo', function () {
        deleteDemoProcessId = $(this).data('id');
        let name = $(this).data('name');
        $('#delete_customer_name').text(name);
        $('#deleteDemoProcessModal').modal('show');
    });

    // Confirm Delete Demo Process
    $('#confirmDeleteDemoProcessBtn').on('click', function () {
        if (!deleteDemoProcessId) return;

        let btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: APP_URL + '/admin/demo-processes/delete/' + deleteDemoProcessId,
            type: 'DELETE',
            success: function (response) {
                if (response.status) {
                    $('#deleteDemoProcessModal').modal('hide');
                    demoProcessTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Failed to delete Demo Process record.');
            },
            complete: function () {
                btn.prop('disabled', false);
                deleteDemoProcessId = null;
            }
        });
    });

    // Period Toggle Buttons
    $('.btn-demo-period').on('click', function () {
        $('.btn-demo-period').removeClass('active');
        $(this).addClass('active');

        let period = $(this).data('period');
        $('#demo_filter_period').val(period);
        $('.demo-filter-date-group').addClass('d-none');

        if (period === 'daily') {
            $('#demo_group_daily').removeClass('d-none');
        } else if (period === 'weekly') {
            $('#demo_group_custom_start').removeClass('d-none');
        } else if (period === 'monthly') {
            $('#demo_group_monthly').removeClass('d-none');
        } else if (period === 'custom') {
            $('#demo_group_custom_start').removeClass('d-none');
            $('#demo_group_custom_end').removeClass('d-none');
        }

        demoProcessTable.ajax.reload();
    });

    // Filter Form Submit
    $('#demoProcessFilterForm').on('submit', function (e) {
        e.preventDefault();
        demoProcessTable.ajax.reload();
    });

    // Reset Filters
    $('#resetDemoFilterBtn').on('click', function () {
        $('#demoProcessFilterForm')[0].reset();
        $('.btn-demo-period').removeClass('active');
        $('.btn-demo-period[data-period="all"]').addClass('active');
        $('#demo_filter_period').val('all');
        $('#demo_filter_status').val('');
        $('#demo_filter_created_by').val('');
        $('.demo-filter-date-group').addClass('d-none');
        demoProcessTable.ajax.reload();
    });
});
