$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize Select2 for multi-select dropdowns
    function initSelect2() {
        if ($.fn.select2) {
            $('#add_joining_staff_ids').select2({
                dropdownParent: $('#addCoordinationModal'),
                placeholder: 'Select Joining Staff members...',
                width: '100%',
                allowClear: true
            });

            $('#edit_joining_staff_ids').select2({
                dropdownParent: $('#editCoordinationModal'),
                placeholder: 'Select Joining Staff members...',
                width: '100%',
                allowClear: true
            });
        }
    }

    // Re-initialize Select2 when modals are opened to ensure proper sizing and alignment
    $('#addCoordinationModal, #editCoordinationModal').on('shown.bs.modal', function () {
        initSelect2();
    });

    initSelect2();

    // When Created Staff is changed in Add modal, auto-ensure it is selected in Joining Staff
    $('#add_staff_id').on('change', function () {
        let val = $(this).val();
        if (val && $.fn.select2) {
            let currentValues = $('#add_joining_staff_ids').val() || [];
            if (!currentValues.includes(val)) {
                currentValues.push(val);
                $('#add_joining_staff_ids').val(currentValues).trigger('change');
            }
        }
    });

    function formatDate(dateStr) {
        if (!dateStr) return 'N/A';
        let d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        let day = String(d.getDate()).padStart(2, '0');
        let month = String(d.getMonth() + 1).padStart(2, '0');
        let year = d.getFullYear();
        return `${day}/${month}/${year}`;
    }

    let coordinationTable = $('#coordinations-table').DataTable({
        ajax: {
            url: APP_URL + '/admin/coordinations/data',
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
                data: 'staff',
                render: function (data, type, row) {
                    let staffName = data && data.name ? data.name : (row.creator && row.creator.name ? row.creator.name : 'N/A');
                    if (type !== 'display') return staffName;
                    return `<strong>${staffName}</strong>`;
                }
            },
            {
                data: 'link',
                render: function (data, type, row) {
                    if (!data) return '<span class="text-muted">N/A</span>';
                    if (type !== 'display') return data;
                    let targetUrl = data.startsWith('http://') || data.startsWith('https://') ? data : 'https://' + data;
                    return `<a href="${targetUrl}" target="_blank" class="text-primary text-break coordination-link-click" data-id="${row.coordination_id}"><i class="bx bx-link-external me-1"></i>${data}</a>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    let total = row.total_joining || 0;
                    let joinedCount = row.joined_count || 0;
                    let pendingCount = row.pending_count || 0;

                    let joinedNames = (row.joined_staff || []).map(s => s.name).join(', ');
                    let pendingNames = (row.pending_staff || []).map(s => s.name).join(', ');

                    if (type !== 'display') {
                        return `Total: ${total}, Joined: ${joinedCount}, Pending: ${pendingCount}`;
                    }

                    let html = `<div class="d-flex flex-column gap-1">`;

                    html += `<div class="d-flex align-items-center gap-1 flex-wrap">`;
                    html += `<span class="badge bg-primary me-1" title="Total Joining Staff">Total: ${total}</span>`;
                    html += `<span class="badge bg-success me-1" title="Joined Staff">Joined: ${joinedCount}</span>`;
                    html += `<span class="badge bg-warning text-dark" title="Pending Staff">Pending: ${pendingCount}</span>`;
                    html += `</div>`;

                    if (joinedCount > 0) {
                        html += `<div class="small"><span class="fw-semibold text-success"><i class="bx bx-check-circle me-1"></i>Joined (${joinedCount}):</span> <span class="text-muted">${joinedNames}</span></div>`;
                    }

                    if (pendingCount > 0) {
                        html += `<div class="small"><span class="fw-semibold text-warning"><i class="bx bx-time-five me-1"></i>Pending (${pendingCount}):</span> <span class="text-muted">${pendingNames}</span></div>`;
                    }

                    html += `</div>`;
                    return html;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    let status = row.user_joined_status || 'Pending';
                    if (type !== 'display') return status;

                    if (status === 'Joined') {
                        return `<button type="button" class="btn btn-xs btn-success btn-toggle-join" data-id="${row.coordination_id}" title="Click to change to Pending">
                                    <i class="bx bx-check-circle me-1"></i> Joined
                                </button>`;
                    } else {
                        return `<button type="button" class="btn btn-xs btn-outline-warning btn-toggle-join" data-id="${row.coordination_id}" title="Click to mark as Joined">
                                    <i class="bx bx-time me-1"></i> Mark Joined
                                </button>`;
                    }
                }
            },
            {
                data: 'created_at',
                render: function (data, type) {
                    let formatted = formatDate(data);
                    if (type !== 'display') return formatted;
                    return `<span class="text-nowrap">${formatted}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: function (data, type, row) {
                    let staffName = row.staff && row.staff.name ? row.staff.name : 'Staff #' + row.staff_id;
                    return `
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item btn-edit-coordination" href="javascript:void(0);" data-id="${row.coordination_id}">
                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                </a>
                                <a class="dropdown-item text-danger btn-delete-coordination" href="javascript:void(0);" data-id="${row.coordination_id}" data-name="${staffName}">
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

    $('.modal').on('hidden.bs.modal', function () {
        let form = $(this).find('form');
        if (form.length) {
            clearValidationErrors(form);
        }
    });

    // Automatically mark user as Joined when clicking the Coordination link
    $(document).on('click', '.coordination-link-click', function () {
        let id = $(this).data('id');
        $.ajax({
            url: APP_URL + '/admin/coordinations/toggle-join/' + id,
            type: 'POST',
            data: { force_join: 1 },
            success: function (response) {
                if (response.status && typeof coordinationTable !== 'undefined') {
                    coordinationTable.ajax.reload(null, false);
                }
            }
        });
    });

    // Toggle Join Status Handler
    $(document).on('click', '.btn-toggle-join', function () {
        let btn = $(this);
        let id = btn.data('id');
        btn.prop('disabled', true);

        $.ajax({
            url: APP_URL + '/admin/coordinations/toggle-join/' + id,
            type: 'POST',
            success: function (response) {
                if (response.status) {
                    coordinationTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Failed to update participation status.');
            },
            complete: function () {
                btn.prop('disabled', false);
            }
        });
    });

    // Add Coordination Form Submit
    $('#addCoordinationForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let submitBtn = $('#addCoordinationSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/coordinations/store',
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#addCoordinationModal').modal('hide');
                    form[0].reset();
                    if ($.fn.select2) {
                        $('#add_joining_staff_ids').val(null).trigger('change');
                    }
                    coordinationTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', xhr.responseJSON?.message || 'An error occurred while creating coordination.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Open Edit Coordination Modal
    $(document).on('click', '.btn-edit-coordination', function () {
        let id = $(this).data('id');
        let form = $('#editCoordinationForm');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/coordinations/edit/' + id,
            type: 'GET',
            success: function (response) {
                if (response.status) {
                    let coordination = response.data;
                    $('#edit_coordination_id').val(coordination.coordination_id);
                    $('#edit_staff_id').val(coordination.staff_id);
                    $('#edit_link').val(coordination.link);

                    if (coordination.joining_staff && $.fn.select2) {
                        let joiningIds = coordination.joining_staff.map(s => s.id);
                        $('#edit_joining_staff_ids').val(joiningIds).trigger('change');
                    }

                    $('#editCoordinationModal').modal('show');
                }
            },
            error: function () {
                showAlert('danger', 'Failed to fetch coordination details.');
            }
        });
    });

    // Update Coordination Form Submit
    $('#editCoordinationForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let id = $('#edit_coordination_id').val();
        let submitBtn = $('#editCoordinationSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/coordinations/update/' + id,
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#editCoordinationModal').modal('hide');
                    coordinationTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', xhr.responseJSON?.message || 'An error occurred while updating coordination.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Open Delete Confirmation Modal
    let deleteCoordinationId = null;
    $(document).on('click', '.btn-delete-coordination', function () {
        deleteCoordinationId = $(this).data('id');
        let name = $(this).data('name');
        $('#delete_staff_name').text(name);
        $('#deleteCoordinationModal').modal('show');
    });

    // Confirm Delete Coordination
    $('#confirmDeleteCoordinationBtn').on('click', function () {
        if (!deleteCoordinationId) return;

        let btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: APP_URL + '/admin/coordinations/delete/' + deleteCoordinationId,
            type: 'DELETE',
            success: function (response) {
                if (response.status) {
                    $('#deleteCoordinationModal').modal('hide');
                    coordinationTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Failed to delete coordination record.');
            },
            complete: function () {
                btn.prop('disabled', false);
                deleteCoordinationId = null;
            }
        });
    });
});
