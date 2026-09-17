$(document).ready(function () {
    if (!$('#audit-logs-table').length) {
        return;
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let auditTable = null;

    function formatDateTime(dtStr) {
        if (!dtStr) return 'N/A';
        let d = new Date(dtStr);
        if (isNaN(d.getTime())) return dtStr;

        let day = String(d.getDate()).padStart(2, '0');
        let monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        let month = monthNames[d.getMonth()];
        let year = d.getFullYear();

        let hours = d.getHours();
        let minutes = String(d.getMinutes()).padStart(2, '0');
        let ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12;
        let formattedHours = String(hours).padStart(2, '0');

        return `${day} ${month} ${year}, ${formattedHours}:${minutes} ${ampm}`;
    }

    function getEventBadge(event) {
        let ev = (event || '').toLowerCase();
        let badgeClass = 'badge-event-default';
        let icon = 'bx-circle';

        if (ev === 'created') {
            badgeClass = 'badge-event-created';
            icon = 'bx-plus-circle';
        } else if (ev === 'updated') {
            badgeClass = 'badge-event-updated';
            icon = 'bx-edit';
        } else if (ev === 'deleted') {
            badgeClass = 'badge-event-deleted';
            icon = 'bx-trash';
        } else if (ev === 'login') {
            badgeClass = 'badge-event-login';
            icon = 'bx-log-in-circle';
        } else if (ev === 'logout') {
            badgeClass = 'badge-event-logout';
            icon = 'bx-log-out-circle';
        }

        return `<span class="badge ${badgeClass} text-uppercase px-2 py-1"><i class="bx ${icon} me-1 fs-tiny"></i>${event}</span>`;
    }

    // Initialize DataTable
    auditTable = $('#audit-logs-table').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: APP_URL + '/admin/audit-logs/data',
            data: function (d) {
                d.filter_type = $('#audit_filter_period').val();
                d.start_date = $('#audit_filter_start_date').val();
                d.end_date = $('#audit_filter_end_date').val();
                d.user_id = $('#audit_filter_user').val();
                d.module = $('#audit_filter_module').val();
                d.event = $('#audit_filter_event').val();
                d.search = $('#audit_filter_search').val();
            },
            dataSrc: 'data'
        },
        order: [[0, 'desc']],
        columns: [
            {
                data: 'created_at',
                render: function (data, type) {
                    let formatted = formatDateTime(data);
                    if (type !== 'display') return formatted;
                    return `<span class="fw-semibold text-secondary small">${formatted}</span>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    let userName = row.user ? row.user.name : 'System / Auto';
                    let userEmail = row.user ? row.user.email : '';
                    if (type !== 'display') {
                        return userName + (userEmail ? ` (${userEmail})` : '');
                    }
                    return `<div class="d-flex align-items-center"><div><span class="fw-bold text-dark">${userName}</span>${userEmail ? `<small class="text-muted d-block">${userEmail}</small>` : ''}</div></div>`;
                }
            },
            {
                data: 'event',
                render: function (data, type) {
                    if (type !== 'display') return (data || '').toUpperCase();
                    return getEventBadge(data);
                }
            },
            {
                data: 'module',
                render: function (data, type) {
                    if (type !== 'display') return data || 'General';
                    return `<span class="badge bg-label-dark fw-semibold">${data || 'General'}</span>`;
                }
            },
            {
                data: 'description',
                render: function (data, type) {
                    if (type !== 'display') return data || '';
                    if (!data) return '<span class="text-muted">No description</span>';
                    return `<span class="text-wrap" style="max-width: 300px; display: inline-block;">${data}</span>`;
                }
            },
            {
                data: 'ip_address',
                render: function (data, type) {
                    if (type !== 'display') return data || 'N/A';
                    if (!data) return '<span class="text-muted small">N/A</span>';
                    return `<code class="small text-muted">${data}</code>`;
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    return `<button type="button" class="btn btn-sm btn-icon btn-outline-primary btn-view-audit-log" data-id="${row.id}" title="View Details">
                                <i class="bx bx-show"></i>
                            </button>`;
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
                            columns: ':not(:last-child)'
                        },
                        {
                            extend: 'copy',
                            className: 'btn btn-secondary btn-sm me-1',
                            exportOptions: {
                                columns: ':visible:not(:last-child)'
                            }
                        },
                        {
                            extend: 'csv',
                            className: 'btn btn-secondary btn-sm me-1',
                            exportOptions: {
                                columns: ':visible:not(:last-child)'
                            }
                        },
                        {
                            extend: 'excel',
                            className: 'btn btn-secondary btn-sm me-1',
                            exportOptions: {
                                columns: ':visible:not(:last-child)'
                            }
                        },
                        {
                            extend: 'pdf',
                            className: 'btn btn-secondary btn-sm me-1',
                            exportOptions: {
                                columns: ':visible:not(:last-child)'
                            }
                        },
                        {
                            extend: 'print',
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
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        pageLength: 10,
        responsive: true,
        language: {
            emptyTable: "No audit logs found matching your criteria",
            processing: "<div class='py-3 text-center'><span class='spinner-border spinner-border-sm text-primary me-2'></span>Loading audit logs...</div>"
        }
    });

    // Handle Period Button Filter
    $('.btn-audit-period').on('click', function () {
        $('.btn-audit-period').removeClass('active');
        $(this).addClass('active');

        let period = $(this).data('period');
        $('#audit_filter_period').val(period);

        if (period === 'custom') {
            $('#audit_group_custom_start, #audit_group_custom_end').removeClass('d-none');
        } else {
            $('#audit_group_custom_start, #audit_group_custom_end').addClass('d-none');
        }

        auditTable.ajax.reload();
    });

    // Custom date changes
    $('#audit_filter_start_date, #audit_filter_end_date').on('change', function () {
        if ($('#audit_filter_period').val() === 'custom') {
            auditTable.ajax.reload();
        }
    });

    // Select filters (User, Module, Event)
    $('#audit_filter_user, #audit_filter_module, #audit_filter_event').on('change', function () {
        auditTable.ajax.reload();
    });

    // Search with debounce
    let searchTimer = null;
    $('#audit_filter_search').on('keyup input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
            auditTable.ajax.reload();
        }, 350);
    });

    // Reset Filters
    $('#btnResetAuditFilter').on('click', function () {
        $('#auditLogFilterForm')[0].reset();
        $('.btn-audit-period').removeClass('active');
        $('.btn-audit-period[data-period="all"]').addClass('active');
        $('#audit_filter_period').val('all');
        $('#audit_group_custom_start, #audit_group_custom_end').addClass('d-none');
        auditTable.ajax.reload();
    });

    // Refresh button
    $('#btnRefreshAuditLogs').on('click', function () {
        let $btn = $(this);
        $btn.find('i').addClass('bx-spin');
        auditTable.ajax.reload(function () {
            $btn.find('i').removeClass('bx-spin');
        });
    });

    // Export CSV (Top Button triggers DataTables CSV export)
    $('#btnExportAuditLog').on('click', function () {
        if (auditTable && $('.buttons-csv').length) {
            $('.buttons-csv').click();
        } else {
            let params = new URLSearchParams({
                filter_type: $('#audit_filter_period').val(),
                start_date: $('#audit_filter_start_date').val(),
                end_date: $('#audit_filter_end_date').val(),
                user_id: $('#audit_filter_user').val(),
                module: $('#audit_filter_module').val(),
                event: $('#audit_filter_event').val(),
                search: $('#audit_filter_search').val(),
            });
            window.location.href = APP_URL + '/admin/audit-logs/export?' + params.toString();
        }
    });

    // View Details Modal
    $(document).on('click', '.btn-view-audit-log', function () {
        let logId = $(this).data('id');
        let modalEl = document.getElementById('auditLogDetailModal');
        let modal = bootstrap.Modal.getOrCreateInstance(modalEl);

        $('#modalLogIdBadge').text('#' + logId);
        $('#modalDetailUser').text('Loading...');
        $('#modalDetailActionBadge').html('');
        $('#modalDetailModule').text('');
        $('#modalDetailDate').text('');
        $('#modalDetailIp').text('');
        $('#modalDetailUserAgent').text('');
        $('#modalDetailDesc').text('');
        $('#modalDetailUrl').text('');
        $('#modalChangesCountBadge').text('');
        $('#modalDiffContainer').html('<div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-2"></span>Loading details...</div>');

        modal.show();

        $.ajax({
            url: APP_URL + '/admin/audit-logs/show/' + logId,
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (!res.status || !res.data) {
                    $('#modalDiffContainer').html('<div class="alert alert-danger mb-0">Failed to load audit log details.</div>');
                    return;
                }

                let log = res.data.log;
                let diff = res.data.diff || [];

                let actorName = log.user ? log.user.name + (log.user.email ? ` (${log.user.email})` : '') : 'System / Automated';
                $('#modalDetailUser').text(actorName);
                $('#modalDetailActionBadge').html(getEventBadge(log.event));
                $('#modalDetailModule').text(log.module || 'General');
                $('#modalDetailDate').text(formatDateTime(log.created_at));
                $('#modalDetailIp').text(log.ip_address || 'N/A');
                $('#modalDetailUserAgent').text(log.user_agent || 'N/A');
                $('#modalDetailDesc').text(log.description || 'No description provided.');

                if (log.url) {
                    $('#modalDetailUrlRow').show();
                    $('#modalDetailUrl').text(log.url);
                } else {
                    $('#modalDetailUrlRow').hide();
                }

                if (diff.length > 0) {
                    $('#modalChangesCountBadge').text(diff.length + ' field(s)');
                    let tableHtml = `
                        <div class="table-responsive border rounded">
                            <table class="table table-bordered table-sm mb-0 diff-table">
                                <thead>
                                    <tr>
                                        <th style="width: 25%;">Field Name</th>
                                        <th style="width: 37.5%;">Old Value (Before)</th>
                                        <th style="width: 37.5%;">New Value (After)</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    diff.forEach(function (item) {
                        let fieldLabel = item.field.replace(/_/g, ' ').toUpperCase();
                        let oldText = item.old !== null && item.old !== undefined ? item.old : '<span class="text-muted fst-italic">null</span>';
                        let newText = item.new !== null && item.new !== undefined ? item.new : '<span class="text-muted fst-italic">null</span>';

                        tableHtml += `
                            <tr>
                                <td class="fw-semibold text-dark small bg-light">${fieldLabel}</td>
                                <td class="diff-val-old">${oldText}</td>
                                <td class="diff-val-new">${newText}</td>
                            </tr>
                        `;
                    });

                    tableHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;

                    $('#modalDiffContainer').html(tableHtml);
                } else {
                    $('#modalChangesCountBadge').text('0');
                    $('#modalDiffContainer').html(`
                        <div class="alert alert-secondary d-flex align-items-center mb-0" role="alert">
                            <i class="bx bx-info-circle me-2 fs-5"></i>
                            <div>No field-level attribute diff recorded for this event (e.g., authentication action or record without state modifications).</div>
                        </div>
                    `);
                }
            },
            error: function () {
                $('#modalDiffContainer').html('<div class="alert alert-danger mb-0">Error fetching audit log details.</div>');
            }
        });
    });
});
