(function ($) {
    "use strict";

    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    $(document).ready(async () => {
        await loadTranslationFile('web', 'user,common');
        fetchUserBookings();
        fetchTransactions();
    });

    // Event bindings
    $(document).on('change', '#duration', fetchUserBookings);
    $(document).on('change', '#sort', fetchTransactions);

    function ucfirst(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
    // Fetch Bookings
    function fetchUserBookings() {
        const duration = $("#duration").val();
        const limit = 5;

        $.ajax({
            url: '/user/ajax-last-bookings',
            type: 'POST',
            data: { duration, limit, _token: csrfToken },
            beforeSend: showBookingLoader,
            success: renderBookings,
            complete: hideBookingLoader,
            error: console.log
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
        let html = '';

        if (response.status === 'success' && response.data.length) {
            html = response.data.map(createBookingRow).join('');
        } else {
            html = `<tr><td colspan="5" class="text-center">${_l('web.common.no_bookings_found')}</td></tr>`;
        }

        $tbody.html(html);
    }

    // Fetch Transactions
    function fetchTransactions() {
        const sort = $("#sort").val();

        $.ajax({
            url: '/recent-transation',
            type: 'GET',
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
            error: console.log
        });
    }

    function renderTransactions(response) {
        const $tbody = $("#transactionTable tbody");
        let html = '';

        if (response.status === 'success' && response.data.length) {
            html = response.data.map(createTransactionRow).join('');
        } else {
            html = `<tr><td colspan="5" class="text-center">${_l('web.user.no_transactions_found')}</td></tr>`;
        }

        $tbody.html(html);
    }

    // Reusable helpers
    function getBookingStatusLabel(status) {
        const labels = {
            1: 'inprogress',
            2: 'confirmed',
            3: 'rejected',
            4: 'booked',
            5: 'completed',
            6: 'cancelled'
        };

        const label = labels[status] ?? '-';
        const badgeClass = label === '-' ? 'badge-light-dark' : `badge-light-${getStatusColor(label)}`;

        return `<span class="badge ${badgeClass}">${_l(`web.common.${label}`)}</span>`;
    }

    function getTransactionStatusLabel(status) {
        const labels = {
            1: 'pending',
            2: 'completed',
            3: 'failed'
        };

        const label = labels[status] ?? '-';
        const badgeClass = label === '-' ? 'badge-dark' : `badge-light-${getStatusColor(label)}`;

        return `<span class="badge ${badgeClass}">${_l(`web.common.${label}`)}</span>`;
    }

    function getStatusColor(label) {
        const map = {
            inprogress: 'warning',
            confirmed: 'success',
            rejected: 'danger',
            booked: 'secondary',
            completed: 'success',
            cancelled: 'danger',
            pending: 'warning',
            failed: 'danger'
        };
        return map[label] ?? 'dark';
    }

    // Template creators
    function createBookingRow(booking) {
        return `
        <tr>
            <td>
                <div class="table-avatar">
                    <a href="${booking.vehicle_page_url}" target="_blank" class="avatar flex-shrink-0">
                        <img class="avatar-img" src="${booking.vehicle_image}" alt="${ucfirst(booking.vehicle_name ?? '')}">
                    </a>
                    <div class="table-head-name flex-grow-1">
                        <a href="${booking.vehicle_page_url}" target="_blank">${ucfirst(booking.vehicle_name ?? '')}</a>
                        <p>${_l('web.common.rental_type')} : ${ucfirst(booking.rental_type ?? '')}</p>
                    </div>
                </div>
            </td>
            <td>
                <h6>${_l('web.common.start_date')}</h6>
                <p>${booking.formated_start_datetime}</p>
            </td>
            <td>
                <h6>${_l('web.common.end_date')}</h6>
                <p>${booking.formated_end_datetime}</p>
            </td>
            <td>
                <h6>${_l('web.common.price')}</h6>
                <h5 class="text-danger">${booking.currency}${booking.total_amount}</h5>
            </td>
            <td>${getBookingStatusLabel(booking.status)}</td>
        </tr>`;
    }

    function createTransactionRow(booking) {
        return `
        <tr>
            <td class="border-0">
                <div class="table-avatar">
                    <a href="/user/bookings" class="avatar avatar-md flex-shrink-0">
                        <img class="avatar-img" src="${booking.vehicle_image}" alt="Booking">
                    </a>
                    <div class="table-head-name flex-grow-1">
                        <a href="/user/bookings">${ucfirst(booking.vehicle_name ?? '')}</a>
                        <p>${_l('web.user.rent_type')} : ${ucfirst(booking.rent_type ?? '')}</p>
                    </div>
                </div>
            </td>
            <td class="border-0 text-end">
                ${getTransactionStatusLabel(booking.status)}
            </td>
        </tr>
        <tr>
            <td colspan="2" class="pt-0">
                <div class="status-box">
                    <p><span>${_l('web.common.status')} : </span>${_l('web.user.on')} ${booking.updated_at ?? ''}</p>
                </div>
            </td>
        </tr>`;
    }

    function renderTransactionSkeletonLoader(count = 3) {
        const $tbody = $("#transaction-skeleton-loader-body");
        $tbody.empty();
    
        for (let i = 0; i < count; i++) {
            $tbody.append(`
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
            `);
        }
    }
    
})(jQuery);
