$(document).ready(function () {
    // datatable js start
    new DataTable('#blogs-table', {
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

    // active and in active status changes start (blog)
    $(document).on('change', '.change_blog_status', function () {
        let dataId = $(this).data('id');
        let isChecked = $(this).is(':checked');
        let status = isChecked ? 'Active' : 'Inactive';
        $.ajax({
            url: APP_URL + '/change_blog_status',
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

    $(document).on('change', '.change_blog_latest_status', function () {
        let dataId = $(this).data('id');
        let isChecked = $(this).is(':checked');
        let latest_status = isChecked ? 'Y' : 'N';
        $.ajax({
            url: APP_URL + '/change_blog_latest_status',
            type: 'GET',
            data: { id: dataId, status: latest_status },
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

    $(document).on('change', '.change_blog_status_wof', function () {
        let dataId = $(this).data('id');
        let isChecked = $(this).is(':checked');
        let status = isChecked ? 'Active' : 'Inactive';
        $.ajax({
            url: APP_URL + '/change_blog_status_wof',
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

    $(document).on('change', '.change_blog_latest_status_wof', function () {
        let dataId = $(this).data('id');
        let isChecked = $(this).is(':checked');
        let latest_status = isChecked ? 'Y' : 'N';
        $.ajax({
            url: APP_URL + '/change_blog_latest_status_wof',
            type: 'GET',
            data: { id: dataId, status: latest_status },
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
    // active and in active status changes end (blog)

    // date filter start
    $("#date_filter_button").on("click", function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let from_date = $("#from_date").val();
        let to_date = $("#to_date").val();
        if (from_date || to_date) {
            $.ajax({
                url: 'date_filter',
                type: 'POST',
                data: {
                    "from_date": from_date,
                    "to_date": to_date
                },
                success: function (response) {
                    if (response.success) {
                        let blogs = response.blogs;
                        let table = $('#blogs-table').DataTable(); // Access the DataTable instance

                        // Destroy the existing DataTable
                        table.destroy();

                        // Clear the table body
                        let tableBody = $("#blogs-table tbody");
                        tableBody.empty();

                        blogs.forEach(function (blog) {
                            let row = `
                                <tr>
                                    <td><img src="assets/img/blog/${blog.image}" alt="blog image" class="rounded" width="50" height="50"></td>
                                    <td>${blog.title}</td>
                                    <td>${blog.publish_date}</td>
                                    <td>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input change_blog_status my-element" type="checkbox"
                                                id="flexSwitchCheckChecked" data-id="${blog.id}"
                                                ${blog.status == 'Active' ? 'checked' : ''}>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch mb-2">
                                        <input class="form-check-input change_blog_latest_status my-element" type="checkbox"
                                            id="flexSwitchCheckChecked" data-id="${ blog.id }"
                                            ${ blog.is_latest == 'Y' ? 'checked' : '' }>
                                    </div>
                                    </td>
                                    <td>
                                    <div class="dropdown">
                                        <a class="btn btn-outline-primary btn-edit"
                                            href="edit_blog_filter/${ blog.id }">
                                            <i class="bx bx-edit-alt me-1"></i>
                                        </a>
                                        <a href="#" class="btn btn-outline-danger btn-delete" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal" data-id="${ blog.id }"
                                            data-name="delete_blog_filter">
                                            <i class="bx bx-trash me-1"></i>
                                        </a>
                                    </div>
                                    </td>
                                </tr>
                            `;
                            tableBody.append(row);
                        });

                        // Reinitialize the DataTable
                        new DataTable('#blogs-table', {
                            layout: {}
                        });

                        $("#from_date").val('');
                        $("#to_date").val('');
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
    // date filter end
});

