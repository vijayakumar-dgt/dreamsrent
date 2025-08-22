/* global loadTranslationFile, document, jQuery, _l, showToast */

(function ($) {
    "use strict";

    const csrfToken = $("meta[name='csrf-token']").attr("content");

    $(document).ready(async () => {
        await loadTranslationFile("web", "user,common");
        fetchUserBookings();
        fetchTransactions();
    });

    // Event bindings
    $(document).on("change", "#duration", fetchUserBookings);
    $(document).on("change", "#sort", fetchTransactions);

    function ucfirst(str) {
        if (!str) return "";  // Fixed: return empty string instead of " "
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // Fetch Bookings
    function fetchUserBookings() {
        const duration = $("#duration").val();
        const limit = 5;

        $.ajax({
            url: "/user/ajax-last-bookings",
            type: "POST",
            data: { duration, limit, _token: csrfToken },
            beforeSend: showBookingLoader,
            success: renderBookings,
            complete: hideBookingLoader,
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
        $tbody.empty(); // Clear existing rows

        if (response.status === "success" && response.data.length > 0) {
            response.data.forEach((booking) => {
                $tbody[0].appendChild(createBookingRow(booking));
            });
        } else {
            const tr = document.createElement("tr");
            const td = document.createElement("td");
            td.colSpan = 5;
            td.className = "text-center";
            td.textContent = _l("web.common.no_bookings_found");
            tr.appendChild(td);
            $tbody[0].appendChild(tr);
        }
    }

    // Fetch Transactions
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
            success: (response) => {
                renderTransactions(response);
            },
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
            const tr = document.createElement("tr");
            const td = document.createElement("td");
            td.colSpan = 5;
            td.className = "text-center";
            td.textContent = _l("web.user.no_transactions_found");
            tr.appendChild(td);
            $tbody[0].appendChild(tr);
        }
    }

    // Reusable helpers
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
        const badge = document.createElement("span");
        badge.classList.add("badge");

        const badgeClass = label === "-" ? "badge-light-dark" : `badge-light-${getStatusColor(label)}`;
        badge.classList.add(badgeClass);

        const text = label === "-" ? "-" : _l(`web.common.${label}`);
        badge.textContent = text;

        return badge;
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
            "inprogress": "warning",
            "confirmed": "success",
            "rejected": "danger",
            "booked": "secondary",
            "completed": "success",
            "cancelled": "danger",
            "pending": "warning",
            "failed": "danger"
        };
        return map[label] ?? "dark";
    }

    function createBookingRow(booking) {
        const tr = document.createElement("tr");

        // Column 1: Vehicle Info
        const td1 = document.createElement("td");
        const avatarDiv = document.createElement("div");
        avatarDiv.className = "table-avatar";

        const a1 = document.createElement("a");
        a1.href = booking.vehicle_page_url ?? "#";
        a1.target = "_blank";
        a1.className = "avatar flex-shrink-0";

        const img = document.createElement("img");
        img.className = "avatar-img";
        img.src = booking.vehicle_image ?? "";
        img.alt = ucfirst(booking.vehicle_name ?? "");

        a1.appendChild(img);

        const nameDiv = document.createElement("div");
        nameDiv.className = "table-head-name flex-grow-1";

        const nameLink = document.createElement("a");
        nameLink.href = booking.vehicle_page_url ?? "#";
        nameLink.target = "_blank";
        nameLink.textContent = ucfirst(booking.vehicle_name ?? "");

        const typeP = document.createElement("p");
        typeP.textContent = `${_l("web.common.rental_type")} : ${ucfirst(booking.rental_type ?? "")}`;

        nameDiv.appendChild(nameLink);
        nameDiv.appendChild(typeP);

        avatarDiv.appendChild(a1);
        avatarDiv.appendChild(nameDiv);
        td1.appendChild(avatarDiv);

        // Column 2: Start Date
        const td2 = document.createElement("td");
        td2.innerHTML = `<h6>${_l("web.common.start_date")}</h6><p>${booking.formated_start_datetime ?? ""}</p>`;

        // Column 3: End Date
        const td3 = document.createElement("td");
        td3.innerHTML = `<h6>${_l("web.common.end_date")}</h6><p>${booking.formated_end_datetime ?? ""}</p>`;

        // Column 4: Price
        const td4 = document.createElement("td");
        td4.innerHTML = `<h6>${_l("web.common.price")}</h6><h5 class="text-danger">${booking.currency ?? ""}${booking.total_amount ?? ""}</h5>`;

        // Column 5: Status
        const td5 = document.createElement("td");
        td5.appendChild(getBookingStatusLabel(booking.status));

        tr.append(td1, td2, td3, td4, td5);
        return tr;
    }

    function createTransactionRow(booking) {
        const rows = [];

        // Row 1 - Main transaction info
        const tr1 = document.createElement("tr");

        const td1 = document.createElement("td");
        td1.className = "border-0";

        const avatarDiv = document.createElement("div");
        avatarDiv.className = "table-avatar";

        const avatarLink = document.createElement("a");
        avatarLink.href = "/user/bookings";
        avatarLink.className = "avatar avatar-md flex-shrink-0";

        const img = document.createElement("img");
        img.className = "avatar-img";
        img.src = booking.vehicle_image ?? "";
        img.alt = "Booking";

        avatarLink.appendChild(img);

        const nameDiv = document.createElement("div");
        nameDiv.className = "table-head-name flex-grow-1";

        const nameLink = document.createElement("a");
        nameLink.href = "/user/bookings";
        nameLink.textContent = ucfirst(booking.vehicle_name ?? "");

        const typePara = document.createElement("p");
        typePara.textContent = `${_l("web.user.rent_type")} : ${ucfirst(booking.rent_type ?? "")}`;

        nameDiv.append(nameLink, typePara);
        avatarDiv.append(avatarLink, nameDiv);
        td1.appendChild(avatarDiv);

        const td2 = document.createElement("td");
        td2.className = "border-0 text-end";
        td2.innerHTML = getTransactionStatusLabel(booking.status);

        tr1.append(td1, td2);

        // Row 2 - Status info
        const tr2 = document.createElement("tr");
        const td3 = document.createElement("td");
        td3.colSpan = 2;
        td3.className = "pt-0";

        const statusBox = document.createElement("div");
        statusBox.className = "status-box";

        const p = document.createElement("p");
        const span = document.createElement("span");
        span.textContent = `${_l("web.common.status")} : `;
        p.append(span, `${_l("web.user.on")} ${booking.updated_at ?? ""}`);

        statusBox.appendChild(p);
        td3.appendChild(statusBox);
        tr2.appendChild(td3);

        rows.push(tr1, tr2);
        return rows;
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