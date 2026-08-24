$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function calcAddTax() {
        let amt = parseFloat($('#add_amount').val()) || 0;
        let taxPct = parseFloat($('#add_tax_percentage').val()) || 0;
        let taxAmt = (amt * taxPct) / 100;
        let total = amt + taxAmt;
        $('#add_tax_amount').val(taxAmt.toFixed(2));
        $('#add_total_amount').val(total.toFixed(2));
    }

    $('#add_amount, #add_tax_percentage').on('input change', calcAddTax);

    let paymentTable = $('#payments-table').DataTable({
        ajax: {
            url: APP_URL + '/admin/payments/data',
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
                data: 'customer.name',
                defaultContent: 'N/A',
                render: function (data, type) {
                    if (type !== 'display') return data || '';
                    return `<strong>${data}</strong>`;
                }
            },
            {
                data: 'amount',
                render: function (val, type) {
                    if (type !== 'display') return val;
                    return '₹' + parseFloat(val).toFixed(2);
                }
            },
            {
                data: 'tax_percentage',
                render: function (val, type) {
                    if (type !== 'display') return val;
                    return parseFloat(val) + '%';
                }
            },
            {
                data: 'tax_amount',
                render: function (val, type) {
                    if (type !== 'display') return val;
                    return '₹' + parseFloat(val).toFixed(2);
                }
            },
            {
                data: 'total_amount',
                render: function (val, type) {
                    if (type !== 'display') return val;
                    return '<strong class="text-success">₹' + parseFloat(val).toFixed(2) + '</strong>';
                }
            },
            { data: 'payment_method' },
            {
                data: 'payment_date',
                render: function (data, type) {
                    if (!data) {
                        return type === 'display'
                            ? '<span class="text-muted">N/A</span>'
                            : '';
                    }

                    if (type !== 'display') return data;

                    return formatDate(data);
                }
            },
            {
                data: 'payment_screenshot',
                render: function (src, type) {
                    if (!src) return '<span class="text-muted">No Proof</span>';
                    if (type !== 'display') return src;
                    let url = APP_URL + '/' + src;
                    return `<button class="btn btn-sm btn-outline-info preview-btn" data-url="${url}"><i class="bx bx-image me-1"></i> View Proof</button>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-xs btn-outline-danger btn-delete-payment" data-id="${row.payment_id}">
                            <i class="bx bx-trash"></i> Delete
                        </button>
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

    $('.modal').on('hidden.bs.modal', function () {
        let form = $(this).find('form');
        if (form.length) {
            clearValidationErrors(form);
        }
    });

    // Add Payment Form Submit
    $('#addPaymentForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let formData = new FormData(this);
        let submitBtn = form.find('button[type="submit"]');

        submitBtn.prop('disabled', true);
        clearValidationErrors(form);

        $.ajax({
            url: APP_URL + '/admin/payments/store',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.status) {
                    $('#addPaymentModal').modal('hide');
                    form[0].reset();
                    paymentTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', xhr.responseJSON?.message || 'Error saving payment record.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.preview-btn', function () {
        $('#previewImage').attr('src', $(this).data('url'));
        $('#previewModal').modal('show');
    });

    $(document).on('click', '.btn-delete-payment', function () {
        if (confirm('Delete this payment record?')) {
            let id = $(this).data('id');
            $.ajax({
                url: APP_URL + '/admin/payments/delete/' + id,
                type: 'DELETE',
                success: function (response) {
                    if (response.status) {
                        paymentTable.ajax.reload(null, false);
                        showAlert('success', response.message);
                    }
                },
                error: function () {
                    showAlert('danger', 'Failed to delete payment record.');
                }
            });
        }
    });
});
