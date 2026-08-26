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
        if ($('#alert-container').length) {
            $('#alert-container').html(alertHtml);
        } else {
            $('.container-p-y').first().prepend(alertHtml);
        }
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

    // Global Self Attendance Quick Check In / Check Out (Dashboard & Attendance View)
    $(document).on('click', '.btn-mark-self-attendance', function () {
        let btn = $(this);
        let type = btn.data('type');
        btn.prop('disabled', true);

        $.ajax({
            url: APP_URL + '/admin/attendance/mark-self',
            type: 'POST',
            data: { type: type },
            success: function (response) {
                if (response.status) {
                    showAlert('success', response.message);
                    setTimeout(function () {
                        location.reload();
                    }, 800);
                } else {
                    showAlert('danger', response.message || 'Failed to record attendance.');
                    btn.prop('disabled', false);
                }
            },
            error: function (xhr) {
                let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to record attendance.';
                showAlert('danger', msg);
                btn.prop('disabled', false);
            }
        });
    });

    let canManageAll = false;
    let attendanceTable = null;

    if ($('#attendance-table').length) {
        attendanceTable = $('#attendance-table').DataTable({
            ajax: {
                url: APP_URL + '/admin/attendance/data',
                data: function (d) {
                    d.filter_type = $('#attendance_filter_period').val();
                    d.date = $('#attendance_filter_date').val();
                    d.month = $('#attendance_filter_month').val();
                    d.start_date = $('#attendance_filter_start_date').val();
                    d.end_date = $('#attendance_filter_end_date').val();
                    d.user_id = $('#attendance_filter_user_id').val();
                    d.checkin_checkout = $('#attendance_filter_checkin_checkout').val();
                    d.status = $('#attendance_filter_status').val();
                    d.check_in_time = $('#attendance_filter_check_in_time').val();
                    d.check_out_time = $('#attendance_filter_check_out_time').val();
                },
                dataSrc: function (json) {
                    canManageAll = json.can_manage_all || false;
                    if (json.total_attendance !== undefined) {
                        $('#kpi_total_attendance').text(json.total_attendance);
                    }
                    if (json.present_count !== undefined) {
                        $('#kpi_present_count').text(json.present_count);
                    }
                    if (json.staff_count !== undefined) {
                        $('#kpi_staff_count').text(json.staff_count);
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
                    data: 'user',
                    render: function (data, type, row) {
                        let name = row.user ? row.user.name : 'N/A';
                        let email = row.user ? row.user.email : '';
                        if (type !== 'display') return name;
                        return `<div><strong>${name}</strong><br><small class="text-muted">${email}</small></div>`;
                    }
                },
                {
                    data: 'date',
                    className: 'text-center',
                    render: function (data, type) {
                        if (!data) return '-';
                        if (type !== 'display') return data;
                        let formatted = formatDate(data);
                        return `<span class="badge bg-label-dark"><i class="bx bx-calendar me-1"></i>${formatted}</span>`;
                    }
                },
                {
                    data: 'check_in',
                    className: 'text-center',
                    render: function (data, type) {
                        if (!data) return '-';
                        if (type !== 'display') return data;
                        return `<span class="badge bg-label-success"><i class="bx bx-log-in me-1"></i>${data}</span>`;
                    }
                },
                {
                    data: 'check_out',
                    className: 'text-center',
                    render: function (data, type) {
                        if (!data) return '<span class="text-muted">Not Checked Out</span>';
                        if (type !== 'display') return data;
                        return `<span class="badge bg-label-danger"><i class="bx bx-log-out me-1"></i>${data}</span>`;
                    }
                },
                {
                    data: 'working_hours',
                    className: 'text-center',
                    render: function (data, type) {
                        if (!data) return '<span class="text-muted">-</span>';
                        if (type !== 'display') return data;
                        return `<span class="badge bg-label-info"><i class="bx bx-time-five me-1"></i>${data}</span>`;
                    }
                },
                {
                    data: 'status',
                    className: 'text-center',
                    render: function (data, type) {
                        if (type !== 'display') return data || 'Present';
                        let badgeClass = 'bg-label-success';
                        if (data === 'Late') badgeClass = 'bg-label-warning';
                        else if (data === 'Half Day') badgeClass = 'bg-label-info';
                        else if (data === 'Absent') badgeClass = 'bg-label-danger';
                        else if (data === 'On Leave') badgeClass = 'bg-label-secondary';
                        return `<span class="badge ${badgeClass}">${data || 'Present'}</span>`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    render: function (data, type, row) {
                        if (canManageAll) {
                            return `
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item btn-edit-attendance" href="javascript:void(0);" data-id="${row.attendance_id}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <a class="dropdown-item text-danger btn-delete-attendance" href="javascript:void(0);" data-id="${row.attendance_id}">
                                            <i class="bx bx-trash me-1"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            `;
                        }
                        return `<span class="text-muted fs-7">-</span>`;
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
            ordering: false
        });

        $('.modal').on('hidden.bs.modal', function () {
            let form = $(this).find('form');
            if (form.length) {
                clearValidationErrors(form);
            }
        });

        // Dynamic Staff Selection Reference Timing Handler
        $('#add_attendance_user_id').on('change', function () {
            let option = $(this).find('option:selected');
            let checkIn = option.data('check-in');
            let checkOut = option.data('check-out');

            if (checkIn) {
                $('#add_attendance_check_in').val(checkIn);
                $('#add_ref_check_in_label').text('Assigned Shift Check-In: ' + checkIn);
            } else {
                $('#add_ref_check_in_label').text('Assigned Shift Check-In: Not Set');
            }

            if (checkOut) {
                $('#add_attendance_check_out').val(checkOut);
                $('#add_ref_check_out_label').text('Assigned Shift Check-Out: ' + checkOut);
            } else {
                $('#add_ref_check_out_label').text('Assigned Shift Check-Out: Not Set');
            }
        });

        // Add Attendance Submit
        $('#addAttendanceForm').on('submit', function (e) {
            e.preventDefault();
            let form = $(this);
            let submitBtn = $('#addAttendanceSubmitBtn');
            let spinner = submitBtn.find('.spinner-border');

            submitBtn.prop('disabled', true);
            spinner.removeClass('d-none');
            clearValidationErrors(form);

            $.ajax({
                url: APP_URL + '/admin/attendance/store',
                type: 'POST',
                data: form.serialize(),
                success: function (response) {
                    if (response.status) {
                        $('#addAttendanceModal').modal('hide');
                        form[0].reset();
                        if (attendanceTable) attendanceTable.ajax.reload(null, false);
                        showAlert('success', response.message);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        showValidationErrors(form, xhr.responseJSON.errors);
                    } else {
                        showAlert('danger', 'An error occurred while saving attendance.');
                    }
                },
                complete: function () {
                    submitBtn.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            });
        });

        // Edit Attendance Open
        $(document).on('click', '.btn-edit-attendance', function () {
            let id = $(this).data('id');
            let form = $('#editAttendanceForm');
            clearValidationErrors(form);

            $.ajax({
                url: APP_URL + '/admin/attendance/edit/' + id,
                type: 'GET',
                success: function (response) {
                    if (response.status) {
                        let att = response.data;
                        $('#edit_attendance_id').val(att.attendance_id);
                        $('#edit_attendance_user_id').val(att.user_id);
                        $('#edit_attendance_date').val(att.date);
                        $('#edit_attendance_check_in').val(att.check_in ? att.check_in.substring(0, 5) : '');
                        $('#edit_attendance_check_out').val(att.check_out ? att.check_out.substring(0, 5) : '');
                        $('#edit_attendance_status').val(att.status);

                        $('#editAttendanceModal').modal('show');
                    }
                },
                error: function () {
                    showAlert('danger', 'Failed to fetch attendance details.');
                }
            });
        });

        // Update Attendance Submit
        $('#editAttendanceForm').on('submit', function (e) {
            e.preventDefault();
            let form = $(this);
            let id = $('#edit_attendance_id').val();
            let submitBtn = $('#editAttendanceSubmitBtn');
            let spinner = submitBtn.find('.spinner-border');

            submitBtn.prop('disabled', true);
            spinner.removeClass('d-none');
            clearValidationErrors(form);

            $.ajax({
                url: APP_URL + '/admin/attendance/update/' + id,
                type: 'POST',
                data: form.serialize(),
                success: function (response) {
                    if (response.status) {
                        $('#editAttendanceModal').modal('hide');
                        if (attendanceTable) attendanceTable.ajax.reload(null, false);
                        showAlert('success', response.message);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        showValidationErrors(form, xhr.responseJSON.errors);
                    } else {
                        showAlert('danger', 'An error occurred while updating attendance.');
                    }
                },
                complete: function () {
                    submitBtn.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            });
        });

        // Delete Attendance Action
        $(document).on('click', '.btn-delete-attendance', function () {
            let id = $(this).data('id');
            if (confirm('Are you sure you want to delete this attendance record?')) {
                $.ajax({
                    url: APP_URL + '/admin/attendance/delete/' + id,
                    type: 'DELETE',
                    success: function (response) {
                        if (response.status) {
                            if (attendanceTable) attendanceTable.ajax.reload(null, false);
                            showAlert('success', response.message);
                        } else {
                            showAlert('danger', response.message);
                        }
                    },
                    error: function () {
                        showAlert('danger', 'An error occurred while deleting attendance.');
                    }
                });
            }
        });

        // Date Period Buttons Toggle
        $('.btn-attendance-period').on('click', function () {
            $('.btn-attendance-period').removeClass('active');
            $(this).addClass('active');

            let period = $(this).data('period');
            $('#attendance_filter_period').val(period);
            $('.attendance-filter-date-group').addClass('d-none');

            if (period === 'daily') {
                $('#attendance_group_daily').removeClass('d-none');
            } else if (period === 'weekly') {
                $('#attendance_group_custom_start').removeClass('d-none');
            } else if (period === 'monthly') {
                $('#attendance_group_monthly').removeClass('d-none');
            } else if (period === 'custom') {
                $('#attendance_group_custom_start').removeClass('d-none');
                $('#attendance_group_custom_end').removeClass('d-none');
            }

            if (attendanceTable) attendanceTable.ajax.reload();
        });

        // Attendance Filter Form Submit
        $('#attendanceFilterForm').on('submit', function (e) {
            e.preventDefault();
            if (attendanceTable) attendanceTable.ajax.reload();
        });

        // Reset Attendance Filters
        $('#resetAttendanceFilterBtn').on('click', function () {
            $('#attendanceFilterForm')[0].reset();
            $('.btn-attendance-period').removeClass('active');
            $('.btn-attendance-period[data-period="all"]').addClass('active');
            $('#attendance_filter_period').val('all');
            $('#attendance_filter_user_id').val('');
            $('#attendance_filter_checkin_checkout').val('');
            $('#attendance_filter_status').val('');
            $('#attendance_filter_check_in_time').val('');
            $('#attendance_filter_check_out_time').val('');
            $('.attendance-filter-date-group').addClass('d-none');
            if (attendanceTable) attendanceTable.ajax.reload();
        });

        // KPI Card Click Handlers
        $('#kpi_card_total_attendance').on('click', function () {
            $('#resetAttendanceFilterBtn').click();
        });
    }
});
