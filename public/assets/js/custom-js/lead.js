$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize Select2 Search for Lead Filter Customer Dropdown
    if ($('#lead_filter_customer_id').length && $.fn.select2) {
        $('#lead_filter_customer_id').select2({
            placeholder: '-- All Customers --',
            allowClear: true,
            width: '100%'
        });
    }

    let leadTableColumns = [
        {
            data: null,
            className: 'text-center',
            render: function (data, type, row, meta) {
                return meta.row + 1;
            }
        }
    ];

    if (window.configuredLeadColumns && window.configuredLeadColumns.length > 0) {
        window.configuredLeadColumns.forEach(function (col) {
            let key = col.key;
            if (key === 'lead_title') {
                leadTableColumns.push({
                    data: 'lead_title',
                    render: function (data, type) {
                        if (type !== 'display') return data || '';
                        return `<strong>${data}</strong>`;
                    }
                });
            } else if (key === 'customer') {
                leadTableColumns.push({
                    data: 'customer',
                    render: function (data, type, row) {
                        let name = row.customer_name || (data ? data.name : null);
                        let mobile = row.mobile || (data ? data.mobile : null);
                        let customerId = row.customer_id || (data ? data.customer_id : null);
                        if (!name && !mobile) return type !== 'display' ? 'N/A' : '<span class="text-muted">N/A</span>';
                        let mobileDisplay = mobile ? `<br><small class="text-muted"><i class="bx bx-phone me-1"></i>${mobile}</small>` : '';
                        let textDisplay = mobile ? `${name} (${mobile})` : (name || 'N/A');
                        if (type !== 'display') return textDisplay;
                        if (customerId) {
                            return `<div><a href="javascript:void(0);" class="fw-bold text-primary view-customer-details-btn text-decoration-underline" data-customer-id="${customerId}" title="Click to view Customer Call & Sale Closed Details"><i class="bx bx-user me-1"></i>${name || 'Customer'}</a>${mobileDisplay}</div>`;
                        }
                        return `<div><strong>${name || 'N/A'}</strong>${mobileDisplay}</div>`;
                    }
                });
            } else if (key === 'branch') {
                leadTableColumns.push({
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
                leadTableColumns.push({
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
            } else if (key === 'lead_source') {
                leadTableColumns.push({
                    data: 'lead_source',
                    render: function (data, type) {
                        if (!data) return type !== 'display' ? 'N/A' : '<span class="text-muted">N/A</span>';
                        if (type !== 'display') return data.name;
                        return `<span class="badge bg-label-info">${data.name}</span>`;
                    }
                });
            } else if (key === 'lead_stage') {
                leadTableColumns.push({
                    data: 'lead_stage',
                    render: function (data, type) {
                        if (!data) return type !== 'display' ? 'N/A' : '<span class="text-muted">N/A</span>';
                        if (type !== 'display') return data.name;
                        return `<span class="badge bg-label-warning">${data.name}</span>`;
                    }
                });
            } else if (key === 'lead_requirement') {
                leadTableColumns.push({
                    data: 'lead_requirement',
                    render: function (data, type) {
                        if (!data) return type !== 'display' ? 'N/A' : '<span class="text-muted">N/A</span>';
                        if (type !== 'display') return data.name;
                        return `<span class="badge bg-label-primary">${data.name}</span>`;
                    }
                });
            } else if (key === 'lost_reason') {
                leadTableColumns.push({
                    data: 'lost_reason',
                    render: function (data, type) {
                        if (!data) return type !== 'display' ? '-' : '<span class="text-muted">-</span>';
                        let reason = data.reason || data.name || '-';
                        if (type !== 'display') return reason;
                        return `<span class="badge bg-label-danger">${reason}</span>`;
                    }
                });
            } else if (key === 'priority') {
                leadTableColumns.push({
                    data: 'priority',
                    render: function (data, type) {
                        if (type !== 'display') return (data || '').toUpperCase();
                        let badgeClass = 'bg-label-secondary';
                        if (data === 'urgent') badgeClass = 'bg-label-danger';
                        else if (data === 'high') badgeClass = 'bg-label-warning';
                        else if (data === 'medium') badgeClass = 'bg-label-primary';
                        else if (data === 'low') badgeClass = 'bg-label-info';
                        return `<span class="badge ${badgeClass} text-uppercase">${data}</span>`;
                    }
                });
            } else if (key === 'expected_amount') {
                leadTableColumns.push({
                    data: 'expected_amount',
                    render: function (data, type) {
                        let formatted = data ? `₹${parseFloat(data).toLocaleString('en-IN', { minimumFractionDigits: 2 })}` : '-';
                        if (type !== 'display') return data ? parseFloat(data).toFixed(2) : '-';
                        return data ? formatted : '<span class="text-muted">-</span>';
                    }
                });
            } else if (key === 'assigned_to') {
                leadTableColumns.push({
                    data: 'assigned_user',
                    render: function (data, type) {
                        if (!data) return type !== 'display' ? 'Unassigned' : '<span class="badge bg-label-secondary">Unassigned</span>';
                        return data.name;
                    }
                });
            } else if (key === 'next_followup_date') {
                leadTableColumns.push({
                    data: 'next_followup_date',
                    render: function (data, type) {
                        if (type !== 'display') return data || '-';
                        return data ? `<span class="text-nowrap"><i class="bx bx-calendar me-1"></i>${formatDate(data)}</span>` : '<span class="text-muted">-</span>';
                    }
                });
            } else if (key === 'created_at') {
                leadTableColumns.push({
                    data: 'created_at',
                    className: 'text-center',
                    render: function (data, type) {
                        if (type !== 'display') return data || '-';
                        return data ? `<span class="text-nowrap">${formatDate(data)}</span>` : '<span class="text-muted">-</span>';
                    }
                });
            } else if (key === 'created_by') {
                leadTableColumns.push({
                    data: 'creator',
                    className: 'text-center',
                    render: function (data, type, row) {
                        let creatorName = data && data.name ? data.name : (row.created_by ? 'User #' + row.created_by : 'N/A');
                        if (type !== 'display') return creatorName;
                        return data && data.name ? `<span>${data.name}</span>` : (row.created_by ? `<span class="text-muted">User #${row.created_by}</span>` : '<span class="text-muted">N/A</span>');
                    }
                });
            } else if (key === 'status') {
                leadTableColumns.push({
                    data: 'status',
                    className: 'text-center',
                    render: function (data, type, row) {
                        let statusText = data == 1 ? 'Active' : 'Closed';
                        if (type !== 'display') return statusText;
                        let isChecked = data == 1 ? 'checked' : '';
                        let statusLabel = data == 1 ? '<span class="badge bg-label-success">Active</span>' : '<span class="badge bg-label-secondary">Closed</span>';
                        return `
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                ${statusLabel}
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input btn-toggle-lead-status" type="checkbox" data-id="${row.lead_id}" ${isChecked}>
                                </div>
                            </div>
                        `;
                    }
                });
            } else {
                leadTableColumns.push({
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

    leadTableColumns.push({
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
                        <a class="dropdown-item btn-edit-lead" href="javascript:void(0);" data-id="${row.lead_id}">
                            <i class="bx bx-edit-alt me-1"></i> Edit
                        </a>
                        <a class="dropdown-item text-danger btn-delete-lead" href="javascript:void(0);" data-id="${row.lead_id}" data-title="${row.lead_title}">
                            <i class="bx bx-trash me-1"></i> Delete
                        </a>
                    </div>
                </div>
            `;
        }
    });

    let leadTable = $('#leads-table').DataTable({
        ajax: {
            url: APP_URL + '/admin/leads/data',
            data: function (d) {
                d.filter_type = $('#lead_filter_period').val();
                d.date = $('#lead_filter_date').val();
                d.month = $('#lead_filter_month').val();
                d.start_date = $('#lead_filter_start_date').val();
                d.end_date = $('#lead_filter_end_date').val();
                d.lead_requirement_id = $('#lead_filter_requirement_id').val();
                d.lead_source_id = $('#lead_filter_source_id').val();
                d.lead_stage_id = $('#lead_filter_stage_id').val();
                d.lost_reason_id = $('#lead_filter_lost_reason_id').val();
                d.customer_id = $('#lead_filter_customer_id').val();
                d.created_by = $('#lead_filter_created_by').val();
                d.status = $('#lead_filter_status').val();
            },
            dataSrc: function (json) {
                if (json.total_leads !== undefined) {
                    $('#kpi_total_leads').text(json.total_leads);
                }
                if (json.staff_created_count !== undefined) {
                    $('#kpi_staff_created_leads').text(json.staff_created_count);
                }
                return json.data || [];
            }
        },
        columns: leadTableColumns,
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
            let inputName = field;
            if (field.indexOf('.') !== -1) {
                let parts = field.split('.');
                inputName = parts[0] + '[' + parts.slice(1).join('][') + ']';
            }
            let input = form.find(`[name="${inputName}"]`);
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

    // Dynamic toggle for Lost Reason and Next Follow-up Date based on Lead Stage
    function handleLeadStageChanges(stageSelect, lostReasonGroup, lostReasonSelect, followupGroup, followupInput) {
        let selectedOption = stageSelect.find('option:selected');
        let stageName = (selectedOption.data('name') || selectedOption.text() || '').toLowerCase().trim();

        let isSaleClosed = stageName.indexOf('sale') !== -1 && stageName.indexOf('close') !== -1;
        let isSharedProposal = stageName.indexOf('shared proposal') !== -1;

        // Lost reason dropdown hide if Sale closed or Shared Proposal
        if (isSaleClosed || isSharedProposal) {
            lostReasonGroup.addClass('d-none');
            lostReasonSelect.val('');
        } else {
            lostReasonGroup.removeClass('d-none');
        }

        // Next followup date hide if Sale closed
        if (isSaleClosed) {
            followupGroup.addClass('d-none');
            followupInput.val('');
        } else {
            followupGroup.removeClass('d-none');
        }
    }

    // Attach stage change handlers
    $('#add_lead_stage_id').on('change', function () {
        handleLeadStageChanges(
            $(this),
            $('#add_lost_reason_group'),
            $('#add_lost_reason_id'),
            $('#add_next_followup_group'),
            $('#add_next_followup_date')
        );
    });

    $('#edit_lead_stage_id').on('change', function () {
        handleLeadStageChanges(
            $(this),
            $('#edit_lost_reason_group'),
            $('#edit_lost_reason_id'),
            $('#edit_next_followup_group'),
            $('#edit_lead_next_followup_date')
        );
    });

    // Reset validation errors on modal hide
    $('.modal').on('hidden.bs.modal', function () {
        let form = $(this).find('form');
        if (form.length) {
            clearValidationErrors(form);
        }
    });

    $('#addLeadModal').on('show.bs.modal', function () {
        handleLeadStageChanges(
            $('#add_lead_stage_id'),
            $('#add_lost_reason_group'),
            $('#add_lost_reason_id'),
            $('#add_next_followup_group'),
            $('#add_next_followup_date')
        );
    });

    // Add Lead Form Submit
    $('#addLeadForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let submitBtn = $('#addLeadSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/leads/store',
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#addLeadModal').modal('hide');
                    form[0].reset();
                    handleLeadStageChanges(
                        $('#add_lead_stage_id'),
                        $('#add_lost_reason_group'),
                        $('#add_lost_reason_id'),
                        $('#add_next_followup_group'),
                        $('#add_next_followup_date')
                    );
                    leadTable.ajax.reload(null, false);
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

    // Open Edit Lead Modal
    $(document).on('click', '.btn-edit-lead', function () {
        let id = $(this).data('id');
        let form = $('#editLeadForm');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/leads/edit/' + id,
            type: 'GET',
            success: function (response) {
                if (response.status) {
                    let lead = response.data;
                    let cust = lead.customer || {};
                    let custName = lead.customer_name || cust.name || '';
                    let custType = lead.customer_type || cust.customer_type || 'user';
                    let custMobile = lead.mobile || cust.mobile || '';
                    let custEmail = lead.email || cust.email || '';

                    $('#edit_lead_id').val(lead.lead_id);
                    $('#edit_lead_customer_id').val(lead.customer_id || '');
                    $('#edit_lead_customer_name').val(custName);
                    $('#edit_lead_customer_type').val(custType);
                    $('#edit_lead_mobile').val(custMobile);
                    $('#edit_lead_email').val(custEmail);

                    $('#edit_lead_title').val(lead.lead_title);
                    $('#edit_lead_source_id').val(lead.lead_source_id || '');
                    $('#edit_lead_stage_id').val(lead.lead_stage_id || '');
                    $('#edit_lead_requirement_id').val(lead.lead_requirement_id || '');
                    $('#edit_lost_reason_id').val(lead.lost_reason_id || '');
                    $('#edit_lead_assigned_to').val(lead.assigned_to || '');
                    $('#edit_lead_priority').val(lead.priority);
                    $('#edit_lead_expected_amount').val(lead.expected_amount || '');
                    $('#edit_lead_next_followup_date').val(lead.next_followup_date || '');
                    $('#edit_lead_description').val(lead.description || '');
                    $('#edit_lead_status').val(lead.status);

                    let bIds = lead.branch_ids || (lead.branch_id ? [lead.branch_id] : []);
                    let tIds = lead.team_ids || (lead.team_id ? [lead.team_id] : []);
                    $('#edit_lead_branch_id').val(bIds).trigger('change.select2');
                    loadTeamsForLeadSelect(bIds, $('#edit_lead_team_id'), tIds);

                    // Trigger stage dynamic visibility in Edit modal
                    handleLeadStageChanges(
                        $('#edit_lead_stage_id'),
                        $('#edit_lost_reason_group'),
                        $('#edit_lost_reason_id'),
                        $('#edit_next_followup_group'),
                        $('#edit_lead_next_followup_date')
                    );

                    // Reset and populate custom fields
                    form.find('[name^="custom_fields["]').val('');
                    form.find('input[type="checkbox"][name^="custom_fields["]').prop('checked', false);

                    if (lead.custom_fields && typeof lead.custom_fields === 'object') {
                        $.each(lead.custom_fields, function (cfKey, cfVal) {
                            let cfInput = form.find(`[name="custom_fields[${cfKey}]"]`);
                            if (cfInput.length) {
                                if (cfInput.is(':checkbox')) {
                                    cfInput.prop('checked', cfVal == 1 || cfVal === true || cfVal === '1');
                                } else {
                                    cfInput.val(cfVal);
                                }
                            }
                        });
                    }

                    $('#editLeadModal').modal('show');
                }
            },
            error: function () {
                showAlert('danger', 'Failed to fetch lead details.');
            }
        });
    });


    // Update Lead Form Submit
    $('#editLeadForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let id = $('#edit_lead_id').val();
        let submitBtn = $('#editLeadSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/leads/update/' + id,
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#editLeadModal').modal('hide');
                    leadTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', 'An error occurred while updating lead.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Open Delete Confirmation Modal
    let deleteLeadId = null;
    $(document).on('click', '.btn-delete-lead', function () {
        deleteLeadId = $(this).data('id');
        let title = $(this).data('title');
        $('#delete_lead_title').text(title);
        $('#deleteLeadModal').modal('show');
    });

    // Confirm Delete Lead
    $('#confirmDeleteLeadBtn').on('click', function () {
        if (!deleteLeadId) return;

        let btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: APP_URL + '/admin/leads/delete/' + deleteLeadId,
            type: 'DELETE',
            success: function (response) {
                if (response.status) {
                    $('#deleteLeadModal').modal('hide');
                    leadTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Failed to delete lead.');
            },
            complete: function () {
                btn.prop('disabled', false);
                deleteLeadId = null;
            }
        });
    });

    // Toggle Status
    $(document).on('change', '.btn-toggle-lead-status', function () {
        let id = $(this).data('id');
        let checkbox = $(this);

        $.ajax({
            url: APP_URL + '/admin/leads/change-status/' + id,
            type: 'POST',
            success: function (response) {
                if (response.status) {
                    leadTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function () {
                checkbox.prop('checked', !checkbox.is(':checked'));
                showAlert('danger', 'Failed to update status.');
            }
        });
    });

    // Period Toggle Buttons
    $('.btn-lead-period').on('click', function () {
        $('.btn-lead-period').removeClass('active');
        $(this).addClass('active');

        let period = $(this).data('period');
        $('#lead_filter_period').val(period);
        $('.lead-filter-date-group').addClass('d-none');

        if (period === 'daily') {
            $('#lead_group_daily').removeClass('d-none');
        } else if (period === 'weekly') {
            $('#lead_group_custom_start').removeClass('d-none');
        } else if (period === 'monthly') {
            $('#lead_group_monthly').removeClass('d-none');
        } else if (period === 'custom') {
            $('#lead_group_custom_start').removeClass('d-none');
            $('#lead_group_custom_end').removeClass('d-none');
        }

        leadTable.ajax.reload();
    });

    // Lead Filter Form Submit
    $('#leadFilterForm').on('submit', function (e) {
        e.preventDefault();
        leadTable.ajax.reload();
    });

    // Reset Lead Filters
    $('#resetLeadFilterBtn').on('click', function () {
        $('#leadFilterForm')[0].reset();
        $('.btn-lead-period').removeClass('active');
        $('.btn-lead-period[data-period="all"]').addClass('active');
        $('#lead_filter_period').val('all');
        $('#lead_filter_requirement_id').val('');
        $('#lead_filter_source_id').val('');
        $('#lead_filter_stage_id').val('');
        $('#lead_filter_lost_reason_id').val('');
        $('#lead_filter_customer_id').val('').trigger('change');
        $('#lead_filter_created_by').val('');
        $('#lead_filter_status').val('');
        $('.lead-filter-date-group').addClass('d-none');
        leadTable.ajax.reload();
    });

    // KPI Card Click Handlers
    $('#kpi_card_total_leads').on('click', function () {
        $('#resetLeadFilterBtn').click();
    });

    // Helper to dynamically load teams for lead branch select element (supports single ID or array of IDs)
    function loadTeamsForLeadSelect(branchId, targetSelect, selectedTeamIds = []) {
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
        });
    }

    // Initialize Select2 on modal open for add/edit lead multiple dropdowns
    $('#addLeadModal').on('shown.bs.modal', function () {
        $('#add_lead_branch_id').select2({
            dropdownParent: $('#addLeadModal'),
            placeholder: 'Select Branch(es)',
            allowClear: true,
            width: '100%'
        });
        $('#add_lead_team_id').select2({
            dropdownParent: $('#addLeadModal'),
            placeholder: 'Select Team(s)',
            allowClear: true,
            width: '100%'
        });
    });

    $('#editLeadModal').on('shown.bs.modal', function () {
        $('#edit_lead_branch_id').select2({
            dropdownParent: $('#editLeadModal'),
            placeholder: 'Select Branch(es)',
            allowClear: true,
            width: '100%'
        });
        $('#edit_lead_team_id').select2({
            dropdownParent: $('#editLeadModal'),
            placeholder: 'Select Team(s)',
            allowClear: true,
            width: '100%'
        });
    });

    $('#add_lead_branch_id').on('change', function() {
        let val = $(this).val();
        loadTeamsForLeadSelect(val, $('#add_lead_team_id'), $('#add_lead_team_id').val() || []);
    });

    $('#edit_lead_branch_id').on('change', function() {
        let val = $(this).val();
        loadTeamsForLeadSelect(val, $('#edit_lead_team_id'), $('#edit_lead_team_id').val() || []);
    });

    // DataTable initialization helper for Customer Details Modal
    function initCdmDataTable(selector) {
        if ($.fn.DataTable.isDataTable(selector)) {
            $(selector).DataTable().clear().destroy();
        }
        return $(selector).DataTable({
            pageLength: 5,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            order: [],
            responsive: true,
            autoWidth: false,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
                lengthMenu: "_MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                emptyTable: "No records found"
            }
        });
    }

    // Auto-adjust DataTable columns on modal tab switch & modal show
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
        if ($.fn.dataTable) {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        }
    });

    $('#customerDetailsModal').on('shown.bs.modal', function () {
        if ($.fn.dataTable) {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        }
    });

    // Customer Full Details Modal (Calls, Sale Closed, Demos, Proposals & Profile)
    $(document).on('click', '.view-customer-details-btn', function (e) {
        e.preventDefault();
        let customerId = $(this).data('customer-id');
        if (!customerId) return;

        let $modal = $('#customerDetailsModal');
        $modal.modal('show');
        $('#cdm_loading').removeClass('d-none');
        $('#cdm_content').addClass('d-none');
        $('#cdm_sale_closed_banner').addClass('d-none');

        // Reset tables and destroy previous DataTables
        ['#cdm_calls_table', '#cdm_leads_table', '#cdm_demos_table', '#cdm_proposals_table', '#cdm_followups_table'].forEach(function(sel) {
            if ($.fn.DataTable.isDataTable(sel)) {
                $(sel).DataTable().clear().destroy();
            }
        });

        // Reset fields
        $('#cdm_name').text('Customer Details');
        $('#cdm_type').text('User');
        $('#cdm_sale_closed_badge').empty();
        $('#cdm_calls_tbody').empty();
        $('#cdm_leads_tbody').empty();
        $('#cdm_demos_tbody').empty();
        $('#cdm_proposals_tbody').empty();
        $('#cdm_followups_tbody').empty();

        $.ajax({
            url: APP_URL + '/admin/customers/details/' + customerId,
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (!res.status || !res.data) {
                    if (typeof showToast === 'function') {
                        showToast('error', res.message || 'Unable to load customer details.');
                    }
                    $modal.modal('hide');
                    return;
                }

                let d = res.data;
                let c = d.customer;
                let sc = d.sale_closed;
                let stats = d.stats || {};

                // Header
                $('#cdm_name').text(c.name || 'Customer');
                $('#cdm_type').text(c.customer_type || 'User');
                if (stats.is_sale_closed) {
                    $('#cdm_sale_closed_badge').html('<span class="badge bg-success"><i class="bx bx-check me-1"></i>Sale Closed</span>');
                } else {
                    $('#cdm_sale_closed_badge').html('<span class="badge bg-label-secondary">Open Lead</span>');
                }

                // KPI Stats Top Row
                $('#cdm_stat_calls').text(stats.total_calls || 0);
                $('#cdm_stat_leads').text(stats.total_leads || 0);
                $('#cdm_stat_closed').html(stats.is_sale_closed ? '<span class="text-success"><i class="bx bx-check me-1"></i>Yes</span>' : '<span class="text-muted">No</span>');
                $('#cdm_stat_demos').text(stats.total_demo_processes || 0);
                $('#cdm_stat_proposals').text(stats.total_proposals || 0);
                $('#cdm_stat_followups').text(stats.total_followups || 0);

                // Tab Badge Counts
                $('#cdm_tab_count_calls').text(stats.total_calls || 0);
                $('#cdm_tab_count_leads').text(stats.total_leads || 0);
                $('#cdm_tab_count_demos').text(stats.total_demo_processes || 0);
                $('#cdm_tab_count_proposals').text(stats.total_proposals || 0);
                $('#cdm_tab_count_followups').text(stats.total_followups || 0);

                // Sale Closed Banner
                if (sc && sc.is_closed) {
                    $('#cdm_sale_closed_banner').removeClass('d-none');
                    $('#cdm_closed_date_badge').text(sc.closed_date);
                    $('#cdm_sale_closed_text').html(`Closed by <strong>${sc.closed_by || 'Staff'}</strong> on <strong>${sc.closed_date}</strong> (${sc.closed_ago}).`);
                    $('#cdm_sale_closed_subtext').html(`Lead: <strong>${sc.lead_title}</strong> | Requirement: <strong>${sc.requirement}</strong> ${sc.expected_amount ? '| Expected Amount: <strong class="text-success">' + sc.expected_amount + '</strong>' : ''}`);
                } else {
                    $('#cdm_sale_closed_banner').addClass('d-none');
                }

                // Customer Info (Profile Tab)
                $('#cdm_p_name').text(c.name || '-');
                $('#cdm_p_type').text(c.customer_type || '-');
                $('#cdm_p_mobile').text(c.mobile || '-');
                if (c.mobile) {
                    $('#cdm_p_mobile_link').attr('href', 'tel:' + c.mobile);
                } else {
                    $('#cdm_p_mobile_link').removeAttr('href');
                }
                $('#cdm_p_email').text(c.email || '-');
                if (c.email && c.email !== '-') {
                    $('#cdm_p_email_link').attr('href', 'mailto:' + c.email);
                } else {
                    $('#cdm_p_email_link').removeAttr('href');
                }
                $('#cdm_p_company').text(c.company_name || '-');
                $('#cdm_p_city').text(c.city || '-');
                $('#cdm_p_address').text(c.address || '-');
                $('#cdm_p_branch').text(c.branch_name || '-');
                $('#cdm_p_team').text(c.team_name || '-');
                $('#cdm_p_owner').text(c.owner_name || c.assigned_by_name || '-');
                $('#cdm_p_creator').text(c.creator_name || '-');
                $('#cdm_p_created_at').text(c.created_at || '-');

                // 1. Call History Tab ("yatha staff yepo call paniruka")
                let calls = d.call_logs || [];
                let callsHtml = '';
                calls.forEach(function (call, idx) {
                    let statusBadge = '<span class="badge bg-label-secondary">' + (call.call_status || '-') + '</span>';
                    let stLower = (call.call_status || '').toLowerCase();
                    if (stLower.includes('answer')) {
                        statusBadge = '<span class="badge bg-label-success">' + call.call_status + '</span>';
                    } else if (stLower.includes('miss') || stLower.includes('no answer')) {
                        statusBadge = '<span class="badge bg-label-danger">' + call.call_status + '</span>';
                    } else if (stLower.includes('busy')) {
                        statusBadge = '<span class="badge bg-label-warning">' + call.call_status + '</span>';
                    }

                    let typeBadge = '<span class="badge bg-label-primary"><i class="bx bx-phone-outgoing me-1"></i>' + (call.call_type || 'Out') + '</span>';
                    if ((call.call_type || '').toLowerCase().includes('in')) {
                        typeBadge = '<span class="badge bg-label-info"><i class="bx bx-phone-incoming me-1"></i>' + call.call_type + '</span>';
                    }

                    let recPlayer = '<span class="text-muted">-</span>';
                    if (call.recording_url) {
                        recPlayer = `
                            <audio controls style="height: 30px; width: 170px;">
                                <source src="${call.recording_url}" type="audio/mpeg">
                                <source src="${call.recording_url}" type="audio/wav">
                                <source src="${call.recording_url}" type="audio/mp3">
                            </audio>
                        `;
                    }

                    callsHtml += `
                        <tr>
                            <td class="text-center">${idx + 1}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs bg-label-primary rounded-circle me-2 d-flex align-items-center justify-content-center">
                                        <i class="bx bx-user" style="font-size: 11px;"></i>
                                    </div>
                                    <div>
                                        <strong class="text-dark">${call.staff_name || 'Staff'}</strong>
                                        ${call.staff_email ? `<br><small class="text-muted">${call.staff_email}</small>` : ''}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <strong>${call.call_date}</strong>
                                <br><small class="text-muted">${call.time_ago}</small>
                            </td>
                            <td>${typeBadge}</td>
                            <td>${statusBadge}</td>
                            <td><span class="badge bg-light text-dark border"><i class="bx bx-time-five me-1"></i>${call.duration}</span></td>
                            <td><small class="text-muted">${call.lead_title || '-'}</small></td>
                            <td>${recPlayer}</td>
                        </tr>
                    `;
                });
                $('#cdm_calls_tbody').html(callsHtml);
                initCdmDataTable('#cdm_calls_table');

                // 2. Leads Tab ("yepo sale close aagiruch")
                let leads = d.leads || [];
                let leadsHtml = '';
                leads.forEach(function (l, idx) {
                    let stageBadge = `<span class="badge bg-label-warning">${l.stage_name}</span>`;
                    let trClass = '';
                    if (l.is_sale_closed) {
                        stageBadge = `<span class="badge bg-success text-white"><i class="bx bx-check-circle me-1"></i>${l.stage_name}</span>`;
                        trClass = 'table-success';
                    }

                    leadsHtml += `
                        <tr class="${trClass}">
                            <td class="text-center">${idx + 1}</td>
                            <td>
                                <strong class="text-dark">${l.lead_title || 'Lead #' + l.lead_id}</strong>
                                ${l.is_sale_closed ? '<br><span class="badge bg-label-success" style="font-size: 10px;">Sale Closed</span>' : ''}
                            </td>
                            <td>${stageBadge}</td>
                            <td><span class="badge bg-label-info">${l.requirement_name}</span></td>
                            <td><small class="text-muted">${l.source_name}</small></td>
                            <td><i class="bx bx-user me-1 text-muted"></i>${l.assigned_to}</td>
                            <td><small class="text-muted">${l.created_at}</small></td>
                            <td>
                                <strong>${l.updated_at}</strong>
                                ${l.is_sale_closed ? '<br><small class="text-success fw-bold">Closed</small>' : ''}
                            </td>
                        </tr>
                    `;
                });
                $('#cdm_leads_tbody').html(leadsHtml);
                initCdmDataTable('#cdm_leads_table');

                // 3. Demo Process Tab
                let demos = d.demo_processes || [];
                let demosHtml = '';
                demos.forEach(function (dp, idx) {
                    let stBadge = '<span class="badge bg-label-secondary">' + dp.status + '</span>';
                    let stLower = (dp.status || '').toLowerCase();
                    if (stLower.includes('finish') || stLower.includes('complete')) {
                        stBadge = '<span class="badge bg-label-success">' + dp.status + '</span>';
                    } else if (stLower.includes('pend') || stLower.includes('sched')) {
                        stBadge = '<span class="badge bg-label-warning">' + dp.status + '</span>';
                    } else if (stLower.includes('cancel')) {
                        stBadge = '<span class="badge bg-label-danger">' + dp.status + '</span>';
                    }

                    demosHtml += `
                        <tr>
                            <td class="text-center">${idx + 1}</td>
                            <td>
                                <strong class="text-dark"><i class="bx bx-calendar me-1 text-primary"></i>${dp.demo_date}</strong>
                                ${dp.demo_time && dp.demo_time !== '-' ? `<br><small class="text-muted"><i class="bx bx-time-five me-1"></i>${dp.demo_time}</small>` : ''}
                            </td>
                            <td><span class="badge bg-label-info">${dp.requirement_name}</span></td>
                            <td><small class="text-muted">${dp.source_name}</small></td>
                            <td><i class="bx bx-user me-1 text-muted"></i>${dp.assigned_staff}</td>
                            <td>${stBadge}</td>
                            <td><small class="text-wrap">${dp.remarks}</small></td>
                        </tr>
                    `;
                });
                $('#cdm_demos_tbody').html(demosHtml);
                initCdmDataTable('#cdm_demos_table');

                // 4. Proposals Tab
                let proposals = d.proposals || [];
                let proposalsHtml = '';
                proposals.forEach(function (pr, idx) {
                    let stBadge = '<span class="badge bg-label-secondary">' + pr.status + '</span>';
                    let stLower = (pr.status || '').toLowerCase();
                    if (stLower.includes('accept') || stLower.includes('active')) {
                        stBadge = '<span class="badge bg-label-success">' + pr.status + '</span>';
                    } else if (stLower.includes('send') || stLower.includes('sent')) {
                        stBadge = '<span class="badge bg-label-primary">' + pr.status + '</span>';
                    } else if (stLower.includes('decline') || stLower.includes('reject')) {
                        stBadge = '<span class="badge bg-label-danger">' + pr.status + '</span>';
                    }

                    proposalsHtml += `
                        <tr>
                            <td class="text-center">${idx + 1}</td>
                            <td><strong class="text-primary">${pr.proposal_number}</strong></td>
                            <td><small class="text-muted">${pr.proposal_date}</small></td>
                            <td><span class="badge bg-label-info">${pr.requirement_name}</span></td>
                            <td><i class="bx bx-user me-1 text-muted"></i>${pr.sales_manager}</td>
                            <td><span class="badge bg-label-dark">${pr.items_count} item(s)</span></td>
                            <td><strong class="text-success">${pr.total_amount}</strong></td>
                            <td>${stBadge}</td>
                        </tr>
                    `;
                });
                $('#cdm_proposals_tbody').html(proposalsHtml);
                initCdmDataTable('#cdm_proposals_table');

                // 5. Followups Tab
                let followups = d.followups || [];
                let fuHtml = '';
                followups.forEach(function (f, idx) {
                    let stBadge = f.status === 'Completed' ? '<span class="badge bg-label-success">Completed</span>' : '<span class="badge bg-label-warning">' + f.status + '</span>';
                    fuHtml += `
                        <tr>
                            <td class="text-center">${idx + 1}</td>
                            <td><small class="fw-semibold">${f.lead_title}</small></td>
                            <td><i class="bx bx-user me-1 text-muted"></i>${f.staff_name}</td>
                            <td><strong class="text-primary">${f.next_followup_date}</strong></td>
                            <td>${stBadge}</td>
                            <td><small>${f.remarks}</small></td>
                        </tr>
                    `;
                });
                $('#cdm_followups_tbody').html(fuHtml);
                initCdmDataTable('#cdm_followups_table');

                $('#cdm_loading').addClass('d-none');
                $('#cdm_content').removeClass('d-none');

                // Adjust DataTable column widths now that content is visible
                setTimeout(function () {
                    if ($.fn.dataTable) {
                        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
                    }
                }, 100);
            },
            error: function () {
                $('#cdm_loading').html('<div class="alert alert-danger m-3 py-2"><i class="bx bx-error-circle me-1"></i>Failed to fetch customer details. Please try again.</div>');
            }
        });
    });
});
