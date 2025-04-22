(async () => {
    await loadTranslationFile('web', 'user,common');
    fetchUserTransactions();
})();

function fetchUserTransactions(){
    let limit = 5;
    let status = $(".status_filter.active").data('status') || '';
    let custom_from_date = $("#custom_from_date").val();
    let custom_to_date = $("#custom_to_date").val();
    let datefilter = $(".datefilter.active").data('id');
    let sort_filter = $(".sort-filter.active").data('id');
    $.ajax({
        url: '/user/ajax-transactions',
        type: 'POST',
        data: {
            duration: datefilter,
            limit: limit,
            status: status,
            duration: datefilter,
            custom_from_date: datefilter == 'custom' ? custom_from_date : null,
            custom_to_date: datefilter == 'custom' ? custom_to_date : null,
            sortby: sort_filter,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            $(".table-loader").show();
            $(".real-table").addClass("d-none");
        },
        success: function (response) {
            

            if (!$.fn.DataTable.isDataTable('#bookingTable')) {
                $('#bookingTable').DataTable({
                    "searching": false,
                    "ordering": false,
                    "sort": false,
                    language: {
                        emptyTable: _l("web.common.empty_table"),
                        info: _l("web.common.showing") + " _START_ " + _l("web.common.to") + " _END_ " + _l("web.common.of") + " _TOTAL_ " + _l("web.common.entries"),
                        infoEmpty: _l("web.common.showing") + " 0 " + _l("web.common.to") + " 0 " + _l("web.common.of") + " 0 " + _l("web.common.entries"),
                        infoFiltered: "(" + _l("web.common.filtered_from") + " _MAX_ " + _l("web.common.total_entries") + ")",
                        lengthMenu: _l("web.common.show") + " _MENU_ " + _l("web.common.entries"),
                        search: _l("web.common.search") + ":",
                        zeroRecords: _l("web.common.no_matching_records"),
                        paginate: {
                            first: _l("web.common.first"),
                            last: _l("web.common.last"),
                            next: _l("web.common.next"),
                            previous: _l("web.common.prev"),
                        },
                    },
                });
            }
            // Get reference to the DataTable
            let table = $('#bookingTable').DataTable();
            table.clear();
            if (response.status === 'success' && response.data.length > 0) {
                let data = response.data;
                data.forEach(booking => {
                    table.row.add($(createBookingCard(booking)));
                });
            } else {
                table.clear().draw();
                $('#bookingTable tbody').html(`<tr><td colspan="5" class="text-center">${_l('we.common.no_bookings_found')}</td></tr>`);
            }
            table.draw();
        },
        complete: function () {
            $(".table-loader").hide();
            $(".real-table").removeClass("d-none");
        },
        error: function (response) {
            console.log(response);
        }
    });
}

function createBookingCard(booking){
    let statusLabel = formatStatusLabel(booking.payment_status);
    return `<tr>
                <td><a href="javascript:void(${booking.id});" class="view_booking" data-id="${booking.id}">#${booking.reservation_id}</a></td>
                 <td>
                    <div class="table-avatar">
                        <a href="${booking.vehicle_page_url}" target="_blank" class="avatar avatar-lg flex-shrink-0">
                            <img class="avatar-img" src="${booking.vehicle_image}" alt="${booking.vehicle_name ?? ""}">
                        </a>
                        <div class="table-head-name flex-grow-1">
                            <a href="${booking.vehicle_page_url}" target="_blank">${booking.vehicle_name ?? ""}</a>
                            <p>${booking.driving_type ?? ""}</p>
                        </div>
                    </div>
                </td>
                <td>
                    <p><span class="d-block">${booking.formated_end_datetime ?? ""}</span></p>
                </td>
                <td>
                    <p class="text-darker">${booking.currency}${booking.total_amount}</p>
                </td>
                <td><span class="badge badge-light-secondary">${booking.payment_type ?? ""}</span></td>
                <td>
                    ${statusLabel}
                </td>
               <!-- <td class="text-end">
                    <div class="dropdown dropdown-action">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#view_invoice">
                                <i class="feather-file-plus"></i> View Invoice
                            </a>
                            <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_modal">
                                <i class="feather-trash-2"></i> Delete
                            </a>
                        </div>
                    </div>
                </td> -->
            </tr>`
}

function formatStatusLabel(status) {
    let statusLabel = '';
    switch (status) {
        case 1:
            statusLabel = `<span class="badge badge-light-warning">${_l('web.common.pending')}</span>`;
            break;
        case 2:
            statusLabel = `<span class="badge badge-light-success">${_l('web.common.completed')}</span>`;
            break;
        case 3:
            statusLabel = `<span class="badge badge-light-danger">${_l('web.common.failed')}</span>`;
            break;
        default:
            statusLabel = `<span class="badge badge-light-danger">NA</span>`;
            break;
    }
    return statusLabel
}

$(document).on('click', '#apply-custom-filter', function () {
    let customFromDate = $("#custom_from_date").val();
    let customToDate = $("#custom_to_date").val();

    if (!customFromDate || !customToDate) {
        $("#custom_date_error").text(`${_l('web.common.enter_from_to_date')}`);
        return;
    }

    let fromDate = new Date(customFromDate);
    let toDate = new Date(customToDate);

    if (toDate < fromDate) {
        $("#custom_date_error").text(`${_l('web.common.to_date_must_greater')}`);
        return;
    }

    $("#custom_date_error").text("");
    $("#custom_date").modal("hide");
    let view = $(".booking_view.active").data('view');
    fetchUserTransactions();
});

$(document).on('click', '.datefilter', function () {
    let selectedFilter = $(this).data('id');

    $(".datefilter").removeClass("active");
    $(this).addClass("active");
    $(".datefilter_text").text($(this).text().trim());

    if (selectedFilter === "custom") {
        return;
    }

    $("#custom_from_date").val("");
    $("#custom_to_date").val("");

    fetchUserTransactions();

});

$(document).on('click','.sort-filter', function(){
   $(".sort-filter").removeClass("active");
   $(this).addClass("active");
   fetchUserTransactions();
   $(".sortfilter_text").text($(this).text().trim());
});