$(document).ready(function () {
    if (!$('#lead-requirements-table').length) return;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Helper to generate dynamic price slab row
    function createPriceRowHtml(rowIndex, data = null) {
        let custType = data ? (data.customer_type || 'all') : 'all';
        let pkgName = data ? (data.package_name || '') : '';
        let qty = data ? (data.quantity !== undefined && data.quantity !== null ? parseFloat(data.quantity) : '') : '';
        let paisa = data ? (data.unit_price_paisa !== undefined && data.unit_price_paisa !== null ? parseFloat(data.unit_price_paisa) : '') : '';
        let subtotal = data ? (data.subtotal !== undefined && data.subtotal !== null ? parseFloat(data.subtotal).toFixed(2) : '') : '';
        let taxPct = data ? (data.tax_percentage !== undefined && data.tax_percentage !== null ? parseFloat(data.tax_percentage) : '18') : '18';
        let taxAmt = data ? (data.tax_amount !== undefined && data.tax_amount !== null ? parseFloat(data.tax_amount).toFixed(2) : '') : '';
        let totalAmt = data ? (data.total_amount !== undefined && data.total_amount !== null ? parseFloat(data.total_amount).toFixed(2) : '') : '';

        return `
            <tr class="price-row" data-row-idx="${rowIndex}">
                <td>
                    <select name="prices[${rowIndex}][customer_type]" class="form-select form-select-sm price-cust-type fw-semibold">
                        <option value="all" ${custType === 'all' ? 'selected' : ''}>All (Both)</option>
                        <option value="user" ${custType === 'user' ? 'selected' : ''}>User</option>
                        <option value="reseller" ${custType === 'reseller' ? 'selected' : ''}>Reseller</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="prices[${rowIndex}][package_name]" class="form-control form-control-sm price-pkg-name"
                        value="${pkgName}" placeholder="e.g. 10K, 25K, 1L" required>
                </td>
                <td>
                    <input type="number" step="any" min="0" name="prices[${rowIndex}][quantity]" class="form-control form-control-sm price-qty text-end"
                        value="${qty}" placeholder="10000" required>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">₹</span>
                        <input type="number" step="0.0001" min="0" name="prices[${rowIndex}][unit_price_paisa]" class="form-control form-control-sm price-paisa text-end"
                            value="${paisa}" placeholder="0.50" required>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">₹</span>
                        <input type="number" step="0.01" min="0" name="prices[${rowIndex}][subtotal]" class="form-control form-control-sm price-subtotal text-end bg-light fw-semibold"
                            value="${subtotal}" placeholder="0.00" readonly>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="number" step="0.01" min="0" name="prices[${rowIndex}][tax_percentage]" class="form-control form-control-sm price-tax-pct text-center px-1"
                            value="${taxPct}" placeholder="18">
                        <span class="input-group-text px-1 bg-light text-muted fw-bold">%</span>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">₹</span>
                        <input type="number" step="0.01" min="0" name="prices[${rowIndex}][tax_amount]" class="form-control form-control-sm price-tax-amt text-end bg-light"
                            value="${taxAmt}" placeholder="0.00" readonly>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">₹</span>
                        <input type="number" step="0.01" min="0" name="prices[${rowIndex}][total_amount]" class="form-control form-control-sm price-total-amt text-end fw-bold bg-light text-success"
                            value="${totalAmt}" placeholder="0.00" readonly>
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm p-1 remove-price-row-btn" title="Remove Slab">
                        <i class="bx bx-trash fs-6"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    function recalcPriceRow($row) {
        let qty = parseFloat($row.find('.price-qty').val()) || 0;
        let paisa = parseFloat($row.find('.price-paisa').val()) || 0;
        let taxPct = parseFloat($row.find('.price-tax-pct').val()) || 0;

        let subtotal = Math.round((qty * paisa) * 100) / 100;
        let taxAmt = Math.round(((subtotal * taxPct) / 100) * 100) / 100;
        let totalAmt = Math.round((subtotal + taxAmt) * 100) / 100;

        $row.find('.price-subtotal').val(subtotal > 0 || (qty > 0 && paisa > 0) ? subtotal.toFixed(2) : '');
        $row.find('.price-tax-amt').val(taxAmt > 0 || subtotal > 0 ? taxAmt.toFixed(2) : '');
        $row.find('.price-total-amt').val(totalAmt > 0 || subtotal > 0 ? totalAmt.toFixed(2) : '');
    }

    // Add Price Slab button click
    $(document).on('click', '.add-price-row-btn', function () {
        let $table = $(this).closest('.modal-content').find('.price-slabs-table');
        let $tbody = $table.find('.price-slabs-tbody');
        let nextIndex = $tbody.find('tr.price-row').length;
        $tbody.append(createPriceRowHtml(nextIndex));
    });

    // Remove Price Slab row
    $(document).on('click', '.remove-price-row-btn', function () {
        let $tbody = $(this).closest('.price-slabs-tbody');
        $(this).closest('tr.price-row').remove();
        if ($tbody.find('tr.price-row').length === 0) {
            $tbody.append(createPriceRowHtml(0));
        }
    });

    // Real-time calculation on Qty, Paisa, Tax % input
    $(document).on('input change', '.price-qty, .price-paisa, .price-tax-pct', function () {
        recalcPriceRow($(this).closest('tr.price-row'));
    });

    // Initialize DataTables
    let requirementTable = $('#lead-requirements-table').DataTable({
        ajax: {
            url: APP_URL + '/admin/lead-requirements/data',
            dataSrc: 'data'
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
                data: 'name',
                render: function (data, type) {
                    if (type !== 'display') return data || '';
                    return `<strong class="text-dark">${data}</strong>`;
                }
            },
            {
                data: 'prices',
                render: function (data, type, row) {
                    let prices = data || [];
                    if (!prices.length) {
                        return '<span class="badge bg-label-secondary">No Slabs</span>';
                    }

                    let userCount = prices.filter(p => p.customer_type === 'user').length;
                    let resellerCount = prices.filter(p => p.customer_type === 'reseller').length;
                    let allCount = prices.filter(p => p.customer_type === 'all').length;

                    let badges = `<span class="badge bg-label-primary fw-bold me-1">${prices.length} Slab(s)</span>`;
                    if (userCount > 0) badges += `<span class="badge bg-label-info me-1" title="User Slabs">User: ${userCount}</span>`;
                    if (resellerCount > 0) badges += `<span class="badge bg-label-warning me-1" title="Reseller Slabs">Reseller: ${resellerCount}</span>`;
                    if (allCount > 0 && (userCount > 0 || resellerCount > 0)) badges += `<span class="badge bg-label-secondary me-1">All: ${allCount}</span>`;

                    let previewList = prices.slice(0, 3).map(p => {
                        let typeTag = p.customer_type === 'reseller' ? '<span class="text-warning fw-bold">[R]</span> ' : (p.customer_type === 'user' ? '<span class="text-info fw-bold">[U]</span> ' : '');
                        return `${typeTag}${p.package_name || p.quantity}: ₹${parseFloat(p.total_amount).toLocaleString('en-IN')}`;
                    }).join(' | ');

                    if (prices.length > 3) previewList += ` <span class="text-primary">+${prices.length - 3} more</span>`;

                    return `<div>${badges}<br><small class="text-muted">${previewList}</small></div>`;
                }
            },
            {
                data: 'status',
                render: function (data, type, row) {
                    let statusText = data == 1 ? 'Active' : 'Inactive';
                    if (type !== 'display') return statusText;
                    let isChecked = data == 1 ? 'checked' : '';
                    let statusLabel = data == 1 ? '<span class="badge bg-label-success">Active</span>' : '<span class="badge bg-label-secondary">Inactive</span>';
                    return `
                        <div class="d-flex align-items-center gap-2">
                            ${statusLabel}
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input btn-toggle-requirement-status" type="checkbox" data-id="${row.lead_requirements_id}" ${isChecked}>
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
                                <a class="dropdown-item btn-edit-requirement" href="javascript:void(0);" data-id="${row.lead_requirements_id}">
                                    <i class="bx bx-edit-alt me-1"></i> Edit & Configure Slabs
                                </a>
                                <a class="dropdown-item text-danger btn-delete-requirement" href="javascript:void(0);" data-id="${row.lead_requirements_id}" data-name="${row.name}">
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

    // When Add Modal Opens, reset form and provide 1 initial slab row
    $('#addLeadRequirementModal').on('show.bs.modal', function () {
        let $form = $('#addLeadRequirementForm');
        $form[0].reset();
        clearValidationErrors($form);
        let $tbody = $form.find('.price-slabs-tbody');
        $tbody.empty().append(createPriceRowHtml(0));
    });

    // Add Lead Requirement Form Submit
    $('#addLeadRequirementForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let submitBtn = $('#addLeadRequirementSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/lead-requirements/store',
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#addLeadRequirementModal').modal('hide');
                    form[0].reset();
                    requirementTable.ajax.reload(null, false);
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

    // Open Edit Lead Requirement Modal
    $(document).on('click', '.btn-edit-requirement', function () {
        let id = $(this).data('id');
        let form = $('#editLeadRequirementForm');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/lead-requirements/edit/' + id,
            type: 'GET',
            success: function (response) {
                if (response.status) {
                    let requirement = response.data;
                    $('#edit_lead_requirements_id').val(requirement.lead_requirements_id);
                    $('#edit_requirement_name').val(requirement.name);
                    $('#edit_requirement_status').val(requirement.status);

                    let $tbody = form.find('.price-slabs-tbody');
                    $tbody.empty();

                    let prices = requirement.prices || [];
                    if (prices.length > 0) {
                        prices.forEach(function (p, idx) {
                            $tbody.append(createPriceRowHtml(idx, p));
                        });
                    } else {
                        $tbody.append(createPriceRowHtml(0));
                    }

                    $('#editLeadRequirementModal').modal('show');
                }
            },
            error: function () {
                showAlert('danger', 'Failed to fetch lead requirement details.');
            }
        });
    });

    // Update Lead Requirement Form Submit
    $('#editLeadRequirementForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let id = $('#edit_lead_requirements_id').val();
        let submitBtn = $('#editLeadRequirementSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/lead-requirements/update/' + id,
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.status) {
                    $('#editLeadRequirementModal').modal('hide');
                    requirementTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', 'An error occurred while updating lead requirement.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Open Delete Confirmation Modal
    let deleteRequirementId = null;
    $(document).on('click', '.btn-delete-requirement', function () {
        deleteRequirementId = $(this).data('id');
        let name = $(this).data('name');
        $('#delete_requirement_name').text(name);
        $('#deleteLeadRequirementModal').modal('show');
    });

    // Confirm Delete Lead Requirement
    $('#confirmDeleteRequirementBtn').on('click', function () {
        if (!deleteRequirementId) return;

        let btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: APP_URL + '/admin/lead-requirements/delete/' + deleteRequirementId,
            type: 'DELETE',
            success: function (response) {
                if (response.status) {
                    $('#deleteLeadRequirementModal').modal('hide');
                    requirementTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Failed to delete lead requirement.');
            },
            complete: function () {
                btn.prop('disabled', false);
                deleteRequirementId = null;
            }
        });
    });

    // Toggle Status
    $(document).on('change', '.btn-toggle-requirement-status', function () {
        let id = $(this).data('id');
        let checkbox = $(this);

        $.ajax({
            url: APP_URL + '/admin/lead-requirements/change-status/' + id,
            type: 'POST',
            success: function (response) {
                if (response.status) {
                    requirementTable.ajax.reload(null, false);
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
