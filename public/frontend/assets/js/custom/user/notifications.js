/* global loadTranslationFile, document, showToast, _l, jQuery*/

(($) => {
    "use strict";

    const loadNotifications = async (page = 1) => {
        try {
            const response = await $.ajax({
                url: `/user/notifications?page=${page}`,
                method: 'GET',
            });

            if (response.count > 0) {
                $('#notification-list').html(response.html);
                $('#pagination-container').html(renderPagination(response));
                $("#notification_action").removeClass("d-none");
            } else {
                $('#notification-list').html(`<p class="text-center">${_l('web.user.no_notifications_found')}</p>`);
                $('#pagination-container').html('');
                $("#notification_action").addClass("d-none");
            }
        } catch {
            showToast("error", "Error loading notifications.");
        }
    };

    const renderPagination = (data) => `
        <nav class="custom-pagination">
            <ul class="pagination justify-content-center align-items-center">
                <li class="page-item ${data.prev_page_url ? '' : 'disabled'}">
                    <a class="page-link" href="#" data-page="${data.current_page - 1}">
                        <i class="fas fa-arrow-left me-1"></i> ${_l('web.user.prev')}
                    </a>
                </li>
                ${Array.from({ length: data.last_page }, (_, i) => `
                    <li class="page-item ${i + 1 === data.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i + 1}">
                            ${i + 1}
                        </a>
                    </li>
                `).join('')}
                <li class="page-item ${data.next_page_url ? '' : 'disabled'}">
                    <a class="page-link" href="#" data-page="${data.current_page + 1}">
                        ${_l('web.user.next')} <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </li>
            </ul>
        </nav>`;

    const handleAjaxRequest = async (url, data, successCallback) => {
        try {
            const response = await $.ajax({
                type: "POST",
                url,
                data: {
                    ...data,
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                dataType: "json",
            });

            if (response.code === 200 || response.status === 'success') {
                showToast('success', response.message);
                successCallback?.();
            } else {
                showToast('error', response.message);
            }
        } catch (error) {
            showToast('error', error.message || 'An error occurred');
        }
    };

    $(document).on('click', '.pagination .page-link', (e) => {
        e.preventDefault();
        const page = $(e.currentTarget).data('page');
        if (page) loadNotifications(page);
    });

    $(document).on('click', '#markAllAsRead', () => {
        handleAjaxRequest("/user/mark-all-notifications-as-read", {}, loadNotifications);
    });

    $(document).on("click", ".notificationitem", (e) => {
        const id = $(e.currentTarget).data('id');
        handleAjaxRequest("/user/mark-notification-as-read", { id }, loadNotifications);
    });

    $(document).on("click", ".del_notification", (e) => {
        const id = $(e.currentTarget).data('id');
        $("#delete_notification .deletebtn").data('id', id);
        $("#delete_notification").modal('show');
    });

    $(document).on('click', '#delete_notification .deletebtn', () => {
        const id = $("#delete_notification .deletebtn").data('id');
        if (id) {
            handleAjaxRequest("/user/delete-notification", { id }, loadNotifications);
            $("#delete_notification").modal('hide');
        }
    });

    $(document).on('click', '#deleteAll', () => {
        $("#deleteAllNotifications").modal('show');
    });

    $(document).on('click', '.deleteAllNotifications', () => {
        handleAjaxRequest("/user/delete-all-notifications", {}, loadNotifications);
        $("#deleteAllNotifications").modal('hide');
    });

    (async () => {
        await loadTranslationFile('web', 'user,common');
        loadNotifications();
    })();

})(jQuery);
