$(document).ready(function () {
    if (!$('#call-logs-table').length && !$('#call-log-report-table').length) return;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function leadMarkup(row, type) {
        let title = row.lead ? row.lead.lead_title : 'N/A';
        let customerName = row.lead && row.lead.customer ? row.lead.customer.name : '';
        if (type !== 'display') return title + ' ' + customerName;
        return `<div><strong>${title}</strong><br><small class="text-muted">${customerName || ''}</small></div>`;
    }

    function userMarkup(row, type) {
        let name = row.user ? row.user.name : 'N/A';
        let email = row.user ? row.user.email : '';
        if (type !== 'display') return name;
        return `<div><strong>${name}</strong><br><small class="text-muted">${email}</small></div>`;
    }

    function callTypeBadge(data, type) {
        if (type !== 'display') return data || '';
        let badgeClass = 'bg-label-primary';
        if (data === 'Inbound') badgeClass = 'bg-label-success';
        if (data === 'Outbound') badgeClass = 'bg-label-info';
        if (data === 'Missed') badgeClass = 'bg-label-danger';
        return `<span class="badge ${badgeClass}">${data || '-'}</span>`;
    }

    function statusBadge(data, type) {
        if (type !== 'display') return data || '';
        let badgeClass = 'bg-label-secondary';
        if (data === 'Completed') badgeClass = 'bg-label-success';
        if (data === 'Missed' || data === 'No Answer' || data === 'Failed') badgeClass = 'bg-label-danger';
        if (data === 'Busy') badgeClass = 'bg-label-warning';
        return `<span class="badge ${badgeClass}">${data || '-'}</span>`;
    }

    function recordingMarkup(row, type) {
        if (!row.recording || !row.recording.recording_file) {
            return type === 'display' ? '<span class="text-muted">-</span>' : '';
        }

        if (type !== 'display') return row.recording.recording_file;

        return `
            <a href="${APP_URL}/${row.recording.recording_file}" download class="btn btn-sm btn-outline-success" title="Download Recording">
                <i class="bx bx-download"></i>
            </a>
        `;
    }

    function formattedDate(data, type) {
        if (!data) return '-';
        if (type !== 'display') return data;
        return `<small class="text-muted"><i class="bx bx-calendar me-1"></i>${formatDateTime(data)}</small>`;
    }

    function columns(includeActions) {
        let tableColumns = [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    return leadMarkup(row, type);
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    return userMarkup(row, type);
                }
            },
            {
                data: 'phone',
                render: function (data, type) {
                    if (type !== 'display') return data || '';
                    return data ? `<span class="fw-semibold">${data}</span>` : '-';
                }
            },
            {
                data: 'call_type',
                render: callTypeBadge
            },
            {
                data: 'duration',
                render: function (data, type) {
                    if (type !== 'display') return data || '';
                    return data ? `<span class="badge bg-label-info"><i class="bx bx-time-five me-1"></i>${data}</span>` : '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'call_status',
                render: statusBadge
            },
            {
                data: null,
                orderable: false,
                render: function (data, type, row) {
                    return recordingMarkup(row, type);
                }
            },
            {
                data: 'created_at',
                render: formattedDate
            }
        ];

        if (includeActions) {
            tableColumns.push({
                data: null,
                orderable: false,
                render: function (data, type, row) {
                    return `
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item btn-edit-call-log" href="javascript:void(0);" data-id="${row.call_id}">
                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                </a>
                                <a class="dropdown-item text-danger btn-delete-call-log" href="javascript:void(0);" data-id="${row.call_id}">
                                    <i class="bx bx-trash me-1"></i> Delete
                                </a>
                            </div>
                        </div>
                    `;
                }
            });
        }

        return tableColumns;
    }

    function tableLayout() {
        return {
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
        };
    }

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

    if ($('#call-logs-table').length) {
        let callLogTable = $('#call-logs-table').DataTable({
            ajax: {
                url: APP_URL + '/admin/call-logs/data',
                dataSrc: 'data'
            },
            columns: columns(true),
            layout: tableLayout(),
            pageLength: 10,
            ordering: false
        });

        $('.modal').on('hidden.bs.modal', function () {
            let form = $(this).find('form');
            if (form.length) clearValidationErrors(form);
        });

        $('#addCallLogForm').on('submit', function (e) {
            e.preventDefault();
            let form = $(this);
            let submitBtn = $('#addCallLogSubmitBtn');
            let spinner = submitBtn.find('.spinner-border');

            submitBtn.prop('disabled', true);
            spinner.removeClass('d-none');
            clearValidationErrors(form);

            $.ajax({
                url: APP_URL + '/admin/call-logs/store',
                type: 'POST',
                data: form.serialize(),
                success: function (response) {
                    if (response.status) {
                        $('#addCallLogModal').modal('hide');
                        form[0].reset();
                        callLogTable.ajax.reload(null, false);
                        showAlert('success', response.message);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        showValidationErrors(form, xhr.responseJSON.errors);
                    } else {
                        showAlert('danger', 'An error occurred while saving the call log.');
                    }
                },
                complete: function () {
                    submitBtn.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            });
        });

        $(document).on('click', '.btn-edit-call-log', function () {
            let id = $(this).data('id');
            let form = $('#editCallLogForm');
            clearValidationErrors(form);

            $.ajax({
                url: APP_URL + '/admin/call-logs/edit/' + id,
                type: 'GET',
                success: function (response) {
                    if (response.status) {
                        let item = response.data;
                        $('#edit_call_log_id').val(item.call_id);
                        $('#edit_call_log_lead_id').val(item.lead_id || '');
                        $('#edit_call_log_user_id').val(item.user_id || '');
                        $('#edit_call_log_phone').val(item.phone || '');
                        $('#edit_call_log_call_type').val(item.call_type || '');
                        $('#edit_call_log_duration').val(item.duration || '');
                        $('#edit_call_log_call_status').val(item.call_status || '');
                        $('#edit_call_log_recording_id').val(item.recording_id || '');
                        $('#edit_call_log_created_at').val(item.created_at ? item.created_at.substring(0, 16) : '');
                        $('#editCallLogModal').modal('show');
                    }
                },
                error: function () {
                    showAlert('danger', 'Failed to fetch call log details.');
                }
            });
        });

        $('#editCallLogForm').on('submit', function (e) {
            e.preventDefault();
            let form = $(this);
            let id = $('#edit_call_log_id').val();
            let submitBtn = $('#editCallLogSubmitBtn');
            let spinner = submitBtn.find('.spinner-border');

            submitBtn.prop('disabled', true);
            spinner.removeClass('d-none');
            clearValidationErrors(form);

            $.ajax({
                url: APP_URL + '/admin/call-logs/update/' + id,
                type: 'POST',
                data: form.serialize(),
                success: function (response) {
                    if (response.status) {
                        $('#editCallLogModal').modal('hide');
                        callLogTable.ajax.reload(null, false);
                        showAlert('success', response.message);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        showValidationErrors(form, xhr.responseJSON.errors);
                    } else {
                        showAlert('danger', 'Failed to update call log.');
                    }
                },
                complete: function () {
                    submitBtn.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            });
        });

        let deleteCallLogId = null;
        $(document).on('click', '.btn-delete-call-log', function () {
            deleteCallLogId = $(this).data('id');
            $('#deleteCallLogModal').modal('show');
        });

        $('#confirmDeleteCallLogBtn').on('click', function () {
            if (!deleteCallLogId) return;

            let btn = $(this);
            btn.prop('disabled', true);

            $.ajax({
                url: APP_URL + '/admin/call-logs/delete/' + deleteCallLogId,
                type: 'DELETE',
                success: function (response) {
                    if (response.status) {
                        $('#deleteCallLogModal').modal('hide');
                        callLogTable.ajax.reload(null, false);
                        showAlert('success', response.message);
                    }
                },
                error: function () {
                    showAlert('danger', 'Failed to delete call log.');
                },
                complete: function () {
                    btn.prop('disabled', false);
                    deleteCallLogId = null;
                }
            });
        });
    }

    if ($('#call-log-report-table').length) {
        let reportTable = $('#call-log-report-table').DataTable({
            ajax: {
                url: APP_URL + '/admin/call-logs/report/data',
                type: 'GET',
                data: function (d) {
                    d.filter_type = $('#call_log_filter_type').val();
                    d.user_id = $('#call_log_filter_user_id').val();
                    d.lead_id = $('#call_log_filter_lead_id').val();
                    d.date = $('#call_log_filter_date').val();
                    d.month = $('#call_log_filter_month').val();
                    d.start_date = $('#call_log_filter_start_date').val();
                    d.end_date = $('#call_log_filter_end_date').val();
                    d.call_type = $('#call_log_filter_call_type').val();
                    d.call_status = $('#call_log_filter_call_status').val();
                },
                dataSrc: function (json) {
                    let summary = json.summary || {};
                    $('#call_log_kpi_total').text(summary.total_calls || 0);
                    $('#call_log_kpi_inbound').text(summary.inbound_calls || 0);
                    $('#call_log_kpi_outbound').text(summary.outbound_calls || 0);
                    $('#call_log_kpi_missed').text(summary.missed_calls || 0);
                    $('#call_log_kpi_completed').text(summary.completed_calls || 0);
                    $('#call_log_kpi_recorded').text(summary.recorded_calls || 0);
                    return json.data || [];
                }
            },
            columns: columns(false),
            layout: tableLayout(),
            pageLength: 10,
            ordering: false
        });

        $('.btn-call-log-period').on('click', function () {
            $('.btn-call-log-period').removeClass('active');
            $(this).addClass('active');

            let period = $(this).data('period');
            $('#call_log_filter_type').val(period);
            $('.call-log-filter-input-group').addClass('d-none');

            if (period === 'daily') {
                $('#call_log_group_daily').removeClass('d-none');
                $('#call_log_report_period_label').text('Daily Call Log Report');
            } else if (period === 'weekly') {
                $('#call_log_group_custom_start').removeClass('d-none');
                $('#call_log_report_period_label').text('Weekly Call Log Report');
            } else if (period === 'monthly') {
                $('#call_log_group_monthly').removeClass('d-none');
                $('#call_log_report_period_label').text('Monthly Call Log Report');
            } else if (period === 'custom') {
                $('#call_log_group_custom_start').removeClass('d-none');
                $('#call_log_group_custom_end').removeClass('d-none');
                $('#call_log_report_period_label').text('Custom Call Log Report');
            }

            reportTable.ajax.reload();
        });

        $('#callLogReportFilterForm').on('submit', function (e) {
            e.preventDefault();
            reportTable.ajax.reload();
        });

        $('#resetCallLogReportFilterBtn').on('click', function () {
            $('#callLogReportFilterForm')[0].reset();
            $('.btn-call-log-period').removeClass('active');
            $('.btn-call-log-period[data-period="daily"]').addClass('active');
            $('#call_log_filter_type').val('daily');
            $('.call-log-filter-input-group').addClass('d-none');
            $('#call_log_group_daily').removeClass('d-none');
            $('#call_log_report_period_label').text('Daily Call Log Report');
            reportTable.ajax.reload();
        });
    }
});
