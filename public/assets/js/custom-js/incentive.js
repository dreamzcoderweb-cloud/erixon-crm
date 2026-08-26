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

    let incentivesTable = null;
    let userCanEdit = false;
    let userCanDelete = false;

    if ($('#incentives-table').length) {
        incentivesTable = $('#incentives-table').DataTable({
            ajax: {
                url: APP_URL + '/admin/incentives/data',
                data: function (d) {
                    d.staff_id = $('#filter_incentive_staff_id').val();
                    d.month = $('#filter_incentive_month').val();
                },
                dataSrc: function (json) {
                    userCanEdit = json.can_edit || false;
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
                    data: 'staff',
                    render: function (data, type, row) {
                        let name = row.staff ? row.staff.name : 'N/A';
                        let email = row.staff ? row.staff.email : '';
                        if (type !== 'display') return name;
                        return `<div><strong>${name}</strong><br><small class="text-muted">${email}</small></div>`;
                    }
                },
                {
                    data: 'month',
                    render: function (data, type) {
                        if (type !== 'display') return data;
                        return `<span class="badge bg-label-info"><i class="bx bx-calendar me-1"></i>${data}</span>`;
                    }
                },
                {
                    data: 'amount',
                    render: function (data, type) {
                        let val = parseFloat(data || 0);
                        if (type !== 'display') return val;
                        return `<strong class="text-success">₹${val.toLocaleString('en-IN', {minimumFractionDigits: 2})}</strong>`;
                    }
                },
                {
                    data: 'remarks',
                    render: function (data) {
                        return data ? `<small class="text-muted">${data}</small>` : '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'creator',
                    render: function (data, type, row) {
                        let name = row.creator ? row.creator.name : 'System';
                        if (type !== 'display') return name;
                        return `<span class="badge bg-label-secondary">${name}</span>`;
                    }
                },
                {
                    data: 'created_at',
                    render: function (data, type, row) {
                        let formatted = row.created_at ? formatDate(row.created_at) : '';
                        if (type !== 'display') return formatted;
                        return `<small class="text-muted">${formatted}</small>`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function (data, type, row) {
                        let dropdownItemsHtml = '';

                        if (userCanEdit) {
                            dropdownItemsHtml += `
                                <a class="dropdown-item btn-edit-incentive" href="javascript:void(0);" data-id="${row.incentive_id}">
                                    <i class="bx bx-edit me-1"></i> Edit
                                </a>
                            `;
                        }

                        if (userCanDelete) {
                            dropdownItemsHtml += `
                                <a class="dropdown-item text-danger btn-delete-incentive" href="javascript:void(0);" data-id="${row.incentive_id}">
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
                            {
                                extend: 'copy',
                                className: 'btn btn-secondary btn-sm me-1',
                                exportOptions: { columns: ':visible:not(:last-child)' }
                            },
                            {
                                extend: 'csv',
                                className: 'btn btn-secondary btn-sm me-1',
                                exportOptions: { columns: ':visible:not(:last-child)' }
                            },
                            {
                                extend: 'excel',
                                className: 'btn btn-secondary btn-sm me-1',
                                exportOptions: { columns: ':visible:not(:last-child)' }
                            },
                            {
                                extend: 'pdf',
                                className: 'btn btn-secondary btn-sm me-1',
                                exportOptions: { columns: ':visible:not(:last-child)' }
                            },
                            {
                                extend: 'print',
                                className: 'btn btn-secondary btn-sm',
                                exportOptions: { columns: ':visible:not(:last-child)' }
                            }
                        ]
                    }
                ],
                topEnd: 'search',
                bottomStart: 'info',
                bottomEnd: 'paging'
            },
            pageLength: 10,
            ordering: false
        });

        $('#filter_incentive_staff_id, #filter_incentive_month').on('change', function () {
            incentivesTable.ajax.reload();
        });
    }

    // Add Incentive Submit
    $('#addIncentiveForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let submitBtn = $('#submitIncentiveBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/incentives/store',
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#addIncentiveModal').modal('hide');
                    form[0].reset();
                    if (incentivesTable) incentivesTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', 'An error occurred while adding incentive.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Open Edit Incentive Modal
    $(document).on('click', '.btn-edit-incentive', function () {
        let id = $(this).data('id');
        let form = $('#editIncentiveForm');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/incentives/edit/' + id,
            type: 'GET',
            success: function (response) {
                if (response.status) {
                    let data = response.data;
                    $('#edit_incentive_id').val(data.incentive_id);
                    $('#edit_incentive_staff_id').val(data.staff_id);
                    $('#edit_incentive_month').val(data.month);
                    $('#edit_incentive_amount').val(data.amount);
                    $('#edit_incentive_remarks').val(data.remarks || '');
                    $('#editIncentiveModal').modal('show');
                }
            },
            error: function () {
                showAlert('danger', 'Failed to fetch incentive details.');
            }
        });
    });

    // Update Incentive Submit
    $('#editIncentiveForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let id = $('#edit_incentive_id').val();
        let submitBtn = $('#updateIncentiveBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/incentives/update/' + id,
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#editIncentiveModal').modal('hide');
                    if (incentivesTable) incentivesTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', 'An error occurred while updating incentive.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Delete Incentive
    $(document).on('click', '.btn-delete-incentive', function () {
        let id = $(this).data('id');
        if (!confirm('Are you sure you want to delete this incentive record?')) return;

        $.ajax({
            url: APP_URL + '/admin/incentives/delete/' + id,
            type: 'DELETE',
            success: function (response) {
                if (response.status) {
                    if (incentivesTable) incentivesTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Failed to delete incentive record.');
            }
        });
    });
});
