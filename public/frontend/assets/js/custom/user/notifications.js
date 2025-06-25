<<<<<<< Updated upstream
(($) => {
=======
/* global loadTranslationFile, document, showToast, _l, jQuery */

(function ($) {
>>>>>>> Stashed changes
    "use strict";

    const loadNotifications = async (page = 1) => {
        try {
            const response = await $.ajax({
                url: `/user/notifications?page=${page}`,
                method: "GET",
            });

            const $notificationList = $("#notification-list");
            const $paginationContainer = $("#pagination-container");
            const $notificationAction = $("#notification_action");

            if (response.count > 0) {
                $notificationList.html(response.html);
                $paginationContainer.html(renderPagination(response));
                $notificationAction.removeClass("d-none");
            } else {
                $notificationList.html(`<p class="text-center">${_l("web.user.no_notifications_found")}</p>`);
                $paginationContainer.html("");
                $notificationAction.addClass("d-none");
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    };

    const renderPagination = (data) => {
        const pageItems = Array.from({ length: data.last_page }, (_, i) => {
            const pageNum = i + 1;
            const activeClass = pageNum === data.current_page ? "active" : "";
            return `
                <li class="page-item ${activeClass}">
                    <a class="page-link" href="#" data-page="${pageNum}">
                        ${pageNum}
                    </a>
                </li>
            `;
        }).join("");

        const prevDisabled = data.prev_page_url ? "" : "disabled";
        const nextDisabled = data.next_page_url ? "" : "disabled";

        return `
            <nav class="custom-pagination">
                <ul class="pagination justify-content-center align-items-center">
                    <li class="page-item ${prevDisabled}">
                        <a class="page-link" href="#" data-page="${data.current_page - 1}">
                            <i class="fas fa-arrow-left me-1"></i> ${_l("web.user.prev")}
                        </a>
                    </li>
                    ${pageItems}
                    <li class="page-item ${nextDisabled}">
                        <a class="page-link" href="#" data-page="${data.current_page + 1}">
                            ${_l("web.user.next")} <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        `;
    };

    const handleAjaxRequest = async (url, data, successCallback) => {
        try {
            const response = await $.ajax({
                type: "POST",
                url,
                data: {
                    ...data,
                    _token: $("meta[name=\"csrf-token\"]").attr("content")
                },
                dataType: "json"
            });

            if (response.code === 200 || response.status === "success") {
                showToast("success", response.message);
                if (typeof successCallback === "function") {
                    successCallback();
                }
            } else {
                showToast("error", response.message);
            }
        } catch (err) {
            showToast("error", err.message || "An error occurred");
        }
    };

    $(document).on("click", ".pagination .page-link", (e) => {
        e.preventDefault();
        const page = $(e.currentTarget).data("page");
        if (page) {
            loadNotifications(page);
        }
    });

    $(document).on("click", "#markAllAsRead", () => {
        handleAjaxRequest("/user/mark-all-notifications-as-read", {}, loadNotifications);
    });

    $(document).on("click", ".notificationitem", (e) => {
        const id = $(e.currentTarget).data("id");
        if (id) {
            handleAjaxRequest("/user/mark-notification-as-read", { id }, loadNotifications);
        }
    });

    $(document).on("click", ".del_notification", (e) => {
        const id = $(e.currentTarget).data("id");
        $("#delete_notification .deletebtn").data("id", id);
        $("#delete_notification").modal("show");
    });

    $(document).on("click", "#delete_notification .deletebtn", () => {
        const id = $("#delete_notification .deletebtn").data("id");
        if (id) {
            handleAjaxRequest("/user/delete-notification", { id }, loadNotifications);
            $("#delete_notification").modal("hide");
        }
    });

    $(document).on("click", "#deleteAll", () => {
        $("#deleteAllNotifications").modal("show");
    });

    $(document).on("click", ".deleteAllNotifications", () => {
        handleAjaxRequest("/user/delete-all-notifications", {}, loadNotifications);
        $("#deleteAllNotifications").modal("hide");
    });

    (async () => {
        await loadTranslationFile("web", "user,common");
        loadNotifications();
    })();
})(jQuery);