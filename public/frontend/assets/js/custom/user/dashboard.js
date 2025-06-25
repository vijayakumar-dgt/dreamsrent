<<<<<<< Updated upstream
=======
/* global loadTranslationFile, document, jQuery, _l, showToast, console */

>>>>>>> Stashed changes
(function ($) {
    "use strict";

    const csrfToken = $("meta[name='csrf-token']").attr("content");

    $(document).ready(async () => {
        try {
            await loadTranslationFile("web", "user,common");
            fetchUserBookings();
            fetchTransactions();
        } catch (err) {
            console.error("Error loading form or translations:", err);
        }
    });

    $(document).on("change", "#duration", fetchUserBookings);
    $(document).on("change", "#sort", fetchTransactions);

    function ucfirst(str) {
        return str ? str.charAt(0).toUpperCase() + str.slice(1) : "";
    }

    function fetchUserBookings() {
        const duration = $("#duration").val();
        const limit = 5;

        $.ajax({
            url: "/user/ajax-last-bookings",
            type: "POST",
            data: { duration, limit, _token: csrfToken },
            beforeSend: showBookingLoader,
            success: renderBookings,
<<<<<<< Updated upstream
            complete: hideBookingLoader,
            error: console.log
=======
            complete: hideBookingLoader
>>>>>>> Stashed changes
        });
    }

    function showBookingLoader() {
        $(".table-loader").removeClass("d-none");
        $(".real-table").addClass("d-none");
    }

    function hideBookingLoader() {
        $(".table-loader").addClass("d-none");
        $(".real-table").removeClass("d-none");
    }

    function renderBookings(response) {
        const $tbody = $("#bookingTable tbody");
        $tbody.empty();

        if (response.status === "success" && response.data.length > 0) {
            response.data.forEach((booking) => {
                $tbody[0].appendChild(createBookingRow(booking));
            });
        } else {
            const tr = $("<tr></tr>");
            const td = $("<td></td>", {
                colspan: 5,
                class: "text-center",
                text: _l("web.common.no_bookings_found")
            });
            tr.append(td);
            $tbody.append(tr);
        }
    }

    function fetchTransactions() {
        const sort = $("#sort").val();

        $.ajax({
            url: "/recent-transation",
            type: "GET",
            data: { sort },
            beforeSend: () => {
                renderTransactionSkeletonLoader(4);
                $(".trans-table-loader").show();
                $(".trans-real-table").addClass("d-none");
            },
            success: renderTransactions,
            complete: () => {
                $(".trans-table-loader").hide();
                $(".trans-real-table").removeClass("d-none");
            },
            error: (error) => {
                showToast("error", "Error fetching transactions.", $(error.message));
            }
        });
    }

    function renderTransactions(response) {
        const $tbody = $("#transactionTable tbody");
        $tbody.empty();

        if (response.status === "success" && response.data.length > 0) {
            response.data.forEach((transaction) => {
                const rows = createTransactionRow(transaction);
                rows.forEach((row) => {
                    $tbody[0].appendChild(row);
                });
            });
        } else {
            const tr = $("<tr></tr>");
            const td = $("<td></td>", {
                colspan: 5,
                class: "text-center",
                text: _l("web.user.no_transactions_found")
            });
            tr.append(td);
            $tbody.append(tr);
        }
    }

    function getBookingStatusLabel(status) {
        const labels = {
            1: "inprogress",
            2: "confirmed",
            3: "rejected",
            4: "booked",
            5: "completed",
            6: "cancelled"
        };

        const label = labels[status] ?? "-";
        const badge = $("<span></span>").addClass("badge");

        const badgeClass = label === "-" ? "badge-light-dark" : `badge-light-${getStatusColor(label)}`;
        badge.addClass(badgeClass).text(label === "-" ? "-" : _l(`web.common.${label}`));

        return badge[0];
    }

    function getTransactionStatusLabel(status) {
        const labels = {
            1: "pending",
            2: "completed",
            3: "failed"
        };

        const label = labels[status] ?? "-";
        const badgeClass = label === "-" ? "badge-dark" : `badge-light-${getStatusColor(label)}`;
        return `<span class="badge ${badgeClass}">${label === "-" ? "-" : _l(`web.common.${label}`)}</span>`;
    }

    function getStatusColor(label) {
        const map = {
            inprogress: "warning",
            confirmed: "success",
            rejected: "danger",
            booked: "secondary",
            completed: "success",
            cancelled: "danger",
            pending: "warning",
            failed: "danger"
        };
        return map[label] ?? "dark";
    }

    function createBookingRow(booking) {
        const tr = document.createElement("tr");

        const createTd = (html) => {
            const td = document.createElement("td");
            td.innerHTML = html;
            return td;
        };

        const td1 = document.createElement("td");
        td1.innerHTML = `
            <div class="table-avatar">
                <a href="${booking.vehicle_page_url ?? "#"}" target="_blank" class="avatar flex-shrink-0">
                    <img class="avatar-img" src="${booking.vehicle_image ?? ""}" alt="${ucfirst(booking.vehicle_name)}">
                </a>
                <div class="table-head-name flex-grow-1">
                    <a href="${booking.vehicle_page_url ?? "#"}" target="_blank">${ucfirst(booking.vehicle_name)}</a>
                    <p>${_l("web.common.rental_type")} : ${ucfirst(booking.rental_type)}</p>
                </div>
            </div>
        `;

        const td2 = createTd(`<h6>${_l("web.common.start_date")}</h6><p>${booking.formated_start_datetime ?? ""}</p>`);
        const td3 = createTd(`<h6>${_l("web.common.end_date")}</h6><p>${booking.formated_end_datetime ?? ""}</p>`);
        const td4 = createTd(`<h6>${_l("web.common.price")}</h6><h5 class="text-danger">${booking.currency ?? ""}${booking.total_amount ?? ""}</h5>`);

        const td5 = document.createElement("td");
        td5.appendChild(getBookingStatusLabel(booking.status));

        tr.append(td1, td2, td3, td4, td5);
        return tr;
    }

    function createTransactionRow(booking) {
        const tr1 = document.createElement("tr");

        const td1 = document.createElement("td");
        td1.className = "border-0";
        td1.innerHTML = `
            <div class="table-avatar">
                <a href="/user/bookings" class="avatar avatar-md flex-shrink-0">
                    <img class="avatar-img" src="${booking.vehicle_image ?? ""}" alt="Booking">
                </a>
                <div class="table-head-name flex-grow-1">
                    <a href="/user/bookings">${ucfirst(booking.vehicle_name ?? "")}</a>
                    <p>${_l("web.user.rent_type")} : ${ucfirst(booking.rent_type ?? "")}</p>
                </div>
            </div>
        `;

        const td2 = document.createElement("td");
        td2.className = "border-0 text-end";
        td2.innerHTML = getTransactionStatusLabel(booking.status);
        tr1.append(td1, td2);

        const tr2 = document.createElement("tr");
        const td3 = document.createElement("td");
        td3.colSpan = 2;
        td3.className = "pt-0";
        td3.innerHTML = `
            <div class="status-box">
                <p><span>${_l("web.common.status")} :</span> ${_l("web.user.on")} ${booking.updated_at ?? ""}</p>
            </div>
        `;
        tr2.appendChild(td3);

        return [tr1, tr2];
    }

    function renderTransactionSkeletonLoader(count = 3) {
        const $tbody = $("#transaction-skeleton-loader-body");
        $tbody.empty();

        const skeletonHTML = `
            <tr class="user_trans-skeleton">
                <td class="border-0">
                    <div class="user_trans-table-avatar skeleton">
                        <div class="user_trans-avatar avatar-md flex-shrink-0">
                            <div class="user_trans-avatar-img skeleton"></div>
                        </div>
                        <div class="user_trans-table-head-name flex-grow-1">
                            <div class="user_trans-skeleton-text skeleton"></div>
                            <div class="user_trans-skeleton-text skeleton"></div>
                        </div>
                    </div>
                </td>
                <td class="border-0 text-end">
                    <div class="user_trans-skeleton-status skeleton"></div>
                </td>
            </tr>
            <tr class="user_trans-skeleton">
                <td colspan="2" class="pt-0 pb-0 border-0">
                    <div class="user_trans-status-box">
                        <p class="user_trans-skeleton-text skeleton"></p>
                    </div>
                </td>
            </tr>
        `;

        for (let i = 0; i < count; i++) {
            $tbody.append(skeletonHTML);
        }
    }
})(jQuery);