$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    if ($('#roles-table').length) {
        $('#roles-table').DataTable({
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
            columnDefs: [
                { orderable: false, targets: -1 }
            ]
        });
    }

    $(document).on('click', '.btn-delete', function () {
        let id = $(this).data('id');
        let name = $(this).data('name');
        if (id && name) {
            $('#deleteForm').attr('action', APP_URL + '/' + name + '/' + id);
        }
    });
});
