(async () => {
    await loadTranslationFile('web', 'user,common');
    fetchUserBookings();
    fetchTransactions();
})();


function fetchUserBookings(){
    let duration = $("#duration").val();
    let limit = 5;
    $.ajax({
        url: '/user/ajax-last-bookings',
        type: 'POST',
        data: {
            duration: duration,
            limit: limit,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            $(".table-loader").show();
            $(".real-table").addClass("d-none");
        },
        success: function(response) {
            let html = '';
            if(response.status == 'success' && response.data.length > 0){
                let data = response.data;
                html = data.map(booking => createBookingCard(booking)).join('');
            }else{
                html = `<tr><td colspan="5" class="text-center">${_l('web.common.no_bookings_found')}</td></tr>`;
            }
            $("#bookingTable tbody").html(html);
        },
        complete: function() {
           $(".table-loader").hide();
           $(".real-table").removeClass("d-none");
        },
        error: function(response) {
            console.log(response);
        }
    });
}
$(document).on('change','#duration', function(){
    fetchUserBookings();
});
function createBookingCard(booking){
    let statusLabel = '';
    switch (booking.status) {
        case 1:
            statusLabel = `<span class="badge badge-light-warning">${_l('web.common.inprogress')}</span>`;
            break;
        case 2:
            statusLabel = `<span class="badge badge-light-success">${_l('web.common.confirmed')}</span>`;
            break;
        case 3:
            statusLabel = `<span class="badge badge-light-danger">${_l('web.common.rejected')}</span>`;
            break;
        case 4:
            statusLabel = `<span class="badge badge-light-secondary">${_l('web.common.booked')}</span>`;
            break;
        case 5:
            statusLabel = `<span class="badge badge-light-success">${_l('web.common.completed')}</span>`;
            break;
        case 6:
            statusLabel = `<span class="badge badge-light-danger">${_l('web.common.cancelled')}</span>`;
            break;
        default:
            statusLabel = `<span class="badge badge-light-dark">-</span>`;
            break;
    }
    return ` <tr>
                <td>
                    <div class="table-avatar">
                        <a href="${booking.vehicle_page_url}" target="_blank" class="avatar  flex-shrink-0">
                            <img class="avatar-img" src="${booking['vehicle_image']}" alt="${ucfirst(booking['vehicle_name'] ?? "")}">
                        </a>
                        <div class="table-head-name flex-grow-1">
                            <a href="${booking.vehicle_page_url}" target="_blank"> ${ucfirst(booking['vehicle_name'] ?? "")}</a>
                            <p>${_l('web.common.rental_type')} : ${ucfirst(booking['rental_type'] ?? "")}</p>
                        </div>
                    </div>
                </td>
                <td>
                    <h6>${_l('web.common.start_date')}</h6>
                    <p>${booking['formated_start_datetime']}</p>
                </td>
                <td>
                    <h6>${_l('web.common.end_date')}</h6>
                    <p>${booking['formated_end_datetime']}</p>
                </td>
                <td>
                    <h6>${_l('web.common.price')}</h6>
                    <h5 class="text-danger">${booking.currency}${booking.total_amount}</h5>
                </td>
                <td>
                    ${statusLabel}
                </td>
            </tr>`;
}

$(document).on('change','#sort', function(){
    fetchTransactions();
});
function fetchTransactions(){
    let sort = $("#sort").val();
    $.ajax({
        url: '/recent-transation',
        type: 'GET',
        data: {
            sort: sort
        },
        beforeSend: function() {
            $(".trans-table-loader").show();
            $(".trans-real-table").addClass("d-none");
        },
        success: function(response) {
            let html = '';
            if(response.status == 'success' && response.data.length > 0){
                let data = response.data;
                html = data.map(booking => createTransactionCard(booking)).join('');
            }else{
                html = `<tr><td colspan="5" class="text-center">${_l('web.user.no_transactions_found')}</td></tr>`;
            }
            $("#transactionTable tbody").html(html);
        },
        complete: function() {
           $(".trans-table-loader").hide();
           $(".trans-real-table").removeClass("d-none");
        },
        error: function(response) {
            console.log(response);
        }
    });
}


function createTransactionCard(booking){
   let bookingStatus = '';
    switch (booking.status) {
        case 1:
            bookingStatus = `<span class="badge badge-light-warning">${_l('web.common.pending')}</span>`;
            break;
        case 2:
            bookingStatus = `<span class="badge badge-light-success">${_l('web.common.completed')}</span>`;
            break;
        case 3:
            bookingStatus = `<span class="badge badge-light-danger">${_l('web.common.failed')}</span>`;
            break;
        default:
            bookingStatus = `<span class="badge badge-dark">-</span>`;
            break;
    }
    return `<tr>
                <td class="border-0">
                    <div class="table-avatar">
                        <a href="/user/bookings" class="avatar avatar-md flex-shrink-0">
                            <img class="avatar-img" src="${booking.vehicle_image}" alt="Booking">
                        </a>
                        <div class="table-head-name flex-grow-1">
                            <a href="/user/bookings">${ucfirst(booking.vehicle_name ?? "")}</a>
                            <p>${_l('web.user.rent_type')} : ${ucfirst(booking.rent_type ?? "")}</p>
                        </div>
                    </div>
                </td>
                <td class="border-0 text-end">
                    ${bookingStatus}
                </td>
            </tr>
            <tr>
                <td colspan="2" class="pt-0">
                    <div class="status-box">
                        <p><span>${_l('web.common.status')} : </span>${_l('web.user.on')} ${booking.updated_at ?? ""}</p>
                    </div>
                </td>
            </tr>`;
}
