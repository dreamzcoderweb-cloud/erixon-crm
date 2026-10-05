/**
 * Proposal Management JavaScript
 * - DataTable with full Export options (ColVis, Copy, CSV, Excel, PDF, Print) referencing Customer List pattern
 * - Customer Name as a Searchable Select2 Dropdown with auto-fill and custom customer tag support
 * - Dynamic Product Package rows with real-time Paisa + Tax calculation
 * - CRUD operations, View Details, Print preview, and Delete modal
 */

$(document).ready(function () {
    // Only initialize if proposals table exists on the current page
    if ($('#proposals-table').length === 0) {
        return;
    }

    let itemRowIndex = 0;
    let proposalTable = null;

    // CSRF Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Helper to resolve URLs with base application path (supports subdirectories like /crm on live server)
    function appUrl(path) {
        const base = typeof APP_URL !== 'undefined' ? APP_URL.replace(/\/+$/, '') : '';
        const cleanPath = path.startsWith('/') ? path : '/' + path;
        return base + cleanPath;
    }

    // -------------------------------------------------------------
    // Helper: Alert Display
    // -------------------------------------------------------------
    function showAlert(type, message) {
        const icon = type === 'success' ? 'bx-check-circle' : 'bx-error-circle';
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show shadow-sm mb-3" role="alert">
                <i class="bx ${icon} me-2 fs-5 align-middle"></i>
                <span class="align-middle"><strong>${message}</strong></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('#alert-container').html(alertHtml);
        $('html, body').animate({ scrollTop: $('#alert-container').offset().top - 80 }, 300);

        setTimeout(() => {
            $('#alert-container .alert').alert('close');
        }, 5000);
    }

    // -------------------------------------------------------------
    // 1. Customer Name Searchable Dropdown (Select2)
    // -------------------------------------------------------------
    function initSelect2Dropdowns() {
        if ($.fn.select2) {
            // Customer Name Select2 Searchable Dropdown
            if (!$('#customer_select').hasClass('select2-hidden-accessible')) {
                $('#customer_select').select2({
                    dropdownParent: $('#proposalModal'),
                    placeholder: '-- Search Customer by Name or Mobile --',
                    allowClear: true,
                    width: '100%',
                    tags: true, // Allows typing custom customer name if not in list
                    createTag: function (params) {
                        const term = $.trim(params.term);
                        if (term === '') return null;
                        return {
                            id: term,
                            text: term + ' (New Customer)',
                            newTag: true
                        };
                    }
                });
            }

            // Sales Manager Select2 Searchable Dropdown
            if (!$('#sales_manager_id').hasClass('select2-hidden-accessible')) {
                $('#sales_manager_id').select2({
                    dropdownParent: $('#proposalModal'),
                    placeholder: '-- Select Sales Manager --',
                    allowClear: true,
                    width: '100%'
                });
            }
        }
    }

    // Initialize or re-align Select2 whenever modal is displayed
    $('#proposalModal').on('shown.bs.modal', function () {
        initSelect2Dropdowns();
    });

    // Handle Customer Name Selection from Searchable Dropdown
    $('#customer_select').on('change select2:select', function () {
        const $selected = $(this).find('option:selected');
        const val = $(this).val();

        if (!val) {
            $('#customer_id').val('');
            $('#customer_name').val('');
            $('#customer_mobile').val('');
            return;
        }

        const customerId = $selected.data('id');
        const customerName = $selected.data('name');
        const customerMobile = $selected.data('mobile');

        if (customerId && customerName) {
            // Existing customer from database
            $('#customer_id').val(customerId);
            $('#customer_name').val(customerName);
            if (customerMobile) {
                $('#customer_mobile').val(customerMobile);
            }
        } else {
            // Typed custom customer name
            $('#customer_id').val('');
            $('#customer_name').val(val);
        }
    });

    // Handle Sales Manager Selection & Auto-populate Mobile
    $('#sales_manager_id').on('change select2:select', function () {
        const $selected = $(this).find('option:selected');
        const name = $selected.data('name') || '';
        const mobile = $selected.data('mobile') || '';

        $('#sales_manager_name').val(name);
        if (mobile) {
            $('#sales_manager_mobile').val(mobile);
        }
    });

    // -------------------------------------------------------------
    // 2. Product Package Row Management & Real-time Calculation
    // -------------------------------------------------------------
    function addProductRow(data = null) {
        const pkg = data ? (data.product_package || '') : '';
        const price = data ? (data.price !== undefined ? parseFloat(data.price).toFixed(2) : '') : '';
        const taxPct = data ? (data.tax_percentage !== undefined ? parseFloat(data.tax_percentage).toFixed(0) : '18') : '18';
        const taxAmt = data ? (data.tax_amount !== undefined ? parseFloat(data.tax_amount).toFixed(2) : '') : '';
        const amount = data ? (data.amount !== undefined ? parseFloat(data.amount).toFixed(2) : '') : '';

        const rowHtml = `
            <tr class="item-row" data-row-idx="${itemRowIndex}">
                <td>
                    <input type="text" class="form-control form-control-sm item-pkg" 
                        name="items[${itemRowIndex}][product_package]" 
                        value="${pkg}" 
                        placeholder="e.g. ERP Software Package" required>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">₹</span>
                        <input type="number" step="0.01" min="0" 
                            class="form-control form-control-sm item-price text-end" 
                            name="items[${itemRowIndex}][price]" 
                            value="${price}" 
                            placeholder="0.00" required>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="number" step="0.01" min="0" 
                            class="form-control form-control-sm item-tax-pct text-center px-1 fw-semibold" 
                            name="items[${itemRowIndex}][tax_percentage]" 
                            value="${taxPct}" 
                            placeholder="18"
                            style="-moz-appearance: textfield; -webkit-appearance: none; appearance: none;">
                        <span class="input-group-text px-2 bg-light text-muted fw-bold">%</span>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">₹</span>
                        <input type="number" step="0.01" min="0" 
                            class="form-control form-control-sm item-tax-amt text-end bg-light" 
                            name="items[${itemRowIndex}][tax_amount]" 
                            value="${taxAmt}" 
                            placeholder="0.00" readonly>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">₹</span>
                        <input type="number" step="0.01" min="0" 
                            class="form-control form-control-sm item-amount text-end fw-bold bg-light" 
                            name="items[${itemRowIndex}][amount]" 
                            value="${amount}" 
                            placeholder="0.00" readonly required>
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm p-1 remove-item-row-btn" title="Remove Item">
                        <i class="bx bx-trash fs-6"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#proposalItemsTbody').append(rowHtml);
        itemRowIndex++;

        recalculateRow($('#proposalItemsTbody tr.item-row:last'));
        updateSummaryTotals();
    }

    function recalculateRow($row) {
        const priceVal = parseFloat($row.find('.item-price').val()) || 0;
        const taxPctVal = parseFloat($row.find('.item-tax-pct').val()) || 0;

        const taxAmt = Math.round(((priceVal * taxPctVal) / 100) * 100) / 100;
        const totalAmt = Math.round((priceVal + taxAmt) * 100) / 100;

        $row.find('.item-tax-amt').val(taxAmt > 0 || priceVal > 0 ? taxAmt.toFixed(2) : '');
        $row.find('.item-amount').val(totalAmt > 0 || priceVal > 0 ? totalAmt.toFixed(2) : '');
    }

    function updateSummaryTotals() {
        let subtotal = 0;
        let totalTax = 0;
        let grandTotal = 0;

        $('#proposalItemsTbody tr.item-row').each(function () {
            const price = parseFloat($(this).find('.item-price').val()) || 0;
            const taxAmt = parseFloat($(this).find('.item-tax-amt').val()) || 0;
            const amt = parseFloat($(this).find('.item-amount').val()) || 0;

            subtotal += price;
            totalTax += taxAmt;
            grandTotal += amt;
        });

        $('#summarySubtotal').text('₹' + subtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#summaryTax').text('₹' + totalTax.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#summaryTotalAmount').text('₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    }

    // "( Add product - button)" handler
    $('#addProductRowBtn').on('click', function () {
        addProductRow();
    });

    // Remove product row handler
    $(document).on('click', '.remove-item-row-btn', function () {
        if ($('#proposalItemsTbody tr.item-row').length <= 1) {
            alert('At least one product package row is required.');
            return;
        }
        $(this).closest('tr.item-row').remove();
        updateSummaryTotals();
    });

    // Real-time calculation on price / tax inputs
    $(document).on('input change', '.item-price, .item-tax-pct', function () {
        const $row = $(this).closest('tr.item-row');
        recalculateRow($row);
        updateSummaryTotals();
    });

    // -------------------------------------------------------------
    // 3. DataTable Initialization with Full Export Buttons
    // -------------------------------------------------------------
    function updateKpiMetrics(proposals) {
        let totalVal = 0;
        let totalPkgs = 0;
        const managersSet = new Set();

        proposals.forEach(p => {
            totalVal += parseFloat(p.total_amount || 0);
            if (p.items && p.items.length) {
                totalPkgs += p.items.length;
            }
            if (p.sales_manager_name) {
                managersSet.add(p.sales_manager_name);
            }
        });

        $('#kpi_total_proposals').text(proposals.length);
        $('#kpi_total_amount').text('₹' + totalVal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#kpi_total_packages').text(totalPkgs);
        $('#kpi_active_managers').text(managersSet.size);
    }

    proposalTable = $('#proposals-table').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: appUrl('/admin/proposals/data'),
            data: function (d) {
                d.filter_type = $('#proposal_filter_period').val();
                d.date = $('#proposal_filter_date').val();
                d.month = $('#proposal_filter_month').val();
                d.start_date = $('#proposal_filter_start_date').val();
                d.end_date = $('#proposal_filter_end_date').val();
                d.lead_requirement_id = $('#proposal_filter_lead_requirement_id').val();
                d.sales_manager_id = $('#proposal_filter_sales_manager_id').val();
                d.search = $('#proposal_filter_search').val();
            },
            dataSrc: function (json) {
                const proposals = json.data || [];
                updateKpiMetrics(proposals);
                return proposals;
            },
            error: function (xhr, error, thrown) {
                console.error('Proposals DataTable AJAX error:', xhr, error, thrown);
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
                data: 'proposal_number',
                render: function (data, type, row) {
                    if (type !== 'display') return data;
                    const createdDate = row.created_at ? new Date(row.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '-';
                    return `
                        <strong class="text-primary">${data}</strong>
                        <div class="text-muted fs-tiny">${createdDate}</div>
                    `;
                }
            },
            {
                data: 'customer_name',
                render: function (data, type, row) {
                    if (type !== 'display') return `${data || ''} ${row.customer_mobile || ''}`;
                    return `
                        <div class="fw-bold text-dark">${data || '-'}</div>
                        <div class="text-muted small"><i class="bx bx-phone me-1"></i>${row.customer_mobile || '-'}</div>
                    `;
                }
            },
            {
                data: 'lead_requirement_name',
                render: function (data, type) {
                    if (type !== 'display') return data || 'N/A';
                    return data ? `<span class="badge bg-label-primary">${data}</span>` : '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'items',
                render: function (data, type, row) {
                    if (!data || data.length === 0) return type !== 'display' ? '' : '<span class="text-muted">-</span>';
                    if (type !== 'display') {
                        return data.map(i => i.product_package).join(', ');
                    }
                    const count = data.length;
                    const firstName = data[0].product_package;
                    return `
                        <div class="fw-semibold text-dark text-truncate" style="max-width: 220px;" title="${firstName}">
                            ${firstName}
                        </div>
                        ${count > 1 ? `<span class="badge bg-label-info fs-tiny mt-1">+${count - 1} more packages</span>` : ''}
                    `;
                }
            },
            {
                data: 'sales_manager_name',
                render: function (data, type, row) {
                    if (type !== 'display') return `${data || 'Unassigned'} ${row.sales_manager_mobile || ''}`;
                    return `
                        <div class="fw-semibold">${data || '<span class="text-muted">Unassigned</span>'}</div>
                        ${row.sales_manager_mobile ? `<div class="text-muted small"><i class="bx bx-mobile me-1"></i>${row.sales_manager_mobile}</div>` : ''}
                    `;
                }
            },
            {
                data: 'total_amount',
                render: function (data, type, row) {
                    const num = parseFloat(data || 0);
                    if (type !== 'display') return num;
                    const formatted = '₹' + num.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    const subtotal = '₹' + parseFloat(row.subtotal || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    return `
                        <div class="fw-bold text-success fs-6">${formatted}</div>
                        <div class="text-muted fs-tiny">Sub: ${subtotal}</div>
                    `;
                }
            },
            {
                data: 'status',
                className: 'text-center',
                render: function (data, type) {
                    const st = data || 'Active';
                    if (type !== 'display') return st;
                    return `<span class="badge bg-label-success">${st}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    return `
                        <div class="d-inline-flex gap-1">
                            <button type="button" class="btn btn-icon btn-sm btn-outline-info view-proposal-btn" data-id="${row.proposal_id}" title="View Details">
                                <i class="bx bx-show"></i>
                            </button>
                            <a href="${appUrl('/admin/proposals/print/' + row.proposal_id)}" target="_blank" class="btn btn-icon btn-sm btn-outline-secondary" title="Print Proposal">
                                <i class="bx bx-printer"></i>
                            </a>
                            <button type="button" class="btn btn-icon btn-sm btn-outline-primary edit-proposal-btn" data-id="${row.proposal_id}" title="Edit Proposal">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-sm btn-outline-danger delete-proposal-btn" data-id="${row.proposal_id}" data-number="${row.proposal_number}" title="Delete Proposal">
                                <i class="bx bx-trash"></i>
                            </button>
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
                            text: '<i class="bx bx-copy me-1"></i> Copy',
                            className: 'btn btn-secondary btn-sm me-1',
                            exportOptions: {
                                columns: ':visible:not(:last-child)'
                            }
                        },
                        {
                            extend: 'csv',
                            text: '<i class="bx bx-file me-1"></i> CSV',
                            className: 'btn btn-secondary btn-sm me-1',
                            exportOptions: {
                                columns: ':visible:not(:last-child)'
                            }
                        },
                        {
                            extend: 'excel',
                            text: '<i class="bx bx-spreadsheet me-1"></i> Excel',
                            className: 'btn btn-secondary btn-sm me-1',
                            exportOptions: {
                                columns: ':visible:not(:last-child)'
                            }
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="bx bxs-file-pdf me-1"></i> PDF',
                            className: 'btn btn-secondary btn-sm me-1',
                            exportOptions: {
                                columns: ':visible:not(:last-child)'
                            }
                        },
                        {
                            extend: 'print',
                            text: '<i class="bx bx-printer me-1"></i> Print',
                            className: 'btn btn-secondary btn-sm',
                            exportOptions: {
                                columns: ':visible:not(:last-child)'
                            }
                        }
                    ]
                }
            ],
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging'
        },
        order: [[1, 'desc']],
        pageLength: 10,
        responsive: true
    });

    // -------------------------------------------------------------
    // 4. Date Period Filter UI Switching & Triggers
    // -------------------------------------------------------------
    $('.btn-proposal-period').on('click', function () {
        $('.btn-proposal-period').removeClass('active');
        $(this).addClass('active');

        const period = $(this).data('period');
        $('#proposal_filter_period').val(period);

        $('.proposal-filter-date-group').addClass('d-none');

        if (period === 'daily') {
            $('#proposal_group_daily').removeClass('d-none');
        } else if (period === 'monthly') {
            $('#proposal_group_monthly').removeClass('d-none');
        } else if (period === 'custom') {
            $('#proposal_group_custom_start').removeClass('d-none');
            $('#proposal_group_custom_end').removeClass('d-none');
        }

        if (proposalTable) {
            proposalTable.ajax.reload();
        }
    });

    // Filter Form Submit
    $('#proposalFilterForm').on('submit', function (e) {
        e.preventDefault();
        if (proposalTable) {
            proposalTable.ajax.reload();
        }
    });

    // Reset Filter Button
    $('#resetProposalFilterBtn').on('click', function () {
        $('#proposalFilterForm')[0].reset();
        $('.btn-proposal-period').removeClass('active');
        $('.btn-proposal-period[data-period="all"]').addClass('active');
        $('#proposal_filter_period').val('all');
        $('.proposal-filter-date-group').addClass('d-none');
        if (proposalTable) {
            proposalTable.ajax.reload();
        }
    });

    // -------------------------------------------------------------
    // 5. Modal Open: Add Mode
    // -------------------------------------------------------------
    $('#openAddProposalModalBtn').on('click', function () {
        $('#proposalForm')[0].reset();
        $('#proposal_id').val('');
        $('#customer_id').val('');
        $('#customer_name').val('');
        $('#customer_mobile').val('');
        $('#sales_manager_name').val('');
        $('#sales_manager_mobile').val('');

        // Reset Select2 Dropdowns
        if ($.fn.select2) {
            $('#customer_select').val('').trigger('change');
            $('#sales_manager_id').val('').trigger('change');
        }

        $('#proposalModalTitle').text('Create New Proposal');
        $('#proposalModalSubtitle').text('Enter proposal details, product packages, and sales manager');
        $('#saveProposalBtnText').text('Save Proposal');
        $('#proposalItemsTbody').empty();
        itemRowIndex = 0;

        // Add 1 default empty item row
        addProductRow();
        updateSummaryTotals();
    });

    // -------------------------------------------------------------
    // 6. Save Proposal (Add / Edit)
    // -------------------------------------------------------------
    $('#proposalForm').on('submit', function (e) {
        e.preventDefault();

        // Ensure customer name is filled
        if (!$('#customer_name').val() && $('#customer_select').val()) {
            $('#customer_name').val($('#customer_select').val());
        }

        if (!$('#customer_name').val()) {
            alert('Please select or enter a Customer Name.');
            $('#customer_select').focus();
            return;
        }

        // Validate items length
        if ($('#proposalItemsTbody tr.item-row').length === 0) {
            alert('Please add at least one product package.');
            return;
        }

        const proposalId = $('#proposal_id').val();
        const url = proposalId ? appUrl(`/admin/proposals/update/${proposalId}`) : appUrl('/admin/proposals/store');

        const $btn = $('#saveProposalBtn');
        const origText = $('#saveProposalBtnText').text();
        $btn.prop('disabled', true);
        $('#saveProposalBtnText').html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Saving...');

        $.ajax({
            url: url,
            type: 'POST',
            data: $(this).serialize(),
            success: function (res) {
                $btn.prop('disabled', false);
                $('#saveProposalBtnText').text(origText);

                if (res.status) {
                    $('#proposalModal').modal('hide');
                    showAlert('success', res.message || 'Proposal saved successfully.');
                    if (proposalTable) {
                        proposalTable.ajax.reload();
                    }
                } else {
                    showAlert('danger', res.message || 'Failed to save proposal.');
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false);
                $('#saveProposalBtnText').text(origText);

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errs = Object.values(xhr.responseJSON.errors).map(e => e.join('<br>')).join('<br>');
                    showAlert('danger', errs);
                } else {
                    const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Server error occurred.';
                    showAlert('danger', msg);
                }
            }
        });
    });

    // -------------------------------------------------------------
    // 7. Edit Proposal
    // -------------------------------------------------------------
    $(document).on('click', '.edit-proposal-btn', function () {
        const id = $(this).data('id');

        $.ajax({
            url: appUrl(`/admin/proposals/edit/${id}`),
            type: 'GET',
            success: function (res) {
                if (!res.status || !res.data) {
                    showAlert('danger', 'Unable to fetch proposal details.');
                    return;
                }

                const p = res.data;
                $('#proposalForm')[0].reset();
                $('#proposal_id').val(p.proposal_id);
                $('#customer_id').val(p.customer_id || '');
                $('#customer_name').val(p.customer_name || '');
                $('#customer_mobile').val(p.customer_mobile || '');
                $('#lead_requirement_id').val(p.lead_requirement_id || '');
                $('#sales_manager_id').val(p.sales_manager_id || '');
                $('#sales_manager_name').val(p.sales_manager_name || '');
                $('#sales_manager_mobile').val(p.sales_manager_mobile || '');
                $('#notes').val(p.notes || '');

                // Set Select2 values
                if ($.fn.select2) {
                    if (p.customer_id && $(`#customer_select option[value="${p.customer_id}"]`).length > 0) {
                        $('#customer_select').val(p.customer_id).trigger('change');
                    } else if (p.customer_name) {
                        // Custom customer name tag
                        if ($(`#customer_select option[value="${p.customer_name}"]`).length === 0) {
                            const newOption = new Option(p.customer_name + ' (Custom)', p.customer_name, true, true);
                            $('#customer_select').append(newOption).trigger('change');
                        } else {
                            $('#customer_select').val(p.customer_name).trigger('change');
                        }
                    } else {
                        $('#customer_select').val('').trigger('change');
                    }

                    // Resolve Sales Manager Selection
                    let targetSmId = p.sales_manager_id ? String(p.sales_manager_id) : '';
                    if (!targetSmId && p.sales_manager_name) {
                        $('#sales_manager_id option').each(function () {
                            const optName = $(this).data('name') || '';
                            const optText = $(this).text().trim();
                            if (
                                (optName && optName.toLowerCase() === p.sales_manager_name.toLowerCase()) ||
                                (optText && optText.toLowerCase().includes(p.sales_manager_name.toLowerCase()))
                            ) {
                                targetSmId = $(this).val();
                                return false;
                            }
                        });
                    }

                    if (targetSmId && $(`#sales_manager_id option[value="${targetSmId}"]`).length > 0) {
                        $('#sales_manager_id').val(targetSmId).trigger('change');
                    } else if (p.sales_manager_name) {
                        const smLabel = p.sales_manager_name + (p.sales_manager_mobile ? ` (${p.sales_manager_mobile})` : '');
                        const optVal = p.sales_manager_id || p.sales_manager_name;
                        const newOption = new Option(smLabel, optVal, true, true);
                        $(newOption).attr('data-id', p.sales_manager_id || '');
                        $(newOption).attr('data-name', p.sales_manager_name);
                        $(newOption).attr('data-mobile', p.sales_manager_mobile || '');
                        $('#sales_manager_id').append(newOption).trigger('change');
                    } else {
                        $('#sales_manager_id').val('').trigger('change');
                    }
                } else {
                    $('#sales_manager_id').val(p.sales_manager_id || '');
                }

                // Explicitly re-affirm customer & sales manager fields
                if (p.customer_name) {
                    $('#customer_name').val(p.customer_name);
                }
                if (p.customer_mobile) {
                    $('#customer_mobile').val(p.customer_mobile);
                }
                if (p.sales_manager_name) {
                    $('#sales_manager_name').val(p.sales_manager_name);
                }
                if (p.sales_manager_mobile) {
                    $('#sales_manager_mobile').val(p.sales_manager_mobile);
                }

                $('#proposalModalTitle').text(`Edit Proposal (${p.proposal_number})`);
                $('#proposalModalSubtitle').text('Update proposal information, product packages, and pricing');
                $('#saveProposalBtnText').text('Update Proposal');

                // Populate product items
                $('#proposalItemsTbody').empty();
                itemRowIndex = 0;
                if (p.items && p.items.length > 0) {
                    p.items.forEach(item => {
                        addProductRow(item);
                    });
                } else {
                    addProductRow();
                }

                updateSummaryTotals();
                $('#proposalModal').modal('show');
            },
            error: function () {
                showAlert('danger', 'Error retrieving proposal for editing.');
            }
        });
    });

    // -------------------------------------------------------------
    // 8. View Proposal Details Modal
    // -------------------------------------------------------------
    $(document).on('click', '.view-proposal-btn', function () {
        const id = $(this).data('id');

        $.ajax({
            url: appUrl(`/admin/proposals/show/${id}`),
            type: 'GET',
            success: function (res) {
                if (!res.status || !res.data) {
                    showAlert('danger', 'Proposal not found.');
                    return;
                }

                const p = res.data;
                $('#view_proposal_number').text(p.proposal_number);
                $('#view_created_at').text('Created: ' + (p.created_at ? new Date(p.created_at).toLocaleString('en-GB') : '-'));
                $('#printProposalModalBtn').attr('href', appUrl(`/admin/proposals/print/${p.proposal_id}`));

                // Customer info
                $('#view_customer_name').text(p.customer_name || '-');
                $('#view_customer_mobile').text(p.customer_mobile || '-');
                $('#view_lead_requirement').text(p.lead_requirement_name || 'Not specified');

                // Sales manager info
                $('#view_sales_manager_name').text(p.sales_manager_name || 'Unassigned');
                $('#view_sales_manager_mobile').text(p.sales_manager_mobile || 'N/A');
                $('#view_proposal_date').text(p.created_at ? new Date(p.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '-');

                // Items
                let itemsHtml = '';
                if (p.items && p.items.length > 0) {
                    p.items.forEach((item, idx) => {
                        const price = '₹' + parseFloat(item.price || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        const taxAmt = '₹' + parseFloat(item.tax_amount || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        const lineAmt = '₹' + parseFloat(item.amount || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                        itemsHtml += `
                            <tr>
                                <td class="text-center">${idx + 1}</td>
                                <td><strong>${item.product_package}</strong></td>
                                <td class="text-end">${price}</td>
                                <td class="text-center">${parseFloat(item.tax_percentage || 0)}%</td>
                                <td class="text-end text-muted">${taxAmt}</td>
                                <td class="text-end fw-bold text-dark">${lineAmt}</td>
                            </tr>
                        `;
                    });
                } else {
                    itemsHtml = `<tr><td colspan="6" class="text-center text-muted py-2">No product items found.</td></tr>`;
                }

                $('#view_items_tbody').html(itemsHtml);
                $('#view_subtotal').text('₹' + parseFloat(p.subtotal || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $('#view_total_tax').text('₹' + parseFloat(p.total_tax || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $('#view_total_amount').text('₹' + parseFloat(p.total_amount || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

                // Notes
                if (p.notes && p.notes.trim() !== '') {
                    $('#view_notes').text(p.notes);
                    $('#view_notes_container').removeClass('d-none');
                } else {
                    $('#view_notes_container').addClass('d-none');
                }

                $('#viewProposalModal').modal('show');
            },
            error: function () {
                showAlert('danger', 'Error loading proposal details.');
            }
        });
    });

    // -------------------------------------------------------------
    // 9. Delete Proposal
    // -------------------------------------------------------------
    let deleteTargetId = null;

    $(document).on('click', '.delete-proposal-btn', function () {
        deleteTargetId = $(this).data('id');
        const num = $(this).data('number');
        $('#delete_proposal_number').text(num);
        $('#deleteProposalModal').modal('show');
    });

    $('#confirmDeleteProposalBtn').on('click', function () {
        if (!deleteTargetId) return;

        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Deleting...');

        $.ajax({
            url: appUrl(`/admin/proposals/delete/${deleteTargetId}`),
            type: 'DELETE',
            success: function (res) {
                $btn.prop('disabled', false).html('<i class="bx bx-trash me-1"></i> Delete');
                $('#deleteProposalModal').modal('hide');

                if (res.status) {
                    showAlert('success', res.message || 'Proposal deleted successfully.');
                    if (proposalTable) {
                        proposalTable.ajax.reload();
                    }
                } else {
                    showAlert('danger', res.message || 'Failed to delete proposal.');
                }
            },
            error: function () {
                $btn.prop('disabled', false).html('<i class="bx bx-trash me-1"></i> Delete');
                showAlert('danger', 'Error deleting proposal.');
            }
        });
    });
});
