/**
 * Pending Work Management JavaScript
 * - DataTable with full Export buttons (ColVis, Copy, CSV, Excel, PDF, Print)
 * - Datepicker filter & Staff dropdown filter
 * - Quick status toggle (0: Pending, 1: Process, 2: Finished)
 * - Add / Edit / Delete modals
 */

$(document).ready(function () {
    if ($('#pending-works-table').length === 0) {
        return;
    }

    let pendingWorkTable = null;

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

    function updateKpis(data) {
        let total = data.length;
        let pending = 0;
        let process = 0;
        let finished = 0;

        data.forEach(item => {
            const st = parseInt(item.status);
            if (st === 0) pending++;
            else if (st === 1) process++;
            else if (st === 2) finished++;
        });

        $('#kpi_total').text(total);
        $('#kpi_pending').text(pending);
        $('#kpi_process').text(process);
        $('#kpi_finished').text(finished);
    }

    // DataTable initialization
    pendingWorkTable = $('#pending-works-table').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: appUrl('/admin/pending-works/data'),
            data: function (d) {
                d.filter_type = $('#filter_period').val();
                d.date = $('#filter_date').val();
                d.month = $('#filter_month').val();
                d.start_date = $('#filter_start_date').val();
                d.end_date = $('#filter_end_date').val();
                d.staff_id = $('#filter_staff_id').val();
                d.status = $('#filter_status').val();
            },
            dataSrc: function (json) {
                const list = json.data || [];
                updateKpis(list);
                return list;
            },
            error: function (xhr, error, thrown) {
                console.error('Pending Works DataTable AJAX error:', xhr, error, thrown);
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
                    if (!data) return '<span class="text-muted">Unassigned</span>';
                    if (type !== 'display') return data.name || '';
                    return `
                        <div class="fw-bold text-dark">${data.name || '-'}</div>
                        <div class="text-muted small">${data.mobile_number || data.email || ''}</div>
                    `;
                }
            },
            {
                data: 'date',
                render: function (data, type) {
                    if (!data) return '-';
                    if (type !== 'display') return data;
                    return `<strong>${new Date(data).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</strong>`;
                }
            },
            {
                data: 'notes',
                render: function (data, type) {
                    if (!data) return '<span class="text-muted">-</span>';
                    if (type !== 'display') return data;
                    return `<div style="white-space: pre-wrap; max-width: 400px;">${data}</div>`;
                }
            },
            {
                data: 'status',
                className: 'text-center',
                render: function (data, type, row) {
                    const st = parseInt(data);
                    if (type !== 'display') {
                        return st === 1 ? 'In Process' : (st === 2 ? 'Finished' : 'Pending');
                    }

                    // Render interactive status changer dropdown
                    let badgeClass = 'bg-label-warning';
                    let label = 'Pending';
                    if (st === 1) {
                        badgeClass = 'bg-label-info';
                        label = 'In Process';
                    } else if (st === 2) {
                        badgeClass = 'bg-label-success';
                        label = 'Finished';
                    }

                    return `
                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn btn-sm ${badgeClass} dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                ${label}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li><a class="dropdown-item btn-change-status ${st === 0 ? 'active' : ''}" href="javascript:void(0);" data-id="${row.pending_id}" data-status="0"><span class="badge bg-label-warning me-1">0</span> Pending</a></li>
                                <li><a class="dropdown-item btn-change-status ${st === 1 ? 'active' : ''}" href="javascript:void(0);" data-id="${row.pending_id}" data-status="1"><span class="badge bg-label-info me-1">1</span> In Process</a></li>
                                <li><a class="dropdown-item btn-change-status ${st === 2 ? 'active' : ''}" href="javascript:void(0);" data-id="${row.pending_id}" data-status="2"><span class="badge bg-label-success me-1">2</span> Finished</a></li>
                            </ul>
                        </div>
                    `;
                }
            },
            {
                data: 'created_at',
                render: function (data, type) {
                    if (!data) return '-';
                    if (type !== 'display') return data;
                    return `<span class="text-muted fs-tiny">${new Date(data).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</span>`;
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
                            <button type="button" class="btn btn-icon btn-sm btn-outline-primary edit-pending-work-btn" data-id="${row.pending_id}" title="Edit">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-sm btn-outline-danger delete-pending-work-btn" data-id="${row.pending_id}" title="Delete">
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
                            text: '<i class="bx bx-columns me-1"></i> Columns',
                            className: 'btn btn-secondary btn-sm me-1',
                            columns: ':not(:first-child):not(:last-child)'
                        },
                        {
                            extend: 'copyHtml5',
                            text: '<i class="bx bx-copy me-1"></i> Copy',
                            className: 'btn btn-secondary btn-sm me-1',
                            exportOptions: { columns: ':visible:not(:last-child)' }
                        },
                        {
                            extend: 'csvHtml5',
                            text: '<i class="bx bx-file me-1"></i> CSV',
                            className: 'btn btn-secondary btn-sm me-1',
                            exportOptions: { columns: ':visible:not(:last-child)' }
                        },
                        {
                            extend: 'excelHtml5',
                            text: '<i class="bx bx-spreadsheet me-1"></i> Excel',
                            className: 'btn btn-secondary btn-sm me-1',
                            exportOptions: { columns: ':visible:not(:last-child)' }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="bx bxs-file-pdf me-1"></i> PDF',
                            className: 'btn btn-secondary btn-sm me-1',
                            exportOptions: { columns: ':visible:not(:last-child)' }
                        },
                        {
                            extend: 'print',
                            text: '<i class="bx bx-printer me-1"></i> Print',
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
        order: [[2, 'desc']]
    });

    // Period Toggle Buttons (Customer Module Pattern)
    $('.btn-period').on('click', function () {
        $('.btn-period').removeClass('active');
        $(this).addClass('active');

        let period = $(this).data('period');
        $('#filter_period').val(period);
        $('.filter-date-group').addClass('d-none');

        if (period === 'daily') {
            $('#group_daily').removeClass('d-none');
        } else if (period === 'weekly') {
            $('#group_custom_start').removeClass('d-none');
        } else if (period === 'monthly') {
            $('#group_monthly').removeClass('d-none');
        } else if (period === 'custom') {
            $('#group_custom_start').removeClass('d-none');
            $('#group_custom_end').removeClass('d-none');
        }

        if (pendingWorkTable) {
            pendingWorkTable.ajax.reload();
        }
    });

    // Filter Form Submit
    $('#pendingWorkFilterForm').on('submit', function (e) {
        e.preventDefault();
        if (pendingWorkTable) {
            pendingWorkTable.ajax.reload();
        }
    });

    // Reset Filters
    $('#filterResetBtn').on('click', function () {
        $('#pendingWorkFilterForm')[0].reset();
        $('.btn-period').removeClass('active');
        $('.btn-period[data-period="all"]').addClass('active');
        $('#filter_period').val('all');
        $('#filter_staff_id').val('');
        $('#filter_status').val('all');
        $('.filter-date-group').addClass('d-none');
        if (pendingWorkTable) {
            pendingWorkTable.ajax.reload();
        }
    });

    // Quick Status Changer
    $(document).on('click', '.btn-change-status', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        const status = $(this).data('status');

        $.ajax({
            url: appUrl(`/admin/pending-works/change-status/${id}`),
            type: 'POST',
            data: { status: status },
            success: function (res) {
                if (res.status) {
                    showAlert('success', res.message || 'Status updated successfully.');
                    if (pendingWorkTable) {
                        pendingWorkTable.ajax.reload(null, false);
                    }
                } else {
                    showAlert('danger', res.message || 'Failed to update status.');
                }
            },
            error: function () {
                showAlert('danger', 'Error updating status.');
            }
        });
    });

    // Open Add Modal
    $('#openAddPendingWorkModalBtn').on('click', function () {
        $('#pendingWorkForm')[0].reset();
        $('#pending_work_id').val('');
        $('#modal_date').val(new Date().toISOString().split('T')[0]);
        $('#modal_status').val('0');
        $('#pendingWorkModalTitle').text('Add Pending Work');
        $('#pendingWorkModalSubtitle').text('Create staff pending work record');
        $('#savePendingWorkBtnText').text('Save Pending Work');
        $('#pendingWorkModal').modal('show');
    });

    // Save Form (Add / Edit)
    $('#pendingWorkForm').on('submit', function (e) {
        e.preventDefault();

        const id = $('#pending_work_id').val();
        const url = id ? appUrl(`/admin/pending-works/update/${id}`) : appUrl('/admin/pending-works/store');

        const $btn = $('#savePendingWorkBtn');
        const origText = $('#savePendingWorkBtnText').text();
        $btn.prop('disabled', true);
        $('#savePendingWorkBtnText').html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Saving...');

        $.ajax({
            url: url,
            type: 'POST',
            data: $(this).serialize(),
            success: function (res) {
                $btn.prop('disabled', false);
                $('#savePendingWorkBtnText').text(origText);

                if (res.status) {
                    $('#pendingWorkModal').modal('hide');
                    showAlert('success', res.message || 'Pending work saved successfully.');
                    if (pendingWorkTable) {
                        pendingWorkTable.ajax.reload();
                    }
                } else {
                    showAlert('danger', res.message || 'Failed to save pending work.');
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false);
                $('#savePendingWorkBtnText').text(origText);
                const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Server error occurred.';
                showAlert('danger', msg);
            }
        });
    });

    // Edit Modal
    $(document).on('click', '.edit-pending-work-btn', function () {
        const id = $(this).data('id');

        $.ajax({
            url: appUrl(`/admin/pending-works/edit/${id}`),
            type: 'GET',
            success: function (res) {
                if (!res.status || !res.data) {
                    showAlert('danger', 'Unable to fetch pending work details.');
                    return;
                }

                const d = res.data;
                $('#pending_work_id').val(d.pending_id);
                $('#modal_staff_id').val(d.user_id);
                $('#modal_date').val(d.date ? d.date.split('T')[0] : '');
                $('#modal_status').val(d.status);
                $('#modal_notes').val(d.notes || '');

                $('#pendingWorkModalTitle').text('Edit Pending Work');
                $('#pendingWorkModalSubtitle').text('Update staff pending work details');
                $('#savePendingWorkBtnText').text('Update Pending Work');
                $('#pendingWorkModal').modal('show');
            },
            error: function () {
                showAlert('danger', 'Error retrieving record.');
            }
        });
    });

    // Delete Modal
    let deleteTargetId = null;

    $(document).on('click', '.delete-pending-work-btn', function () {
        deleteTargetId = $(this).data('id');
        $('#deletePendingWorkModal').modal('show');
    });

    $('#confirmDeletePendingWorkBtn').on('click', function () {
        if (!deleteTargetId) return;

        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Deleting...');

        $.ajax({
            url: appUrl(`/admin/pending-works/delete/${deleteTargetId}`),
            type: 'DELETE',
            success: function (res) {
                $btn.prop('disabled', false).html('<i class="bx bx-trash me-1"></i> Delete');
                $('#deletePendingWorkModal').modal('hide');

                if (res.status) {
                    showAlert('success', res.message || 'Pending work deleted successfully.');
                    if (pendingWorkTable) {
                        pendingWorkTable.ajax.reload();
                    }
                } else {
                    showAlert('danger', res.message || 'Failed to delete record.');
                }
            },
            error: function () {
                $btn.prop('disabled', false).html('<i class="bx bx-trash me-1"></i> Delete');
                showAlert('danger', 'Error deleting record.');
            }
        });
    });
});
