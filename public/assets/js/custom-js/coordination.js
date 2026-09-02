$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                    let staffName = data && data.name ? data.name : 'N/A';
                    if (type !== 'display') return staffName;
                    return `<strong>${staffName}</strong>`;
                }
            },
            {
                data: 'link',
                render: function (data, type) {
                    if (!data) return '<span class="text-muted">N/A</span>';
                    if (type !== 'display') return data;
                    let targetUrl = data.startsWith('http://') || data.startsWith('https://') ? data : 'https://' + data;
                    return `<a href="${targetUrl}" target="_blank" class="text-primary text-break"><i class="bx bx-link-external me-1"></i>${data}</a>`;
                }
            },
            {
                data: 'creator',
                render: function (data, type, row) {
                    let creatorName = data && data.name ? data.name : (row.created_by ? 'User #' + row.created_by : 'N/A');
                    if (type !== 'display') return creatorName;
                    return `<span class="badge bg-label-info">${creatorName}</span>`;
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
