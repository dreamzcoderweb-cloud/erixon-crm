$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function updateBadgeCount(unreadCount) {
        let badge = $('#notification-badge-count');
        if (unreadCount > 0) {
            badge.text(unreadCount).removeClass('d-none');
        } else {
            badge.text(0).addClass('d-none');
        }
    }

    function renderNotifications(notifications) {
        let container = $('#notification-list-container');
        container.empty();

        if (!notifications || notifications.length === 0) {
            container.html(`
                <li class="list-group-item text-center text-muted py-4">
                    <i class="bx bx-bell-off fs-3 mb-1 d-block text-secondary"></i>
                    <span>No notifications available</span>
                </li>
            `);
            return;
        }

        let html = '';
        $.each(notifications, function (index, item) {
            let bgClass = item.is_read ? 'bg-white' : 'bg-light';
            let unreadDot = !item.is_read ? '<span class="badge badge-dot bg-primary me-2"></span>' : '';
            let titleClass = item.is_read ? 'text-dark font-weight-normal' : 'text-primary fw-bold';

            html += `
                <li class="list-group-item list-group-item-action ${bgClass} p-3 border-bottom notification-item" data-id="${item.id}" style="cursor: pointer;">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-circle ${item.is_read ? 'bg-label-secondary text-secondary' : 'bg-label-primary text-primary'}">
                                    <i class="bx bx-calendar-event fs-5"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 fs-6 ${titleClass}">${unreadDot}${item.title}</h6>
                                <small class="text-muted fs-tiny">${item.created_at || ''}</small>
                            </div>
                            <p class="mb-0 text-muted fs-7 small">${item.message}</p>
                        </div>
                    </div>
                </li>
            `;
        });

        container.html(html);
    }

    window.loadAppNotifications = function () {
        $.ajax({
            url: APP_URL + '/admin/notifications',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.status) {
                    updateBadgeCount(response.unread_count || 0);
                    renderNotifications(response.data || []);
                }
            },
            error: function () {
                $('#notification-list-container').html(`
                    <li class="list-group-item text-center text-muted py-3">
                        <small>Failed to load notifications</small>
                    </li>
                `);
            }
        });
    };

    // Mark single notification as read on click
    $(document).on('click', '.notification-item', function () {
        let id = $(this).data('id');
        let itemElement = $(this);

        if (id) {
            $.ajax({
                url: APP_URL + '/admin/notifications/mark-as-read/' + id,
                type: 'POST',
                dataType: 'json',
                success: function (response) {
                    if (response.status) {
                        itemElement.removeClass('bg-light').addClass('bg-white');
                        itemElement.find('.badge-dot').remove();
                        itemElement.find('h6').removeClass('text-primary fw-bold').addClass('text-dark font-weight-normal');
                        updateBadgeCount(response.unread_count || 0);
                    }
                }
            });
        }
    });

    // Mark all notifications as read
    $('#mark-all-read-btn').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        $.ajax({
            url: APP_URL + '/admin/notifications/mark-all-as-read',
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response.status) {
                    updateBadgeCount(0);
                    loadAppNotifications();
                }
            }
        });
    });

    // Load initial notifications on page load
    loadAppNotifications();

    // Auto refresh notifications every 30 seconds
    setInterval(loadAppNotifications, 30000);
});
