$(document).ready(function () {
    if (!$('#attendance-report-table').length) return;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let reportTable = null;

    function initReportTable() {
        reportTable = $('#attendance-report-table').DataTable({
            ajax: {
                url: APP_URL + '/admin/attendance/report/data',
                type: 'GET',
                data: function (d) {
                    d.filter_type = $('#filter_type').val();
                    d.user_id     = $('#filter_user_id').val();
                    d.date        = $('#filter_date').val();
                    d.month       = $('#filter_month').val();
                    d.start_date  = $('#filter_start_date').val();
                    d.end_date    = $('#filter_end_date').val();
                },
                dataSrc: function (json) {
                    if (json.summary) {
                        $('#kpi_present').text(json.summary.total_present || 0);
                        $('#kpi_late').text(json.summary.total_late || 0);
                        $('#kpi_half_day').text(json.summary.total_half_day || 0);
                        $('#kpi_absent').text(json.summary.total_absent || 0);
                        $('#kpi_on_leave').text(json.summary.total_on_leave || 0);
                        $('#kpi_total_hours').text(json.summary.total_working_hours || '0 hrs');
                        let deductionVal = json.summary.total_late_deduction || 0;
                        $('#kpi_late_deduction').text('₹' + parseFloat(deductionVal).toFixed(2));
                    }
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
                        let formatted = formatDate(data);
                        return `<span class="badge bg-label-dark"><i class="bx bx-calendar me-1"></i>${formatted}</span>`;
                    }
                },
                {
                    data: 'allowed_check_in_time',
                    render: function (data, type) {
                        if (!data) return '<span class="text-muted">09:10 AM</span>';
                        if (type !== 'display') return data;
                        return `<span class="badge bg-label-secondary">${data}</span>`;
                    }
                },
                {
                    data: 'session_1',
                    render: function (data, type, row) {
                        let text = data || (row.check_in ? row.check_in + (row.check_out ? ' → ' + row.check_out : '') : '-');
                        if (type !== 'display') return text;
                        let badge = row.status === 'Late' ? 'bg-label-warning' : 'bg-label-success';
                        return `<span class="badge ${badge}"><i class="bx bx-log-in me-1"></i>${text}</span>`;
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        if (!row.latitude || !row.longitude) {
                            return '<span class="text-muted"><i class="bx bx-map-pin me-1"></i>N/A</span>';
                        }
                        if (type !== 'display') return `${row.latitude}, ${row.longitude}`;
                        let mapsUrl = `https://maps.google.com/?q=${row.latitude},${row.longitude}`;
                        let html = `<a href="${mapsUrl}" target="_blank" class="badge bg-label-info text-decoration-none" title="View Check-In Location on Google Maps"><i class="bx bx-map-pin me-1"></i>${row.latitude}, ${row.longitude}</a>`;
                        if (row.second_check_in_latitude && row.second_check_in_longitude) {
                            let mapsUrl2 = `https://maps.google.com/?q=${row.second_check_in_latitude},${row.second_check_in_longitude}`;
                            html += `<br><a href="${mapsUrl2}" target="_blank" class="badge bg-label-primary mt-1 text-decoration-none" title="View Session 2 Location"><i class="bx bx-map-pin me-1"></i>S2: ${row.second_check_in_latitude}, ${row.second_check_in_longitude}</a>`;
                        }
                        return html;
                    }
                },
                {
                    data: 'actual_work_finished_time',
                    render: function (data, type) {
                        let text = data || '-';
                        if (type !== 'display') return text;
                        return text !== '-' ? `<span class="badge bg-label-secondary">${text}</span>` : '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'ot_minutes',
                    render: function (data, type) {
                        let minutes = parseInt(data || 0, 10);
                        if (type !== 'display') return minutes;
                        return `<span class="badge ${minutes > 0 ? 'bg-label-success' : 'bg-label-secondary'}"><i class="bx bx-time-five me-1"></i>${minutes} min</span>`;
                    }
                },
                {
                    data: 'ot_income',
                    render: function (data, type) {
                        let amount = parseFloat(data || 0);
                        if (type !== 'display') return amount;
                        return amount > 0 ? `<span class="badge bg-label-success">+₹${amount.toFixed(2)}</span>` : '<span class="text-muted">₹0.00</span>';
                    }
                },
                {
                    data: 'permission_period',
                    render: function (data, type) {
                        let text = data || '-';
                        if (type !== 'display') return text;
                        return text !== '-' ? `<span class="badge bg-label-info"><i class="bx bx-time me-1"></i>${text}</span>` : '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'permission_duration',
                    render: function (data, type) {
                        let text = data || '-';
                        if (type !== 'display') return text;
                        return text !== '-' ? `<span class="badge bg-label-primary"><i class="bx bx-timer me-1"></i>${text}</span>` : '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'session_2',
                    render: function (data, type) {
                        let text = data || '-';
                        if (type !== 'display') return text;
                        return text !== '-' ? `<span class="badge bg-label-success"><i class="bx bx-log-in-circle me-1"></i>${text}</span>` : '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'late_duration_minutes',
                    render: function (data, type) {
                        if (!data || data <= 0) return '<span class="text-muted">0 mins</span>';
                        if (type !== 'display') return data + ' mins';
                        return `<span class="badge bg-label-warning"><i class="bx bx-time me-1"></i>${data} mins</span>`;
                    }
                },
                {
                    data: 'late_count_status',
                    render: function (data, type) {
                        if (!data) return '<span class="text-muted">-</span>';
                        if (type !== 'display') return data;
                        return `<small class="fw-semibold text-dark">${data}</small>`;
                    }
                },
                {
                    data: 'is_allowed_count_exceeded',
                    render: function (data, type) {
                        if (type !== 'display') return data ? 'Yes' : 'No';
                        if (data) {
                            return `<span class="badge bg-label-danger"><i class="bx bx-error me-1"></i>Yes (Exceeded)</span>`;
                        }
                        return `<span class="badge bg-label-success"><i class="bx bx-check me-1"></i>No</span>`;
                    }
                },
                {
                    data: 'salary_deduction',
                    render: function (data, type) {
                        let amount = parseFloat(data || 0);
                        if (type !== 'display') return amount;
                        if (amount > 0) {
                            return `<span class="badge bg-danger text-white"><i class="bx bx-minus-circle me-1"></i>₹${amount.toFixed(2)}</span>`;
                        }
                        return `<span class="text-muted">₹0.00</span>`;
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
    }

    initReportTable();

    // Toggle Period Buttons
    $('.btn-period').on('click', function () {
        $('.btn-period').removeClass('active');
        $(this).addClass('active');

        let period = $(this).data('period');
        $('#filter_type').val(period);

        $('.filter-input-group').addClass('d-none');

        if (period === 'daily') {
            $('#group_daily').removeClass('d-none');
            $('#report_period_label').text('Daily Attendance Report');
        } else if (period === 'weekly') {
            $('#group_custom_start').removeClass('d-none');
            $('#report_period_label').text('Weekly Attendance Report');
        } else if (period === 'monthly') {
            $('#group_monthly').removeClass('d-none');
            $('#report_period_label').text('Monthly Attendance Report');
        } else if (period === 'custom') {
            $('#group_custom_start').removeClass('d-none');
            $('#group_custom_end').removeClass('d-none');
            $('#report_period_label').text('Custom Period Attendance Report');
        }

        reportTable.ajax.reload();
    });

    // Apply Filter Submit
    $('#attendanceReportFilterForm').on('submit', function (e) {
        e.preventDefault();
        reportTable.ajax.reload();
    });

    // Reset Filters
    $('#resetReportFilterBtn').on('click', function () {
        $('#attendanceReportFilterForm')[0].reset();
        $('.btn-period').removeClass('active');
        $('.btn-period[data-period="monthly"]').addClass('active');
        $('#filter_type').val('monthly');
        $('.filter-input-group').addClass('d-none');
        $('#group_monthly').removeClass('d-none');
        $('#report_period_label').text('Monthly Attendance Report');
        reportTable.ajax.reload();
    });
});
