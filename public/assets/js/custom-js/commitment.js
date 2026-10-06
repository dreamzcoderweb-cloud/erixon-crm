/**
 * Commitment Management JavaScript
 * - DataTable with full Export buttons (ColVis, Copy, CSV, Excel, PDF, Print)
 * - Datepicker filter & Staff dropdown filter
 * - Add / Edit / Delete modals
 */

$(document).ready(function () {
    if ($('#commitments-table').length === 0) {
        return;
    }

    let commitmentTable = null;

    // CSRF Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Helper to resolve URLs with base application path
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
        const todayStr = new Date().toISOString().split('T')[0];
        let total = data.length;
        let todayCount = 0;
        let staffSet = new Set();

        data.forEach(item => {
            if (item.date && item.date.startsWith(todayStr)) {
                todayCount++;
            }
            if (item.user_id) {
                staffSet.add(item.user_id);
            }
        });

        $('#kpi_total').text(total);
        $('#kpi_today').text(todayCount);
        $('#kpi_staff_count').text(staffSet.size);
    }

    // DataTable initialization
    commitmentTable = $('#commitments-table').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: appUrl('/admin/commitments/data'),
            data: function (d) {
                const period = $('#filter_period').val() || 'all';
                d.filter_type = period;
                if (period === 'daily') {
                    d.date = $('#filter_date').val();
                } else if (period === 'monthly') {
                    d.month = $('#filter_month').val();
                } else if (period === 'weekly') {
                    d.start_date = $('#filter_start_date').val();
                } else if (period === 'custom') {
                    d.start_date = $('#filter_start_date').val();
                    d.end_date = $('#filter_end_date').val();
                }
                d.staff_id = $('#filter_staff_id').val();
            },
            dataSrc: function (json) {
                const list = json.data || [];
                updateKpis(list);
                return list;
            },
            error: function (xhr, error, thrown) {
                console.error('Commitments DataTable AJAX error:', xhr, error, thrown);
            }
        },
        columns: [
            {
                data: null,
                className: 'text-center',
                orderable: false,
                searchable: false,
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
                    return `<div style="white-space: pre-wrap; max-width: 450px;">${data}</div>`;
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
                            <button type="button" class="btn btn-icon btn-sm btn-outline-primary edit-commitment-btn" data-id="${row.commitment_id}" title="Edit">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-sm btn-outline-danger delete-commitment-btn" data-id="${row.commitment_id}" title="Delete">
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
        ordering: false
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

        if (commitmentTable) {
            commitmentTable.ajax.reload();
        }
    });

    // Filter Form Submit
    $('#commitmentFilterForm').on('submit', function (e) {
        e.preventDefault();
        if (commitmentTable) {
            commitmentTable.ajax.reload();
        }
    });

    // Reset Filters
    $('#filterResetBtn').on('click', function () {
        $('#commitmentFilterForm')[0].reset();
        $('.btn-period').removeClass('active');
        $('.btn-period[data-period="all"]').addClass('active');
        $('#filter_period').val('all');
        $('#filter_staff_id').val('');
        $('.filter-date-group').addClass('d-none');
        if (commitmentTable) {
            commitmentTable.ajax.reload();
        }
    });

    // Open Add Modal
    $('#openAddCommitmentModalBtn').on('click', function () {
        $('#commitmentForm')[0].reset();
        $('#commitment_id').val('');
        $('#modal_date').val(new Date().toISOString().split('T')[0]);
        $('#commitmentModalTitle').text('Add Commitment');
        $('#commitmentModalSubtitle').text('Record staff daily target, pledge, or commitment');
        $('#saveCommitmentBtnText').text('Save Commitment');
        $('#commitmentModal').modal('show');
    });

    // Save Form (Add / Edit)
    $('#commitmentForm').on('submit', function (e) {
        e.preventDefault();

        const id = $('#commitment_id').val();
        const url = id ? appUrl(`/admin/commitments/update/${id}`) : appUrl('/admin/commitments/store');

        const $btn = $('#saveCommitmentBtn');
        const origText = $('#saveCommitmentBtnText').text();
        $btn.prop('disabled', true);
        $('#saveCommitmentBtnText').html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Saving...');

        $.ajax({
            url: url,
            type: 'POST',
            data: $(this).serialize(),
            success: function (res) {
                $btn.prop('disabled', false);
                $('#saveCommitmentBtnText').text(origText);

                if (res.status) {
                    $('#commitmentModal').modal('hide');
                    showAlert('success', res.message || 'Commitment saved successfully.');
                    if (commitmentTable) {
                        commitmentTable.ajax.reload();
                    }
                } else {
                    showAlert('danger', res.message || 'Failed to save commitment.');
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false);
                $('#saveCommitmentBtnText').text(origText);
                const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Server error occurred.';
                showAlert('danger', msg);
            }
        });
    });

    // Edit Modal
    $(document).on('click', '.edit-commitment-btn', function () {
        const id = $(this).data('id');

        $.ajax({
            url: appUrl(`/admin/commitments/edit/${id}`),
            type: 'GET',
            success: function (res) {
                if (!res.status || !res.data) {
                    showAlert('danger', 'Unable to fetch commitment details.');
                    return;
                }

                const d = res.data;
                $('#commitment_id').val(d.commitment_id);
                $('#modal_staff_id').val(d.user_id);
                $('#modal_date').val(d.date ? d.date.split('T')[0] : '');
                $('#modal_notes').val(d.notes || '');

                $('#commitmentModalTitle').text('Edit Commitment');
                $('#commitmentModalSubtitle').text('Update staff commitment details');
                $('#saveCommitmentBtnText').text('Update Commitment');
                $('#commitmentModal').modal('show');
            },
            error: function () {
                showAlert('danger', 'Error retrieving record.');
            }
        });
    });

    // Delete Modal
    let deleteTargetId = null;

    $(document).on('click', '.delete-commitment-btn', function () {
        deleteTargetId = $(this).data('id');
        $('#deleteCommitmentModal').modal('show');
    });

    $('#confirmDeleteCommitmentBtn').on('click', function () {
        if (!deleteTargetId) return;

        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Deleting...');

        $.ajax({
            url: appUrl(`/admin/commitments/delete/${deleteTargetId}`),
            type: 'DELETE',
            success: function (res) {
                $btn.prop('disabled', false).html('<i class="bx bx-trash me-1"></i> Delete');
                $('#deleteCommitmentModal').modal('hide');

                if (res.status) {
                    showAlert('success', res.message || 'Commitment deleted successfully.');
                    if (commitmentTable) {
                        commitmentTable.ajax.reload();
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
