$(document).ready(function () {
    if (!$('#attendance-table').length) return;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let canManageAll = false;

    let attendanceTable = $('#attendance-table').DataTable({
        ajax: {
            url: APP_URL + '/admin/attendance/data',
            dataSrc: function (json) {
                canManageAll = json.can_manage_all || false;
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
                data: 'date',
                render: function (data, type) {
                    if (!data) return '-';
                    if (type !== 'display') return data;
                    let formatted = new Date(data).toLocaleDateString();
                    return `<span class="badge bg-label-dark"><i class="bx bx-calendar me-1"></i>${formatted}</span>`;
                }
            },
            {
                data: 'check_in',
                render: function (data, type) {
                    if (!data) return '-';
                    if (type !== 'display') return data;
                    return `<span class="badge bg-label-success"><i class="bx bx-log-in me-1"></i>${data}</span>`;
                }
            },
            {
                data: 'check_out',
                render: function (data, type) {
                    if (!data) return '<span class="text-muted">Not Checked Out</span>';
                    if (type !== 'display') return data;
                    return `<span class="badge bg-label-danger"><i class="bx bx-log-out me-1"></i>${data}</span>`;
                }
            },
            {
                data: 'working_hours',
                render: function (data, type) {
                    if (!data) return '<span class="text-muted">-</span>';
                    if (type !== 'display') return data;
                    return `<span class="badge bg-label-info"><i class="bx bx-time-five me-1"></i>${data}</span>`;
                }
            },
            {
                data: 'status',
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
                render: function (data, type, row) {
                    if (canManageAll) {
                        return `
                            <button class="btn btn-sm btn-outline-primary btn-edit-attendance me-1" data-id="${row.attendance_id}">
                                <i class="bx bx-edit-alt"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-delete-attendance" data-id="${row.attendance_id}">
                                <i class="bx bx-trash"></i>
                            </button>
                        `;
                    }
                    return `<span class="text-muted fs-7">-</span>`;
                }
            }
        ],
        layout: {
            topStart: ['pageLength', { buttons: ['copy', 'csv', 'excel', 'pdf', 'print'] }],
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging'
        },
        pageLength: 10,
        ordering: false
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

    // Self Attendance Quick Check In / Check Out
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
                }
            },
            error: function (xhr) {
                let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to record attendance.';
                showAlert('danger', msg);
                btn.prop('disabled', false);
            }
        });
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
                    attendanceTable.ajax.reload(null, false);
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
                    attendanceTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', 'Failed to update attendance.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Delete Attendance
    let deleteAttendanceId = null;
    $(document).on('click', '.btn-delete-attendance', function () {
        deleteAttendanceId = $(this).data('id');
        $('#deleteAttendanceModal').modal('show');
    });

    $('#confirmDeleteAttendanceBtn').on('click', function () {
        if (!deleteAttendanceId) return;

        let btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: APP_URL + '/admin/attendance/delete/' + deleteAttendanceId,
            type: 'DELETE',
            success: function (response) {
                if (response.status) {
                    $('#deleteAttendanceModal').modal('hide');
                    attendanceTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Failed to delete attendance.');
            },
            complete: function () {
                btn.prop('disabled', false);
                deleteAttendanceId = null;
            }
        });
    });
});
