$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
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

    // Auto calculate leave days from date range (excluding Sundays/weekly holidays)
    $('#leave_from_date, #leave_to_date').on('change', function () {
        let from = $('#leave_from_date').val();
        let to = $('#leave_to_date').val();
        if (from && to) {
            let d1 = new Date(from);
            let d2 = new Date(to);
            if (d2 >= d1) {
                let workingDays = 0;
                let cur = new Date(d1.getTime());
                while (cur <= d2) {
                    if (cur.getDay() !== 0) { // 0 is Sunday
                        workingDays++;
                    }
                    cur.setDate(cur.getDate() + 1);
                }
                $('#leave_number_of_days').val(workingDays);
            }
        }
    });

    // Leave Requests DataTable
    let leavesTable = null;
    let userCanApprove = false;
    let userCanDelete = false;

    if ($('#leaves-table').length) {
        leavesTable = $('#leaves-table').DataTable({
            ajax: {
                url: APP_URL + '/admin/leaves/data',
                data: function (d) {
                    d.user_id = $('#filter_leave_user_id').val();
                    d.status = $('#filter_leave_status').val();
                },
                dataSrc: function (json) {
                    userCanApprove = json.can_approve || false;
                    userCanDelete = json.can_delete || false;
                    return json.data || [];
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
                    data: 'user',
                    render: function (data, type, row) {
                        let name = row.user ? row.user.name : 'N/A';
                        let email = row.user ? row.user.email : '';
                        if (type !== 'display') return name;
                        return `<div><strong>${name}</strong><br><small class="text-muted">${email}</small></div>`;
                    }
                },
                {
                    data: 'leave_type',
                    render: function (data, type) {
                        if (type !== 'display') return data;
                        return `<span class="badge bg-label-primary">${data}</span>`;
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        let f = row.from_date ? formatDate(row.from_date) : '';
                        let t = row.to_date ? formatDate(row.to_date) : '';
                        if (type !== 'display') return f + ' - ' + t;
                        return `<span class="badge bg-label-dark"><i class="bx bx-calendar me-1"></i>${f} to ${t}</span>`;
                    }
                },
                {
                    data: 'number_of_days',
                    render: function (data, type) {
                        if (type !== 'display') return data;
                        return `<strong>${data} day(s)</strong>`;
                    }
                },
                {
                    data: 'reason',
                    render: function (data) {
                        return data ? `<small class="text-muted">${data}</small>` : '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'status',
                    render: function (data, type) {
                        if (type !== 'display') return data || 'Pending';
                        let badgeClass = 'bg-label-warning';
                        if (data === 'Approved') badgeClass = 'bg-label-success';
                        else if (data === 'Rejected') badgeClass = 'bg-label-danger';
                        return `<span class="badge ${badgeClass}"><i class="bx ${data === 'Approved' ? 'bx-check-circle' : (data === 'Rejected' ? 'bx-x-circle' : 'bx-time')} me-1"></i>${data || 'Pending'}</span>`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function (data, type, row) {
                        let dropdownItemsHtml = '';

                        // Admin Approve / Reject actions
                        if (userCanApprove) {
                            if (row.status === 'Pending') {
                                dropdownItemsHtml += `
                                    <a class="dropdown-item text-success btn-action-leave" href="javascript:void(0);" data-id="${row.id}" data-action="approve">
                                        <i class="bx bx-check me-1"></i> Approve
                                    </a>
                                    <a class="dropdown-item text-danger btn-action-leave" href="javascript:void(0);" data-id="${row.id}" data-action="reject">
                                        <i class="bx bx-x me-1"></i> Reject
                                    </a>
                                `;
                            }
                        }

                        // Delete button
                        if (userCanDelete) {
                            dropdownItemsHtml += `
                                <a class="dropdown-item text-danger btn-delete-leave" href="javascript:void(0);" data-id="${row.id}">
                                    <i class="bx bx-trash me-1"></i> Delete
                                </a>
                            `;
                        }

                        if (!dropdownItemsHtml) {
                            return `<span class="text-muted fs-7">-</span>`;
                        }

                        return `
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    ${dropdownItemsHtml}
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
            pageLength: 10,
            ordering: false,

        });

        $('#filter_leave_user_id, #filter_leave_status').on('change', function () {
            leavesTable.ajax.reload();
        });
    }





    // Submit Leave Form
    $('#requestLeaveForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let submitBtn = $('#submitLeaveBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/leaves/store',
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#requestLeaveModal').modal('hide');
                    form[0].reset();
                    if (leavesTable) leavesTable.ajax.reload(null, false);
                    if (typeof window.loadAppNotifications === 'function') {
                        window.loadAppNotifications();
                    }
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', 'An error occurred while submitting leave request.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Open Action Modal (Approve / Reject)
    $(document).on('click', '.btn-action-leave', function () {
        let id = $(this).data('id');
        let action = $(this).data('action');

        $('#action_leave_id').val(id);
        $('#action_leave_type').val(action);

        let modalHeader = $('#actionModalHeader');
        let modalTitle = $('#actionModalTitle');
        let confirmBtn = $('#confirmActionLeaveBtn');
        let desc = $('#actionModalDescription');

        if (action === 'approve') {
            modalHeader.attr('class', 'modal-header bg-label-success text-dark');
            modalTitle.html('<i class="bx bx-check-circle me-1"></i> Approve Leave Request');
            desc.text('Confirm approving this leave request. This approved leave will count towards the staff monthly leave calculation and excess leave salary deduction.');
            confirmBtn.attr('class', 'btn btn-success').text('Approve Leave');
        } else {
            modalHeader.attr('class', 'modal-header bg-label-danger text-dark');
            modalTitle.html('<i class="bx bx-x-circle me-1"></i> Reject Leave Request');
            desc.text('Are you sure you want to reject this leave request? Rejected leaves will NOT be included in leave or salary deduction calculations.');
            confirmBtn.attr('class', 'btn btn-danger').text('Reject Leave');
        }

        $('#actionLeaveModal').modal('show');
    });

    // Action Form Submit
    $('#actionLeaveForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let id = $('#action_leave_id').val();
        let action = $('#action_leave_type').val();
        let url = APP_URL + '/admin/leaves/' + action + '/' + id;

        $.ajax({
            url: url,
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#actionLeaveModal').modal('hide');
                    if (leavesTable) leavesTable.ajax.reload(null, false);
                    loadSalaryReport(); // Refresh salary calculation report
                    showAlert('success', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Failed to process leave request action.');
            }
        });
    });

    // Delete Leave
    $(document).on('click', '.btn-delete-leave', function () {
        let id = $(this).data('id');
        if (!confirm('Are you sure you want to delete this leave record?')) return;

        $.ajax({
            url: APP_URL + '/admin/leaves/delete/' + id,
            type: 'DELETE',
            success: function (response) {
                if (response.status) {
                    if (leavesTable) leavesTable.ajax.reload(null, false);
                    loadSalaryReport();
                    showAlert('success', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Failed to delete leave request.');
            }
        });
    });

    // Salary & Excess Leave Deduction Report DataTable
    let salaryReportTable = null;
    let canEditSalaryReport = false;

    function renderEditableSalaryCell(field, val, isEdited, autoVal, row) {
        let formattedVal = '₹' + val.toLocaleString('en-IN', {minimumFractionDigits: 2});
        let badgeClass = field === 'ot_income'
            ? (val > 0 ? 'bg-label-success fw-bold' : 'text-muted')
            : (val > 0 ? 'bg-label-danger fw-bold' : 'text-muted');
        let prefix = field === 'ot_income' ? (val > 0 ? '+' : '') : (val > 0 ? '-' : '');

        let html = `<div class="inline-salary-cell" data-field="${field}" data-user-id="${row.user_id}" data-current-val="${val}">`;
        html += `<span class="badge ${badgeClass} cell-val-display">${prefix}${formattedVal}</span>`;

        if (isEdited) {
            html += `<span class="badge bg-label-warning badge-edited-tag" title="Custom manually edited value (Auto: ₹${autoVal.toFixed(2)})">Edited</span>`;
        }

        if (canEditSalaryReport) {
            html += `<button type="button" class="btn btn-outline-primary btn-inline-edit btn-trigger-edit" title="Edit ${field === 'ot_income' ? 'OT Income' : 'Leave Deduction'}"><i class="bx bx-edit-alt"></i></button>`;
            if (isEdited) {
                html += `<button type="button" class="btn btn-outline-secondary btn-inline-reset btn-trigger-reset" title="Reset to auto-calculated (₹${autoVal.toFixed(2)})"><i class="bx bx-undo"></i></button>`;
            }
        }

        html += `</div>`;
        return html;
    }

    function recalculateKpisFromTable() {
        if (!salaryReportTable) return;
        let data = salaryReportTable.rows().data().toArray();
        let totalDeductions = 0;
        let totalNetSalary = 0;

        $.each(data, function (index, item) {
            totalDeductions += parseFloat(item.salary_deduction || 0);
            totalNetSalary += parseFloat(item.net_salary || 0);
        });

        $('#kpi_total_deductions').text('₹' + totalDeductions.toLocaleString('en-IN', {minimumFractionDigits: 2}));
        $('#kpi_total_net_salary').text('₹' + totalNetSalary.toLocaleString('en-IN', {minimumFractionDigits: 2}));
    }

    function initSalaryReportTable() {
        if (!$('#salary-report-table').length) return;

        salaryReportTable = $('#salary-report-table').DataTable({
            ajax: {
                url: APP_URL + '/admin/leaves/salary-report',
                type: 'GET',
                data: function (d) {
                    let now = new Date();
                    let localMonth = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0');
                    d.month = $('#salary_month').val() || localMonth;
                },
                dataSrc: function (json) {
                    if (json.status) {
                        canEditSalaryReport = json.can_edit || false;
                        $('#kpi_month_title').text(json.month_name);
                        $('#kpi_working_days').text(json.working_days + ' days');
                        $('#kpi_days_breakdown').text(`(${json.total_days} total - ${json.sundays} Sundays)`);

                        let data = json.data || [];
                        let totalDeductions = 0;
                        let totalNetSalary = 0;

                        $.each(data, function (index, item) {
                            totalDeductions += parseFloat(item.salary_deduction || 0);
                            totalNetSalary += parseFloat(item.net_salary || 0);
                        });

                        $('#kpi_total_deductions').text('₹' + totalDeductions.toLocaleString('en-IN', {minimumFractionDigits: 2}));
                        $('#kpi_total_net_salary').text('₹' + totalNetSalary.toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    }
                    return json.data || [];
                }
            },
            columns: [
                {
                    data: 'staff_name',
                    className: 'text-start align-middle',
                    render: function (data, type, row) {
                        if (type !== 'display') return data;
                        return `<div class="d-flex flex-column text-start">
                                    <strong class="text-dark">${data}</strong>
                                    <small class="text-muted">${row.email || ''}</small>
                                </div>`;
                    }
                },
                {
                    data: 'month',
                    className: 'text-center align-middle',
                    render: function (data, type) {
                        if (type !== 'display') return data || '';
                        return `<span class="badge bg-label-dark">${data || '-'}</span>`;
                    }
                },
                {
                    data: 'designation',
                    className: 'text-center align-middle',
                    render: function (data, type) {
                        if (type !== 'display') return data || 'Staff';
                        return `<span class="badge bg-label-info">${data || 'Staff'}</span>`;
                    }
                },
                {
                    data: 'base_salary',
                    className: 'text-center align-middle',
                    render: function (data, type) {
                        let val = parseFloat(data || 0);
                        if (type !== 'display') return val;
                        return `<strong>₹${val.toLocaleString('en-IN', {minimumFractionDigits: 2})}</strong>`;
                    }
                },
                {
                    data: 'ot_income',
                    className: 'text-center align-middle',
                    render: function (data, type, row) {
                        let val = parseFloat(data || 0);
                        if (type !== 'display') return val;
                        let isEdited = row.is_ot_edited || false;
                        let autoVal = parseFloat(row.auto_ot_income || 0);
                        return renderEditableSalaryCell('ot_income', val, isEdited, autoVal, row);
                    }
                },
                {
                    data: 'available_leave_count',
                    className: 'text-center align-middle',
                    render: function (data, type) {
                        if (type !== 'display') return data;
                        return `<span class="badge bg-label-secondary">${data} day(s)</span>`;
                    }
                },
                {
                    data: 'approved_leave_days',
                    className: 'text-center align-middle',
                    render: function (data, type, row) {
                        if (type !== 'display') return data;
                        let badgeClass = parseFloat(data) > parseFloat(row.available_leave_count || 0) ? 'bg-label-danger' : 'bg-label-primary';
                        return `<span class="badge ${badgeClass}">${data} day(s)</span>`;
                    }
                },
                {
                    data: 'excess_leave_days',
                    className: 'text-center align-middle',
                    render: function (data, type) {
                        if (type !== 'display') return data;
                        let badgeClass = parseFloat(data) > 0 ? 'bg-label-danger' : 'bg-label-success';
                        return `<span class="badge ${badgeClass}">${data} day(s)</span>`;
                    }
                },
                {
                    data: 'total_leave_days',
                    className: 'text-center align-middle',
                    render: function (data, type) {
                        if (type !== 'display') return data;
                        return `<span class="badge bg-label-primary">${data} day(s)</span>`;
                    }
                },
                {
                    data: 'paid_leave_days',
                    className: 'text-center align-middle',
                    render: function (data, type) {
                        if (type !== 'display') return data;
                        return `<span class="badge bg-label-success">${data} day(s)</span>`;
                    }
                },
                {
                    data: 'unpaid_leave_days',
                    className: 'text-center align-middle',
                    render: function (data, type) {
                        if (type !== 'display') return data;
                        return `<span class="badge ${parseFloat(data || 0) > 0 ? 'bg-label-danger' : 'bg-label-secondary'}">${data} day(s)</span>`;
                    }
                },
                {
                    data: 'per_day_salary',
                    className: 'text-center align-middle',
                    render: function (data, type) {
                        let val = parseFloat(data || 0);
                        if (type !== 'display') return val;
                        return `<span class="text-dark fw-semibold">₹${val.toFixed(2)} / day</span>`;
                    }
                },
                {
                    data: 'leave_deduction',
                    className: 'text-center align-middle',
                    render: function (data, type, row) {
                        let val = parseFloat(data || 0);
                        if (type !== 'display') return val;
                        let isEdited = row.is_leave_edited || false;
                        let autoVal = parseFloat(row.auto_leave_deduction || 0);
                        return renderEditableSalaryCell('leave_deduction', val, isEdited, autoVal, row);
                    }
                },
                {
                    data: 'late_deduction',
                    className: 'text-center align-middle',
                    render: function (data, type) {
                        let val = parseFloat(data || 0);
                        if (type !== 'display') return val;
                        return val > 0 ? `<span class="badge bg-label-warning fw-bold">-₹${val.toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>` : '<span class="text-muted">₹0.00</span>';
                    }
                },
                {
                    data: 'salary_deduction',
                    className: 'text-center align-middle',
                    render: function (data, type) {
                        let val = parseFloat(data || 0);
                        if (type !== 'display') return val;
                        return val > 0 ? `<span class="badge bg-label-danger fw-bold">-₹${val.toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>` : '<span class="text-muted">₹0.00</span>';
                    }
                },
                {
                    data: 'incentive_amount',
                    className: 'text-center align-middle',
                    render: function (data, type) {
                        let val = parseFloat(data || 0);
                        if (type !== 'display') return val;
                        return val > 0 ? `<span class="badge bg-label-success fw-bold">+₹${val.toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>` : '<span class="text-muted">₹0.00</span>';
                    }
                },
                {
                    data: 'net_salary',
                    className: 'text-center align-middle',
                    render: function (data, type) {
                        let val = parseFloat(data || 0);
                        if (type !== 'display') return val;
                        return `<span class="badge bg-label-success fs-6 fw-bold">₹${val.toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>`;
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
                                columns: ':not(:first-child)'
                            },
                            { extend: 'copy', className: 'btn btn-secondary btn-sm me-1', exportOptions: { columns: ':visible' } },
                            { extend: 'csv', className: 'btn btn-secondary btn-sm me-1', exportOptions: { columns: ':visible' } },
                            { extend: 'excel', className: 'btn btn-secondary btn-sm me-1', exportOptions: { columns: ':visible' } },
                            { extend: 'pdf', className: 'btn btn-secondary btn-sm me-1', exportOptions: { columns: ':visible' } },
                            { extend: 'print', className: 'btn btn-secondary btn-sm', exportOptions: { columns: ':visible' } }
                        ]
                    }
                ],
                topEnd: 'search',
                bottomStart: 'info',
                bottomEnd: 'paging'
            },
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            pageLength: 10,
            ordering: false,
            autoWidth: false,
            responsive: false
        });
    }

    if ($('#salary-report-table').length) {
        initSalaryReportTable();
    }

    // Trigger inline edit form
    $(document).on('click', '.btn-trigger-edit', function (e) {
        e.stopPropagation();
        let container = $(this).closest('.inline-salary-cell');
        let currentVal = parseFloat(container.data('current-val') || 0);

        // Store previous HTML in case user cancels
        container.data('prev-html', container.html());

        let formHtml = `
            <div class="inline-edit-form">
                <input type="number" step="0.01" min="0" class="form-control form-control-sm inline-edit-input" value="${currentVal}">
                <button type="button" class="btn btn-success btn-xs btn-inline-save" title="Save"><i class="bx bx-check"></i></button>
                <button type="button" class="btn btn-secondary btn-xs btn-inline-cancel" title="Cancel"><i class="bx bx-x"></i></button>
            </div>
        `;

        container.html(formHtml);
        let input = container.find('.inline-edit-input');
        input.focus().select();
    });

    // Cancel inline edit
    $(document).on('click', '.btn-inline-cancel', function (e) {
        e.stopPropagation();
        let container = $(this).closest('.inline-salary-cell');
        let prevHtml = container.data('prev-html');
        if (prevHtml) {
            container.html(prevHtml);
        }
    });

    // Handle Enter and Escape key in inline edit input
    $(document).on('keydown', '.inline-edit-input', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $(this).siblings('.btn-inline-save').click();
        } else if (e.key === 'Escape') {
            e.preventDefault();
            $(this).siblings('.btn-inline-cancel').click();
        }
    });

    // Save inline edit
    $(document).on('click', '.btn-inline-save', function (e) {
        e.stopPropagation();
        let saveBtn = $(this);
        let container = saveBtn.closest('.inline-salary-cell');
        let input = container.find('.inline-edit-input');
        let field = container.data('field');
        let userId = container.data('user-id');
        let val = parseFloat(input.val());

        if (isNaN(val) || val < 0) {
            input.addClass('is-invalid');
            return;
        }

        let now = new Date();
        let localMonth = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0');
        let selectedMonth = $('#salary_month').val() || localMonth;

        saveBtn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i>');

        $.ajax({
            url: APP_URL + '/admin/leaves/salary-report/update-adjustment',
            type: 'POST',
            data: {
                user_id: userId,
                month: selectedMonth,
                field: field,
                value: val
            },
            success: function (response) {
                if (response.status && response.data) {
                    let tr = container.closest('tr');
                    let rowObj = salaryReportTable.row(tr);
                    rowObj.data(response.data).draw(false);

                    let updatedTr = salaryReportTable.row(rowObj.index()).node();
                    $(updatedTr).find('td').addClass('cell-highlight-flash');
                    setTimeout(function () {
                        $(updatedTr).find('td').removeClass('cell-highlight-flash');
                    }, 1500);

                    recalculateKpisFromTable();
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                saveBtn.prop('disabled', false).html('<i class="bx bx-check"></i>');
                let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to update adjustment.';
                showAlert('danger', msg);
            }
        });
    });

    // Reset inline edit back to auto-calculated value
    $(document).on('click', '.btn-trigger-reset', function (e) {
        e.stopPropagation();
        let resetBtn = $(this);
        let container = resetBtn.closest('.inline-salary-cell');
        let field = container.data('field');
        let userId = container.data('user-id');
        let fieldLabel = field === 'ot_income' ? 'OT Income' : 'Leave Deduction';

        if (!confirm(`Are you sure you want to reset ${fieldLabel} back to the auto-calculated value?`)) {
            return;
        }

        let now = new Date();
        let localMonth = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0');
        let selectedMonth = $('#salary_month').val() || localMonth;

        resetBtn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i>');

        $.ajax({
            url: APP_URL + '/admin/leaves/salary-report/reset-adjustment',
            type: 'POST',
            data: {
                user_id: userId,
                month: selectedMonth,
                field: field
            },
            success: function (response) {
                if (response.status && response.data) {
                    let tr = container.closest('tr');
                    let rowObj = salaryReportTable.row(tr);
                    rowObj.data(response.data).draw(false);

                    let updatedTr = salaryReportTable.row(rowObj.index()).node();
                    $(updatedTr).find('td').addClass('cell-highlight-flash');
                    setTimeout(function () {
                        $(updatedTr).find('td').removeClass('cell-highlight-flash');
                    }, 1500);

                    recalculateKpisFromTable();
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                resetBtn.prop('disabled', false).html('<i class="bx bx-undo"></i>');
                let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to reset adjustment.';
                showAlert('danger', msg);
            }
        });
    });

    function reloadSalaryReport() {
        if (salaryReportTable) {
            salaryReportTable.ajax.reload();
            salaryReportTable.columns.adjust().draw();
        } else {
            initSalaryReportTable();
        }
    }

    $('#salaryReportFilterForm').on('submit', function (e) {
        e.preventDefault();
        reloadSalaryReport();
    });

    // Auto load salary report when clicking tab
    $('button[data-bs-target="#navs-salary-report"]').on('shown.bs.tab', function () {
        reloadSalaryReport();
    });
});

