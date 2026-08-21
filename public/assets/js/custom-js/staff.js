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

    if ($('#staff-table').length) {
        $('#staff-table').DataTable({
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
    }

    $(document).on('click', '.btn-delete', function () {
        let id = $(this).data('id');
        let name = $(this).data('name');
        if (id && name) {
            $('#deleteForm').attr('action', APP_URL + '/' + name + '/' + id);
        }
    });

    // Requirement 2: Toggle Staff Leave Status
    $(document).on('click', '.btn-toggle-leave', function () {
        let btn = $(this);
        let id = btn.data('id');
        btn.prop('disabled', true);

        $.ajax({
            url: APP_URL + '/admin/staff/toggle-leave/' + id,
            type: 'POST',
            success: function (response) {
                if (response.status) {
                    showAlert('success', response.message);
                    setTimeout(function () {
                        location.reload();
                    }, 800);
                }
            },
            error: function () {
                showAlert('danger', 'Failed to update staff leave status.');
                btn.prop('disabled', false);
            }
        });
    });

    // Requirement 2: View & Reassign Today's Follow-ups for Staff on Leave
    $(document).on('click', '.btn-view-leave-followups', function () {
        let staffId = $(this).data('id');
        let staffName = $(this).data('name');
        let tbody = $('#leaveStaffFollowupsTbody');

        $('#leaveStaffNameTitle').text(staffName);
        tbody.html('<tr><td colspan="8" class="text-center"><span class="spinner-border spinner-border-sm me-1"></span> Loading pending follow-ups...</td></tr>');
        $('#leaveStaffFollowupsModal').modal('show');

        // Fetch available staff members first
        let availableStaffOptions = '<option value="">-- Select New Staff --</option>';

        $.ajax({
            url: APP_URL + '/admin/followups/leave-staff/' + staffId,
            type: 'GET',
            success: function (response) {
                if (response.status) {
                    let followups = response.data;
                    if (followups.length === 0) {
                        tbody.html('<tr><td colspan="8" class="text-center text-muted">No pending follow-ups scheduled for today for this staff member.</td></tr>');
                        return;
                    }

                    // Build table of follow-ups with inline reassign select
                    let rowsHtml = '';
                    $.each(followups, function (index, row) {
                        let clientName = (row.lead && row.lead.customer) ? row.lead.customer.name : (row.lead ? row.lead.lead_title : 'N/A');
                        let contactMobile = (row.lead && row.lead.customer && row.lead.customer.mobile) ? row.lead.customer.mobile : 'N/A';
                        let dt = row.next_followup_date ? formatDateTime(row.next_followup_date) : 'Today';
                        let typeText = row.followup_type;
                        if (row.followup_type === 'Call' && row.duration) {
                            typeText += ` (${row.duration})`;
                        }

                        rowsHtml += `
                            <tr id="leave-followup-row-${row.followups_id}">
                                <td>${index + 1}</td>
                                <td><strong>${row.lead ? row.lead.lead_title : 'N/A'}</strong><br><small class="text-muted"><i class="bx bx-user me-1"></i>${clientName}</small></td>
                                <td><a href="tel:${contactMobile}" class="text-primary"><i class="bx bx-phone me-1"></i>${contactMobile}</a></td>
                                <td><span class="badge bg-label-info">${typeText}</span></td>
                                <td><span class="badge bg-label-dark"><i class="bx bx-calendar me-1"></i>${dt}</span></td>
                                <td><span class="badge bg-label-warning">${row.followup_status}</span></td>
                                <td><small class="text-muted">${row.remarks || '-'}</small></td>
                                <td>
                                    <button class="btn btn-sm btn-primary btn-reassign-followup" data-id="${row.followups_id}" data-staff="${staffName}">
                                        <i class="bx bx-user-voice me-1"></i> Reassign Staff
                                    </button>
                                </td>
                            </tr>
                        `;
                    });

                    tbody.html(rowsHtml);
                }
            },
            error: function () {
                tbody.html('<tr><td colspan="8" class="text-center text-danger">Failed to load follow-ups.</td></tr>');
            }
        });
    });
});
