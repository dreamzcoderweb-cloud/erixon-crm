$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Helper functions
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

    // Dynamic Duration field toggler (Requirement 3)
    function handleDurationToggle(typeSelectId, containerId) {
        let typeVal = $(typeSelectId).val();
        let container = $(containerId);
        if (typeVal === 'Call') {
            container.removeClass('d-none').show();
            container.find('select, input').prop('required', true);
        } else {
            container.addClass('d-none').hide();
            container.find('select, input').prop('required', false).val('');
        }
    }

    // Add modal type change listener
    $('#add_followup_type').on('change', function () {
        handleDurationToggle('#add_followup_type', '#add_duration_container');
    });

    // Edit modal type change listener
    $('#edit_followup_type').on('change', function () {
        handleDurationToggle('#edit_followup_type', '#edit_duration_container');
    });

    // Initial state check when modals open
    $('#addFollowupModal').on('show.bs.modal', function () {
        handleDurationToggle('#add_followup_type', '#add_duration_container');
    });

    // Reset validation errors on modal hide
    $('.modal').on('hidden.bs.modal', function () {
        let form = $(this).find('form');
        if (form.length) {
            clearValidationErrors(form);
        }
    });

    // Initialize Follow-ups DataTable if present on page
    let followupTable = null;
    if ($('#followups-table').length) {
        followupTable = $('#followups-table').DataTable({
            ajax: {
                url: APP_URL + '/admin/followups/data',
                type: 'GET',
                data: function (d) {
                    d.filter_type = $('#filter_type_input').val();
                    d.staff_id    = $('#filter_staff_id').val();
                    d.date        = $('#filter_custom_date').val();
                    d.month       = $('#followup_filter_month').val();
                    d.start_date  = $('#followup_filter_start_date').val();
                    d.end_date    = $('#followup_filter_end_date').val();
                    d.lead_id     = $('#followup_filter_lead_id').val();
                    d.customer_id = $('#followup_filter_customer_id').val();
                    d.lead_source_id = $('#followup_filter_source_id').val();
                    d.created_by  = $('#filter_staff_id').val();
                    d.status      = $('#followup_filter_status').val();
                },
                dataSrc: function (json) {
                    if (json.counts) {
                        $('#badge_count_all').text(json.counts.all || 0);
                        $('#badge_count_today').text(json.counts.today || 0);
                        $('#badge_count_upcoming').text(json.counts.upcoming || 0);
                        $('#badge_count_overdue').text(json.counts.overdue || 0);
                    }
                    if (json.total_followups !== undefined) {
                        $('#kpi_total_followups').text(json.total_followups);
                    }
                    if (json.staff_created_count !== undefined) {
                        $('#kpi_staff_created_followups').text(json.staff_created_count);
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
                    data: null,
                    render: function (data, type, row) {
                        let title = row.lead ? row.lead.lead_title : 'N/A';
                        let customerName = (row.lead && row.lead.customer) ? row.lead.customer.name : '';
                        let mobile = (row.lead && row.lead.customer && row.lead.customer.mobile) ? row.lead.customer.mobile : '';
                        if (type !== 'display') return title + ' (' + customerName + ')';
                        return `<div><strong>${title}</strong><br><small class="text-muted">${customerName ? '<i class="bx bx-user me-1"></i>' + customerName : ''} ${mobile ? ' (' + mobile + ')' : ''}</small></div>`;
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
                    data: 'duration',
                    className: 'text-center',
                    render: function (data, type, row) {
                        if (type !== 'display') return data || '-';
                        if (row.followup_type === 'Call' && data) {
                            return `<span class="badge bg-label-info"><i class="bx bx-time-five me-1"></i>${data}</span>`;
                        }
                        return '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'next_followup_date',
                    render: function (data, type) {
                        if (!data) return type === 'display' ? '<span class="text-muted">N/A</span>' : '';
                        if (type !== 'display') return data;
                        let formatted = formatDateTime(data);
                        return `<span class="badge bg-label-dark"><i class="bx bx-calendar me-1"></i>${formatted}</span>`;
                    }
                },
                {
                    data: 'followup_status',
                    className: 'text-center',
                    render: function (data, type) {
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
                        let isOnLeave = row.forward_to_user ? row.forward_to_user.is_on_leave : false;
                        if (type !== 'display') return name || 'N/A';
                        if (!name) return '<span class="text-muted">N/A</span>';
                        if (isOnLeave) {
                            return `<span class="badge bg-label-danger"><i class="bx bx-user-x me-1"></i>${name} (On Leave)</span>`;
                        }
                        return `<span class="badge bg-label-info">${name}</span>`;
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
                    className: 'text-center',
                    render: function (data, type, row) {
                        let reassignBtn = '';
                        if (row.followup_status === 'Pending') {
                            reassignBtn = `
                                <a class="dropdown-item btn-reassign-followup" href="javascript:void(0);" data-id="${row.followups_id}" data-staff="${row.forward_to_user ? row.forward_to_user.name : 'Unassigned'}">
                                    <i class="bx bx-user-voice me-1"></i> Reassign Staff
                                </a>
                            `;
                        }
                        return `
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    ${reassignBtn}
                                    <a class="dropdown-item btn-edit-followup" href="javascript:void(0);" data-id="${row.followups_id}">
                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                    </a>
                                    <a class="dropdown-item text-danger btn-delete-followup" href="javascript:void(0);" data-id="${row.followups_id}">
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
    }

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
                    if (followupTable) followupTable.ajax.reload(null, false);
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

                    // Handle Duration field
                    handleDurationToggle('#edit_followup_type', '#edit_duration_container');
                    $('#edit_duration').val(followup.duration || '');

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
                    if (followupTable) followupTable.ajax.reload(null, false);
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

    // Reassign Staff Modal handler (Requirement 2)
    $(document).on('click', '.btn-reassign-followup', function () {
        let id = $(this).data('id');
        let staffName = $(this).data('staff') || 'Unassigned';

        $('#reassign_followup_id').val(id);
        $('#reassign_current_staff').text(staffName);
        $('#reassign_new_staff_id').val('');
        $('#reassign_notes').val('');
        clearValidationErrors($('#reassignFollowupForm'));

        $('#reassignFollowupModal').modal('show');
    });

    // Submit Reassign Staff Form
    $('#reassignFollowupForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let id = $('#reassign_followup_id').val();
        let submitBtn = $('#confirmReassignBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/followups/reassign/' + id,
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#reassignFollowupModal').modal('hide');
                    if (followupTable) followupTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        showValidationErrors(form, xhr.responseJSON.errors);
                    } else if (xhr.responseJSON.message) {
                        showAlert('danger', xhr.responseJSON.message);
                    }
                } else {
                    showAlert('danger', 'Failed to reassign follow-up.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Reassignment Audit Trail Modal View
    $(document).on('click', '.btn-view-reassignment-history', function () {
        let tbody = $('#reassignmentHistoryTbody');
        tbody.html('<tr><td colspan="6" class="text-center"><span class="spinner-border spinner-border-sm me-1"></span> Loading audit trail...</td></tr>');
        $('#reassignmentHistoryModal').modal('show');

        $.ajax({
            url: APP_URL + '/admin/followups/reassignment-history',
            type: 'GET',
            success: function (response) {
                if (response.status && response.data) {
                    if (response.data.length === 0) {
                        tbody.html('<tr><td colspan="6" class="text-center text-muted">No reassignment history records found.</td></tr>');
                        return;
                    }
                    let rowsHtml = '';
                    $.each(response.data, function (index, item) {
                        let clientName = (item.followup && item.followup.lead && item.followup.lead.customer) ? item.followup.lead.customer.name : 'N/A';
                        let leadTitle = (item.followup && item.followup.lead) ? item.followup.lead.lead_title : 'N/A';
                        let prevStaff = item.previous_staff ? item.previous_staff.name : 'Unassigned';
                        let newStaff = item.new_staff ? item.new_staff.name : 'N/A';
                        let reassignedBy = item.reassigned_by ? item.reassigned_by.name : 'System';
                        let dt = formatDateTime(item.created_at);

                        rowsHtml += `
                            <tr>
                                <td>${index + 1}</td>
                                <td><strong>${leadTitle}</strong><br><small class="text-muted"><i class="bx bx-user me-1"></i>${clientName}</small></td>
                                <td><span class="badge bg-label-secondary">${prevStaff}</span></td>
                                <td><span class="badge bg-label-success">${newStaff}</span></td>
                                <td><span class="badge bg-label-info">${reassignedBy}</span></td>
                                <td><small class="text-muted"><i class="bx bx-time me-1"></i>${dt}</small></td>
                            </tr>
                        `;
                    });
                    tbody.html(rowsHtml);
                }
            },
            error: function () {
                tbody.html('<tr><td colspan="6" class="text-center text-danger">Failed to load reassignment history.</td></tr>');
            }
        });
    });

    // Delete Follow-up Confirm
    let deleteFollowupId = null;
    $(document).on('click', '.btn-delete-followup', function () {
        deleteFollowupId = $(this).data('id');
        $('#deleteFollowupModal').modal('show');
    });

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
                    if (followupTable) followupTable.ajax.reload(null, false);
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

    // Requirement 1: Check and show Today's Follow-up Reminder Modal immediately after login
    if ($('#todayFollowupReminderModal').length && !sessionStorage.getItem('today_reminder_dismissed')) {
        $.ajax({
            url: APP_URL + '/admin/followups/today-reminders',
            type: 'GET',
            success: function (response) {
                if (response.status && response.count > 0) {
                    let tbody = $('#todayFollowupReminderTbody');
                    let rowsHtml = '';
                    $.each(response.data, function (index, row) {
                        let clientName = (row.lead && row.lead.customer) ? row.lead.customer.name : (row.lead ? row.lead.lead_title : 'N/A');
                        let contactMobile = (row.lead && row.lead.customer && row.lead.customer.mobile) ? row.lead.customer.mobile : 'N/A';
                        let dateStr = row.next_followup_date ? formatDate(row.next_followup_date) : 'Today';
                        let formattedDt = row.next_followup_date ? formatDateTime(row.next_followup_date) : '';
                        let timeStr = formattedDt.includes(', ') ? formattedDt.split(', ')[1] : 'N/A';
                        let typeText = row.followup_type;
                        if (row.followup_type === 'Call' && row.duration) {
                            typeText += ` (${row.duration})`;
                        }
                        let remarks = row.remarks ? row.remarks : '-';

                        rowsHtml += `
                            <tr>
                                <td><strong>${clientName}</strong></td>
                                <td><a href="tel:${contactMobile}" class="text-primary"><i class="bx bx-phone me-1"></i>${contactMobile}</a></td>
                                <td>
                                    <div><span class="badge bg-label-dark"><i class="bx bx-calendar me-1"></i>${dateStr}</span></div>
                                    <div class="mt-1"><small class="text-muted"><i class="bx bx-time me-1"></i>${timeStr}</small></div>
                                </td>
                                <td><span class="badge bg-label-info">${typeText}</span></td>
                                <td><span class="badge bg-label-warning">${row.followup_status}</span></td>
                                <td><small class="text-muted">${remarks}</small></td>
                                <td>
                                    <a href="${APP_URL}/admin/followups" class="btn btn-sm btn-primary btn-dismiss-reminder-goto">
                                        <i class="bx bx-show me-1"></i> Open Details
                                    </a>
                                </td>
                            </tr>
                        `;
                    });
                    tbody.html(rowsHtml);
                    $('#todayFollowupReminderModal').modal('show');
                }
            }
        });
    }

    // Set sessionStorage when reminder modal is closed so it doesn't pop up on every menu click during active session
    $('#todayFollowupReminderModal').on('hidden.bs.modal', function () {
        sessionStorage.setItem('today_reminder_dismissed', 'true');
    });

    $(document).on('click', '.btn-dismiss-reminder-goto', function () {
        sessionStorage.setItem('today_reminder_dismissed', 'true');
    });

    // Clear reminder dismissal on logout link click
    $(document).on('click', 'a[href*="logout"]', function () {
        sessionStorage.removeItem('today_reminder_dismissed');
    });

    // Follow-up Status Nav Tab Click Handler
    $(document).on('click', '.btn-filter-tab', function () {
        $('.btn-filter-tab').removeClass('active');
        $(this).addClass('active');

        let filterType = $(this).data('filter');
        $('#filter_type_input').val(filterType);
        $('#filter_custom_date').val(''); // Reset custom date when tab clicked

        if (followupTable) followupTable.ajax.reload();
    });

    // Date Period Buttons Toggle
    $(document).on('click', '.btn-followup-period', function () {
        $('.btn-followup-period').removeClass('active');
        $(this).addClass('active');

        let period = $(this).data('period');
        $('#filter_type_input').val(period);
        $('.followup-filter-date-group').addClass('d-none');

        if (period === 'daily') {
            $('#followup_group_daily').removeClass('d-none');
        } else if (period === 'weekly') {
            $('#followup_group_custom_start').removeClass('d-none');
        } else if (period === 'monthly') {
            $('#followup_group_monthly').removeClass('d-none');
        } else if (period === 'custom') {
            $('#followup_group_custom_start').removeClass('d-none');
            $('#followup_group_custom_end').removeClass('d-none');
        }

        if (followupTable) followupTable.ajax.reload();
    });

    // Filter Form Submit
    $(document).on('submit', '#followupFilterForm', function (e) {
        e.preventDefault();
        if (followupTable) followupTable.ajax.reload();
    });

    // Reset Filters Listener
    $(document).on('click', '#resetFollowupFiltersBtn', function () {
        $('#followupFilterForm')[0].reset();
        $('.btn-filter-tab').removeClass('active');
        $('.btn-filter-tab[data-filter="all"]').addClass('active');
        $('.btn-followup-period').removeClass('active');
        $('.btn-followup-period[data-period="all"]').addClass('active');
        $('#filter_type_input').val('all');
        $('#filter_staff_id').val('');
        $('#filter_custom_date').val('');
        $('#followup_filter_lead_id').val('');
        $('#followup_filter_customer_id').val('');
        $('#followup_filter_source_id').val('');
        $('#followup_filter_status').val('');
        $('.followup-filter-date-group').addClass('d-none');

        if (followupTable) followupTable.ajax.reload();
    });

    // KPI Card Click Handlers
    $('#kpi_card_total_followups').on('click', function () {
        $('#resetFollowupFiltersBtn').click();
    });
});
