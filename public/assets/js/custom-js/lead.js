$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let leadTable = $('#leads-table').DataTable({
        ajax: {
            url: APP_URL + '/admin/leads/data',
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
                data: 'lead_title',
                render: function (data, type) {
                    if (type !== 'display') return data || '';
                    return `<strong>${data}</strong>`;
                }
            },
            {
                data: 'customer',
                render: function (data, type) {
                    if (!data) return type !== 'display' ? 'N/A' : '<span class="text-muted">N/A</span>';
                    if (type !== 'display') return `${data.name} (${data.mobile})`;
                    return `<div><strong>${data.name}</strong><br><small class="text-muted">${data.mobile}</small></div>`;
                }
            },
            {
                data: 'lead_source',
                render: function (data, type) {
                    if (!data) return type !== 'display' ? 'N/A' : '<span class="text-muted">N/A</span>';
                    if (type !== 'display') return data.name;
                    return `<span class="badge bg-label-info">${data.name}</span>`;
                }
            },
            {
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
            },
            {
                data: 'expected_amount',
                render: function (data, type) {
                    let formatted = data ? `₹${parseFloat(data).toLocaleString('en-IN', { minimumFractionDigits: 2 })}` : '-';
                    if (type !== 'display') return data ? parseFloat(data).toFixed(2) : '-';
                    return data ? formatted : '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'assigned_user',
                render: function (data, type) {
                    if (!data) return type !== 'display' ? 'Unassigned' : '<span class="badge bg-label-secondary">Unassigned</span>';
                    return data.name;
                }
            },
            {
                data: 'next_followup_date',
                render: function (data, type) {
                    if (type !== 'display') return data || '-';
                    return data ? `<span class="text-nowrap"><i class="bx bx-calendar me-1"></i>${formatDate(data)}</span>` : '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'status',
                render: function (data, type, row) {
                    let statusText = data == 1 ? 'Active' : 'Closed';
                    if (type !== 'display') return statusText;
                    let isChecked = data == 1 ? 'checked' : '';
                    let statusLabel = data == 1 ? '<span class="badge bg-label-success">Active</span>' : '<span class="badge bg-label-secondary">Closed</span>';
                    return `
                        <div class="d-flex align-items-center gap-2">
                            ${statusLabel}
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input btn-toggle-lead-status" type="checkbox" data-id="${row.lead_id}" ${isChecked}>
                            </div>
                        </div>
                    `;
                }
            },
            {
                data: null,
                orderable: false,
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

    // Reset validation errors on modal hide
    $('.modal').on('hidden.bs.modal', function () {
        let form = $(this).find('form');
        if (form.length) {
            clearValidationErrors(form);
        }
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
                    $('#edit_lead_id').val(lead.lead_id);
                    $('#edit_lead_customer_id').val(lead.customer_id);
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
});
