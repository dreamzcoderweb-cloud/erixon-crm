$(document).ready(function () {
    // datatable js start
    new DataTable('#testimonials-table', {
        layout: {},
        "ordering": false,
        oLanguage: {
            sLengthMenu: "_MENU_",
        }
    });
    // datatable js end

    // modal delete operation start
    $('#deleteModal').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget); // Button that triggered the modal
        let Id = button.data('id'); // Extract info from data-* attributes
        let Name = button.data('name');
        let form = $('#deleteForm');

        // Update form action URL
        form.attr('action', APP_URL + '/' + Name + '/' + Id);
    });
    // modal delete operation end

    // active and in active status changes start (testimonial)
    $(document).on('change', '.change_testimonial_status', function () {
        let dataId = $(this).data('id');
        let isChecked = $(this).is(':checked');
        let status = isChecked ? 'Active' : 'Inactive';
        $.ajax({
            url: APP_URL + '/change_testimonial_status',
            type: 'GET',
            data: { id: dataId, status: status },
            success: function (response) {
                if (response.success) {
                    let message = response.status === 'Active' ? '<span class="text-success">Status changed</span>' : '<span class="text-success">Status changed</span>';
                    $('#status_msg_' + dataId).html(message).fadeIn().delay(1000).fadeOut();
                } else {
                    alert('Error updating status.');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error: ' + status + error);
            }
        });
    });

    $(document).on('change', '.change_testimonial_status_wof', function () {
        let dataId = $(this).data('id');
        let isChecked = $(this).is(':checked');
        let status = isChecked ? 'Active' : 'Inactive';
        $.ajax({
            url: APP_URL + '/change_testimonial_status',
            type: 'GET',
            data: { id: dataId, status: status },
            success: function (response) {
                if (response.success) {
                    let message = response.status === 'Active' ? '<span class="text-success">Status changed</span>' : '<span class="text-success">Status changed</span>';
                    $('#status_msg_' + dataId).html(message).fadeIn().delay(1000).fadeOut();
                } else {
                    alert('Error updating status.');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error: ' + status + error);
            }
        });
    });
    // active and in active status changes end (testimonial)

    // testimonial status filer start
    $("#filter_button").on("click", function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let testimonial_status = $("#status_filter").val();
        let testimonial_rating = $("#rating_filter").val();
        if (testimonial_status || testimonial_rating) {
            $.ajax({
                url: 'testimonial_filter',
                type: 'POST',
                data: {
                    "testimonial_status": testimonial_status,
                    "testimonial_rating": testimonial_rating,
                },
                success: function (response) {
                    if (response.success) {
                        let filerdata = response.filter;
                        let table = $('#testimonials-table').DataTable(); // Access the DataTable instance

                        // Destroy the existing DataTable
                        table.destroy();

                        // Clear the table body
                        let tableBody = $("#testimonials-table tbody");
                        tableBody.empty();

                        filerdata.forEach(function (filter) {
                            let row = `
                                <tr>
                                    <td><img src="assets/img/testimonial/${filter.image}" alt="testimonial image" class="rounded" width="50" height="50"></td>
                                    <td>${filter.name}</td>
                                    <td>${filter.designation}</td>
                                    <td>${filter.rating}</td>
                                    <td>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input change_testimonial_status my-element" type="checkbox"
                                                id="flexSwitchCheckChecked" data-id="${filter.id}"
                                                ${filter.status == 'Active' ? 'checked' : ''}>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                        <a class="btn btn-outline-primary btn-edit"
                                            href="edit_testimonial_filter/${filter.id}">
                                            <i class="bx bx-edit-alt me-1"></i>
                                        </a>
                                        <a href="#" class="btn btn-outline-danger btn-delete" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal" data-id="${filter.id}"
                                            data-name="delete_testimonial_filter">
                                            <i class="bx bx-trash me-1"></i>
                                        </a>
                                        </div>
                                    </td>
                                </tr>
                            `;
                            tableBody.append(row);
                        });

                        // Reinitialize the DataTable
                        new DataTable('#testimonials-table', {
                            layout: {}
                        });

                        $("#status_filter").val('');
                        $("#rating_filter").val('');
                        $('#basicModal').modal('toggle');

                    } else {
                        console.error("Error: Unable to fetch banners");
                    }
                },

                error: function (xhr, status, error) {
                    console.error('AJAX Error: ' + status + error);
                }
            });
        }
    });
    // testimonial status filer end
});

