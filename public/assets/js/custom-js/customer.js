$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let customerTableColumns = [
        {
            data: null,
            className: 'text-center',
            render: function (data, type, row, meta) {
                return meta.row + 1;
            }
        }
    ];

    if (window.configuredCustomerColumns && window.configuredCustomerColumns.length > 0) {
        window.configuredCustomerColumns.forEach(function (col) {
            let key = col.key;
            if (key === 'branch') {
                customerTableColumns.push({
                    data: 'branch',
                    render: function (data, type, row) {
                        if (row.branches && row.branches.length > 0) {
                            if (type !== 'display') return row.branches.map(b => b.name).join(', ');
                            return row.branches.map(b => `<span class="badge bg-label-primary mb-1 me-1"><i class="bx bx-buildings me-1"></i>${b.name}</span>`).join('');
                        }
                        let name = (row.branch && row.branch.name) ? row.branch.name : (row.branch_name || null);
                        if (!name) return type !== 'display' ? '-' : '<span class="text-muted">-</span>';
                        if (type !== 'display') return name;
                        return `<span class="badge bg-label-primary"><i class="bx bx-buildings me-1"></i>${name}</span>`;
                    }
                });
            } else if (key === 'team') {
                customerTableColumns.push({
                    data: 'team',
                    render: function (data, type, row) {
                        if (row.teams && row.teams.length > 0) {
                            if (type !== 'display') return row.teams.map(t => t.name).join(', ');
                            return row.teams.map(t => `<span class="badge bg-label-info mb-1 me-1"><i class="bx bx-group me-1"></i>${t.name}</span>`).join('');
                        }
                        let name = (row.team && row.team.name) ? row.team.name : (row.team_name || null);
                        if (!name) return type !== 'display' ? '-' : '<span class="text-muted">-</span>';
                        if (type !== 'display') return name;
                        return `<span class="badge bg-label-info"><i class="bx bx-group me-1"></i>${name}</span>`;
                    }
                });
            } else if (key === 'customer_type') {
                customerTableColumns.push({
                    data: 'customer_type',
                    render: function (data, type) {
                        if (type !== 'display') return (data || 'user').toUpperCase();
                        let badgeClass = data === 'reseller' ? 'bg-label-info' : 'bg-label-primary';
                        return `<span class="badge ${badgeClass} text-uppercase">${data || 'user'}</span>`;
                    }
                });
            } else if (key === 'name') {
                customerTableColumns.push({
                    data: 'name',
                    render: function (data, type) {
                        if (type !== 'display') return data || '';
                        return `<strong>${data}</strong>`;
                    }
                });
            } else if (key === 'company_name') {
                customerTableColumns.push({
                    data: 'company_name',
                    render: function (data, type) {
                        if (type !== 'display') return data || 'N/A';
                        return data ? data : '<span class="text-muted">N/A</span>';
                    }
                });
            } else if (key === 'mobile') {
                customerTableColumns.push({ data: 'mobile' });
            } else if (key === 'email') {
                customerTableColumns.push({
                    data: 'email',
                    render: function (data, type) {
                        if (type !== 'display') return data || 'N/A';
                        return data ? data : '<span class="text-muted">N/A</span>';
                    }
                });
            } else if (key === 'alternate_mobile') {
                customerTableColumns.push({
                    data: 'alternate_mobile',
                    render: function (data, type) {
                        if (type !== 'display') return data || 'N/A';
                        return data ? data : '<span class="text-muted">N/A</span>';
                    }
                });
            } else if (key === 'address') {
                customerTableColumns.push({
                    data: 'address',
                    render: function (data, type) {
                        if (type !== 'display') return data || 'N/A';
                        return data ? data : '<span class="text-muted">N/A</span>';
                    }
                });
            } else if (key === 'city') {
                customerTableColumns.push({
                    data: 'city',
                    render: function (data, type) {
                        if (type !== 'display') return data || 'N/A';
                        return data ? data : '<span class="text-muted">N/A</span>';
                    }
                });
            } else if (key === 'state') {
                customerTableColumns.push({
                    data: 'state',
                    render: function (data, type) {
                        if (type !== 'display') return data || 'N/A';
                        return data ? data : '<span class="text-muted">N/A</span>';
                    }
                });
            } else if (key === 'country') {
                customerTableColumns.push({
                    data: 'country',
                    render: function (data, type) {
                        if (type !== 'display') return data || 'N/A';
                        return data ? data : '<span class="text-muted">N/A</span>';
                    }
                });
            } else if (key === 'pincode') {
                customerTableColumns.push({
                    data: 'pincode',
                    render: function (data, type) {
                        if (type !== 'display') return data || 'N/A';
                        return data ? data : '<span class="text-muted">N/A</span>';
                    }
                });
            } else if (key === 'lead_source') {
                customerTableColumns.push({
                    data: null,
                    render: function (data, type, row) {
                        let name = (row.latest_lead && row.latest_lead.lead_source) ? row.latest_lead.lead_source.name : null;
                        if (!name) return type !== 'display' ? 'N/A' : '<span class="text-muted">N/A</span>';
                        if (type !== 'display') return name;
                        return `<span class="badge bg-label-info">${name}</span>`;
                    }
                });
            } else if (key === 'lead_stage') {
                customerTableColumns.push({
                    data: null,
                    render: function (data, type, row) {
                        let name = (row.lead_stage && row.lead_stage.name)
                            ? row.lead_stage.name
                            : (row.lead_stage_name || ((row.latest_lead && row.latest_lead.lead_stage) ? row.latest_lead.lead_stage.name : null));
                        if (!name) return type !== 'display' ? 'N/A' : '<span class="text-muted">N/A</span>';
                        if (type !== 'display') return name;
                        return `<span class="badge bg-label-warning">${name}</span>`;
                    }
                });
            } else if (key === 'lead_requirement') {
                customerTableColumns.push({
                    data: null,
                    render: function (data, type, row) {
                        let name = (row.lead_requirement && row.lead_requirement.name)
                            ? row.lead_requirement.name
                            : (row.lead_requirement_name || ((row.latest_lead && row.latest_lead.lead_requirement) ? row.latest_lead.lead_requirement.name : null));
                        if (!name) return type !== 'display' ? 'N/A' : '<span class="text-muted">N/A</span>';
                        if (type !== 'display') return name;
                        return `<span class="badge bg-label-primary">${name}</span>`;
                    }
                });
            } else if (key === 'lost_reason') {
                customerTableColumns.push({
                    data: null,
                    render: function (data, type, row) {
                        let reason = (row.latest_lead && row.latest_lead.lost_reason) ? (row.latest_lead.lost_reason.reason || row.latest_lead.lost_reason.name) : null;
                        if (!reason) return type !== 'display' ? '-' : '<span class="text-muted">-</span>';
                        if (type !== 'display') return reason;
                        return `<span class="badge bg-label-danger">${reason}</span>`;
                    }
                });
            } else if (key === 'created_at') {
                customerTableColumns.push({
                    data: 'created_at',
                    render: function (data, type) {
                        if (type !== 'display') return formatDate(data) || 'N/A';
                        return data ? formatDate(data) : '<span class="text-muted">N/A</span>';
                    }
                });
            } else if (key === 'created_by') {
                customerTableColumns.push({
                    data: 'creator',
                    render: function (data, type, row) {
                        let creatorName = data && data.name ? data.name : (row.created_by ? 'User #' + row.created_by : 'N/A');
                        if (type !== 'display') return creatorName;
                        return data && data.name ? `<span>${data.name}</span>` : (row.created_by ? `<span class="text-muted">User #${row.created_by}</span>` : '<span class="text-muted">N/A</span>');
                    }
                });
            } else if (key === 'status') {
                customerTableColumns.push({
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
                });
            } else {
                customerTableColumns.push({
                    data: null,
                    render: function (data, type, row) {
                        let val = (row.custom_fields && row.custom_fields[key] !== undefined && row.custom_fields[key] !== null) ? row.custom_fields[key] : null;
                        if (type !== 'display') return val !== null ? val : '';
                        if (val === null || val === undefined || val === '') return '<span class="text-muted">-</span>';

                        let fieldType = col.field ? col.field.field_type : '';
                        if (fieldType === 'Checkbox') {
                            if (val == 1 || val === true || val === '1' || val === 'Yes') {
                                return '<span class="badge bg-label-success">Yes</span>';
                            } else {
                                return '<span class="badge bg-label-secondary">No</span>';
                            }
                        }
                        if (fieldType === 'Date') {
                            return `<span class="text-nowrap">${formatDate(val)}</span>`;
                        }
                        return val;
                    }
                });
            }
        });
    }

    customerTableColumns.push({
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
                        <a class="dropdown-item btn-transfer-customer" href="javascript:void(0);" data-id="${row.customer_id}" data-name="${row.name}" data-branch="${row.branch_id || ''}" data-team="${row.team_id || ''}" data-owner="${row.owner_by || ''}">
                            <i class="bx bx-transfer me-1 text-primary"></i> Transfer
                        </a>
                        <a class="dropdown-item text-danger btn-delete-customer" href="javascript:void(0);" data-id="${row.customer_id}" data-name="${row.name}">
                            <i class="bx bx-trash me-1"></i> Delete
                        </a>
                    </div>
                </div>
            `;
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
                d.branch_id = $('#customer_filter_branch').val();
                d.team_id = $('#customer_filter_team').val();
                d.created_by = $('#customer_filter_created_by').val();
                d.customer_type = $('#customer_filter_type').val();
                d.status = $('#customer_filter_status').val();
                d.lead_requirement_id = $('#customer_filter_lead_requirement').val();
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
        columns: customerTableColumns,
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
            let inputName = field;
            if (field.indexOf('.') !== -1) {
                let parts = field.split('.');
                inputName = parts[0] + '[' + parts.slice(1).join('][') + ']';
            }

            let input = form.find(`[name="${inputName}"]`);
            if (!input.length) {
                input = form.find(`[name="${field}"]`);
            }

            if (!input.length) {
                return;
            }

            input.addClass('is-invalid');

            let errorDiv = input.siblings('.invalid-feedback').first();
            if (!errorDiv.length) {
                errorDiv = input.parent().find('.invalid-feedback').first();
            }
            if (!errorDiv.length) {
                errorDiv = $('<div class="invalid-feedback"></div>');
                input.after(errorDiv);
            }

            errorDiv.text(messages[0]).css('display', 'block');
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
                    $('#edit_owner_by').val(customer.owner_by || '');
                    $('#edit_assign_by').val(customer.assign_by || '');
                    $('#edit_lead_requirement_id').val(customer.lead_requirement_id || '');
                    $('#edit_lead_stage_id').val(customer.lead_stage_id || '');
                    let bIds = customer.branch_ids || (customer.branch_id ? [customer.branch_id] : []);
                    let tIds = customer.team_ids || (customer.team_id ? [customer.team_id] : []);
                    $('#edit_customer_branch_id').val(bIds).trigger('change.select2');
                    loadTeamsForSelect(bIds, $('#edit_customer_team_id'), tIds);
                    $('#edit_status').val(customer.status);

                    if (window.customCustomerFields && window.customCustomerFields.length > 0 && customer.custom_fields) {
                        window.customCustomerFields.forEach(function (cf) {
                            let val = customer.custom_fields[cf.field_name];
                            let el = $('#edit_cf_cust_' + cf.field_name);
                            if (el.length) {
                                if (cf.field_type === 'Checkbox') {
                                    el.prop('checked', val == 1 || val === true || val === '1' || val === 'Yes');
                                } else {
                                    el.val(val !== undefined && val !== null ? val : '');
                                }
                            }
                        });
                    }

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
        $('#customer_filter_branch').val('');
        $('#customer_filter_team').val('');
        $('#customer_filter_lead_requirement').val('');
        $('.customer-filter-date-group').addClass('d-none');
        customerTable.ajax.reload();
    });

    // Helper to dynamically load teams for a branch select element (supports single ID or array of IDs)
    function loadTeamsForSelect(branchId, targetSelect, selectedTeamIds = []) {
        if (!Array.isArray(selectedTeamIds)) {
            selectedTeamIds = selectedTeamIds ? [selectedTeamIds] : [];
        }
        let selectedTeamStrIds = selectedTeamIds.map(String);

        let branchQuery = '';
        if (Array.isArray(branchId)) {
            branchQuery = branchId.filter(Boolean).join(',');
        } else if (branchId) {
            branchQuery = String(branchId);
        }

        let isMultiple = targetSelect.prop('multiple');

        if (!branchQuery) {
            if (isMultiple) {
                targetSelect.empty().trigger('change');
            } else {
                targetSelect.html('<option value="">-- Select Team --</option>');
            }
            return;
        }

        if (!isMultiple) {
            targetSelect.html('<option value="">Loading teams...</option>');
        }

        $.get(APP_URL + '/admin/teams/by-branch/' + branchQuery, function(res) {
            let options = isMultiple ? '' : '<option value="">-- Select Team --</option>';
            if (res.status && res.data.length > 0) {
                res.data.forEach(t => {
                    let sel = selectedTeamStrIds.includes(String(t.id)) ? 'selected' : '';
                    let branchLabel = t.branch ? ` (${t.branch.name})` : '';
                    options += `<option value="${t.id}" data-branch="${t.branch_id}" ${sel}>${t.name} [${t.code}]${branchLabel}</option>`;
                });
            } else {
                if (!isMultiple) {
                    options = '<option value="">No active teams in selected branch(es)</option>';
                }
            }
            targetSelect.html(options).trigger('change');
        }).fail(function() {
            if (!isMultiple) {
                targetSelect.html('<option value="">Failed to load teams</option>');
            }
        });
    }

    // Initialize Select2 on modal open for add/edit customer multiple dropdowns
    $('#addCustomerModal').on('shown.bs.modal', function () {
        $('#add_customer_branch_id').select2({
            dropdownParent: $('#addCustomerModal'),
            placeholder: 'Select Branch(es)',
            allowClear: true,
            width: '100%'
        });
        $('#add_customer_team_id').select2({
            dropdownParent: $('#addCustomerModal'),
            placeholder: 'Select Team(s)',
            allowClear: true,
            width: '100%'
        });
    });

    $('#editCustomerModal').on('shown.bs.modal', function () {
        $('#edit_customer_branch_id').select2({
            dropdownParent: $('#editCustomerModal'),
            placeholder: 'Select Branch(es)',
            allowClear: true,
            width: '100%'
        });
        $('#edit_customer_team_id').select2({
            dropdownParent: $('#editCustomerModal'),
            placeholder: 'Select Team(s)',
            allowClear: true,
            width: '100%'
        });
    });

    // Filter Branch Change
    $('#customer_filter_branch').on('change', function() {
        let branchId = $(this).val();
        loadTeamsForSelect(branchId, $('#customer_filter_team'));
    });

    // Add Modal Branch Change
    $('#add_customer_branch_id').on('change', function() {
        let val = $(this).val();
        loadTeamsForSelect(val, $('#add_customer_team_id'), $('#add_customer_team_id').val() || []);
    });

    // Edit Modal Branch Change
    $('#edit_customer_branch_id').on('change', function() {
        let val = $(this).val();
        loadTeamsForSelect(val, $('#edit_customer_team_id'), $('#edit_customer_team_id').val() || []);
    });

    // Transfer Modal Branch Change
    $('#transfer_branch_id').on('change', function() {
        loadTeamsForSelect($(this).val(), $('#transfer_team_id'));
    });

    // Open Transfer Modal
    $(document).on('click', '.btn-transfer-customer', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let branchId = $(this).data('branch');
        let teamId = $(this).data('team');
        let ownerId = $(this).data('owner');

        $('#transfer_customer_id').val(id);
        $('#transfer_customer_name').text(name);
        $('#transfer_branch_id').val(branchId);
        $('#transfer_owner_by').val(ownerId);

        if (branchId) {
            loadTeamsForSelect(branchId, $('#transfer_team_id'), teamId);
        } else {
            $('#transfer_team_id').html('<option value="">-- Select Team --</option>');
        }

        $('#transferCustomerModal').modal('show');
    });

    // Transfer Form Submit
    $('#transferCustomerForm').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let id = $('#transfer_customer_id').val();
        let btn = $('#transferCustomerSubmitBtn');
        btn.prop('disabled', true).find('.spinner-border').removeClass('d-none');
        form.find('.is-invalid').removeClass('is-invalid');

        $.ajax({
            url: APP_URL + '/admin/customers/transfer/' + id,
            type: 'POST',
            data: form.serialize(),
            success: function(res) {
                btn.prop('disabled', false).find('.spinner-border').addClass('d-none');
                if (res.status) {
                    $('#transferCustomerModal').modal('hide');
                    customerTable.ajax.reload(null, false);
                    showAlert('success', res.message);
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).find('.spinner-border').addClass('d-none');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', 'Transfer failed. Please try again.');
                }
            }
        });
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
