(async () => {
    await loadTranslationFile('web', 'user,common');
    fetchUserBookings();
})();

function fetchUserBookings(callback = null) {
    let limit = 3;
    let status = $(".status_filter.active").data('status') || '';
    let custom_from_date = $("#custom_from_date").val();
    let custom_to_date = $("#custom_to_date").val();
    let datefilter = $(".datefilter.active").data('id');
    let sort_filter = $(".sort-filter.active").data('id');
    $.ajax({
        url: '/user/ajax-bookings',
        type: 'POST',
        data: {
            duration: datefilter,
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
            if (callback) {
                callback(response);
                return;
            }

            if (!$.fn.DataTable.isDataTable('#bookingTable')) {
                $('#bookingTable').DataTable({
                    ordering: true,
                    searching: false,
                    pageLength: 10,
                    lengthChange: false,
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
            $("#totalBookingCount").html(response.data.length || 0);
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
            $(".booking-headers").trigger('click');
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

function initializeCalendar() {
    if (!$('#fullcalendar').length) return;

    const calendarEl = document.getElementById('fullcalendar');

    // Destroy previous instance before reinitializing
    if (calendarEl.fullCalendarInstance) {
        calendarEl.fullCalendarInstance.destroy();
    }

    fetchUserBookings((response) => {
        if (response.status === 'success') {
            const events = (response.data || []).map(booking => ({
                id: booking.id,
                title: booking.vehicle_name.length > 15 
                    ? booking.vehicle_name.substring(0, 15) + '...' 
                    : booking.vehicle_name,
                start: booking.start_datetime,
                end: booking.end_datetime,
                classNames: getStatusClass(booking.status),
                extendedProps: {
                    image: booking.vehicle_image
                }
            }));
        
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                editable: false,
                aspectRatio: 1.5,
                events: events,
                headerToolbar: {
                    start: 'title',
                    end: 'prev,dayGridMonth,next book'
                },
                views: {
                    dayGridMonth: {
                        titleFormat: { month: 'long' }
                    }
                },
                customButtons: {
                    book: {
                        text: `${_l('web.user.add_booking')}`,
                        click: function () {
                            window.location.href = '/vehicles';
                        }
                    }
                },
                eventContent: function(info) {
                    return { html: `
                        <div class="view_booking" data-id="${info.event.id}" style="display: flex; align-items: center; cursor: pointer; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; position: relative; padding: 2px;">
                            <img src="${info.event.extendedProps.image}" width="15" height="15" style="border-radius:3px; margin-right:5px;">
                            <span>${info.event.title}</span>
                        </div>` 
                    };
                },
                eventDidMount: function(info) {
                    let eventEl = info.el;
                    eventEl.style.position = "relative";
                    eventEl.style.left = "0";
                    eventEl.style.right = "0";
                    eventEl.style.width = "100%";
                    eventEl.style.overflow = "hidden"; 
                    eventEl.style.display = "flex"; 
                    eventEl.style.alignItems = "center";
                    eventEl.style.justifyContent = "flex-start"; 
                }
            });
        
            calendar.render();
            calendarEl.fullCalendarInstance = calendar;
        }
        
    });
       
}

function getStatusClass(status) {
    switch (status) {
        case 5: return 'event-completed';
        case 1: return 'event-inprogress';
        case 6: return 'event-cancel';
        case 'upcoming': return 'event-upcoming';
        default: return 'event-default';
    }
}

$(document).on('click', '.booking_view', function () {
    let view = $(this).data('view');
    $(".booking_view").removeClass("active");
    $(this).addClass("active");
    $("#sort_filter").removeClass("d-none");

    if (view === 'list') {
        $('#booking_list').removeClass('d-none');
        $("#calendar_view").addClass('d-none');
        fetchUserBookings();
    } else {
        fullCalendarCallback = true;
        $('#booking_list').addClass('d-none');
        $("#calendar_view").removeClass('d-none');
        $("#sort_filter").addClass("d-none");
        fetchUserBookings(initializeCalendar);
    }
});

$(document).on('click', '.status_filter', function () {
    $(".status_filter").removeClass("active");
    $(this).addClass("active");
    let view = $(".booking_view.active").data('view');
    if (view === 'list') {
        fetchUserBookings();
    } else {
        fetchUserBookings(initializeCalendar);
    }
});

function createBookingCard(booking){
    let statusLabel = formatStatusLabel(booking.status);
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
                    <p>${booking.rental_type ?? ""}</p>
                </td>
                <td>
                    <p>${booking.pickup_location ?? ""}<span class="d-block">${booking.formated_start_datetime ?? ""}</span></p>
                </td>
                <td>
                    <p>${booking.return_location ?? ""}<span class="d-block">${booking.formated_end_datetime ?? ""}</span></p>
                </td>
                <td>
                    <p>${booking.formated_booked_on ?? ""}</p>
                </td>
                <td>
                    <p class="text-darker">${booking.currency}${booking.total_amount}</p>
                </td>
                <td>
                    ${statusLabel}
                </td>
                <td class="text-end">
                    <div class="dropdown dropdown-action">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item view_booking" href="javascript:void(0);" data-id="${booking.id}">
                                <i class="feather-eye"></i> ${_l('web.common.view')}
                            </a>
                            <a class="dropdown-item" href="javascript:void(0);" id="delete_booking" data-id="${booking.id}" data-bs-toggle="modal" data-bs-target="#delete_modal">
                                <i class="feather-trash-2"></i> ${_l('web.common.delete')}
                            </a>
                        </div>
                    </div>
                </td>
            </tr>`
}

function formatStatusLabel(status) {
    let statusLabel = '';
    switch (status) {
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
            statusLabel = `<span class="badge badge-light-danger">NA</span>`;
            break;
    }
    return statusLabel
}
$(document).on('click','.view_booking', function(){
     $.ajax({
         type: "GET",
         url: "/user/booking-details/"+$(this).data('id'),
         success: function (response) {
            if(response.status == 'success'){
                let data = response.data;
                $("#booking_details").find(".bk-name").html(data.vehicle_name ?? "");
                $("#booking_details").find(".bk-img").attr("src", data.vehicle_image ?? "");
                $("#booking_details").find(".bk-location").html(data.main_location ?? "");
                $("#booking_details").find(".bk-amount").html(data.currency + data.total_amount ?? "");
                $("#booking_details").find(".bk-type").html(data.driving_type ?? "");
                $("#booking_details").find(".bk-rental").html(data.rental_type ?? "");
                $("#booking_details").find(".bk-pickup-location").html(data.pickup_location ?? "");
                $("#booking_details").find(".bk-drop-location").html(data.return_location ?? "");
                $("#booking_details").find(".bk-start-date").html(data.formated_start_datetime ?? "");
                $("#booking_details").find(".bk-end-date").html(data.formated_end_datetime ?? "");
                $("#booking_details").find(".bk-booked-on").html(data.formated_booked_on ?? "");
                $("#booking_details").find(".bk-status").html(formatStatusLabel(data.status ?? ""));
                $("#booking_details").find(".bk-extra-service").html(data.extra_services ?? "");
                let footerButtons = renderButtons(data);
                //if status cancelled then show cancel reason
                if(data.status == 'Cancelled'){
                    $(".cancel-reason-section").html(`<div class="cancel-reason">
                                                            <h6>${_l('web.user.cancel_reason')}</h6>
                                                            <p>${data.cancel_reason ?? ""}</p>
                                                        </div>
                                                        <div class="cancel-box">
                                                            <p>${_l('web.user.cancelled_by')} ${data.cancel_by ?? ""} ${_l('web.user.on')} ${data.formated_cancel_date}</p>
                                                        </div>`);
                }else{
                    $(".cancel-reason-section").html("");
                }
                $("#booking_details").find(".modal_footer").html(footerButtons);
                $("#booking_details").find(".user-name").html(data.customer  ? data.customer.name : "");
                $("#booking_details").find(".user-email").html(data.customer  ? data.customer.email : "");
                $("#booking_details").find(".user-phone").html(data.customer  ? data.customer.phone_number : "");
                $("#booking_details").find(".user-address").html(data.customer_detail  ? data.customer_detail.address : "");
                $("#booking_details").find(".user-passengers").html(data.no_of_passengers  ? data.no_of_passengers : "");
                $("#booking_details").modal("show");
            }
         }
     });
});

function renderButtons(data){
    let html = "";
    switch (data.status) {
        case 4:
            html = `<a href="javascript:void(0);" id="cancel_booking" data-id="${data.id}" class="btn btn-secondary">
                        ${_l('web.common.cancel')} ${_l('web.user.booking')}
                    </a>
                    <a href="javascript:void(0);" id="start_ride" data-id="${data.id}" class="btn btn-primary">
                       ${_l('web.user.start_ride')}
                    </a>`;
            break;
        case 1:
            html = `<a href="javascript:void(0);" id="complete_booking" data-id="${data.id}" class="btn btn-primary">
							${_l('web.user.complete_ride')}
                    </a>`;
            break;
        // case 6:
        //     html = `<a href="javascript:void(0);" data-bs-target="#view_status" data-bs-toggle="modal"  data-bs-dismiss="modal" class="btn btn-primary">
		// 					${_l('web.common.view')} ${_l('web.common.status')}
        //             </a>`;
        //     break;
        case 5:
            html = `<button class="btn btn-light" data-bs-dismiss="modal">${_l('web.common.close')}</button>`;
            break;
        default:
            html = `<button class="btn btn-light" data-bs-dismiss="modal">${_l('web.common.close')}</button>`;
            break;
    }
    return html;
}

$(document).on('click','#cancel_booking', function(){
    let id = $(this).data('id');
    $("#booking_details").modal("hide");
    $("#cancelRideForm #booking_id").val(id);
    $("#cancel_ride").modal("show");
});
$(document).on('submit','#cancelRideForm', function(e){
    e.preventDefault();
    let reason = $("#cancelRideForm .cancel-reason").val();
    if(reason.length <= 0){
        $("#cancelRideForm .cancel-reason").addClass("is-invalid");
        $("#cancelRideForm .cancel-reason-error").text(`${_l('web.common.enter_cancel_reason')}`);
        return false;
    }else{
        $("#cancelRideForm .cancel-reason").removeClass("is-invalid");
        $("#cancelRideForm .cancel-reason-error").text("");
        let id = $("#cancelRideForm #booking_id").val();
        $.ajax({
            type: "POST",
            url: "/user/cancel-ride",
            data: {
                id: id,
                reason: reason,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
               $("#cancelRideForm .submitbtn").attr("disabled", true);
               $("#cancelRideForm .submitbtn").html('<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span>');
            },
            success: function (response) {
                if(response.status == 'success'){
                    $("#cancel_ride").modal("hide");
                    showToast('success', response.message);
                    fetchUserBookings();
                }else{
                    showToast('error', response.message);
                    $("#cancel_ride").modal("hide");
                }
            },
            complete: function () {
                $("#cancelRideForm .submitbtn").attr("disabled", false);
                $("#cancelRideForm .submitbtn").html('Submit');
            }
        });
    }
});

$(document).on('keyup', '#cancelRideForm .cancel-reason', function(){
    let reason = $(this).val();
    if(reason.length > 0){
        $("#cancelRideForm .cancel-reason").removeClass("is-invalid");
        $("#cancelRideForm .cancel-reason-error").text("");
    }
})

$(document).on('click','#complete_booking', function(){
     let id = $(this).data('id');
     $.ajax({
         type: "POST",
         url: "/user/complete-ride",
         data: {
             id: id,
             _token: $('meta[name="csrf-token"]').attr('content')
         },
         beforeSend: function () {
            $("#complete_booking").attr("disabled", true);
            $("#complete_booking").html('<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span>');
         },
         success: function (response) {
             if(response.status == 'success'){
                 $("#booking_details").modal("hide");
                 $("#ride_completed").modal("show");
                 fetchUserBookings();
             }else{
                 showToast('error', response.message);
             }
         },
         complete: function () {
            $("#complete_booking").attr("disabled", false);
            $("#complete_booking").html(`${_l('web.user.complete_ride')}`);
         }
     });
});

$(document).on('click','#start_ride', function(){
    let id = $(this).data('id');
    $.ajax({
        type: "POST",
        url: "/user/start-ride",
        data: {
            id: id,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
           $("#start_ride").attr("disabled", true);
           $("#start_ride").html('<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span>');
        },
        success: function (response) {
            if(response.status == 'success'){
                $("#booking_details").modal("hide");
                $("#ride_started").modal("show");
                fetchUserBookings();
            }else{
                showToast('error', response.message);
            }
        },
        complete: function () {
           $("#start_ride").attr("disabled", false);
           $("#start_ride").html(`${_l('web.user.start_ride')}`);
        }
    });
});

$(document).on('click','#delete_booking', function(){
    let id = $(this).data('id');
    $("#deleteForm #delete_id").val(id);
});

$(document).on('submit','#deleteForm', function(e){
    e.preventDefault();
    let id = $("#deleteForm #delete_id").val();
    $.ajax({
        type: "POST",
        url: "/user/delete-ride",
        data: {
            id: id,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
           $("#deleteForm .submitbtn").attr("disabled", true);
           $("#deleteForm .submitbtn").html('<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span>');
        },
        success: function (response) {
            if(response.status == 'success'){
                showToast('success', response.message);
                fetchUserBookings();
            }else{
                showToast('error', response.message);
            }
        },
        complete: function () {
           $("#deleteForm .submitbtn").attr("disabled", false);
           $("#deleteForm .submitbtn").html(`${_l('web.common.delete')}`);
        }
    });
});

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
    if (view === 'list') {
        fetchUserBookings();
    } else {
        fetchUserBookings(initializeCalendar);
    }
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

    let view = $(".booking_view.active").data('view');
    if (view === 'list') {
        fetchUserBookings();
    } else {
        fetchUserBookings(initializeCalendar);
    }
});

$(document).on('click','.sort-filter', function(){
   $(".sort-filter").removeClass("active");
   $(this).addClass("active");
   let view = $(".booking_view.active").data('view');
   if (view === 'list') {
       fetchUserBookings();
   }
   $(".sortfilter_text").text($(this).text().trim());
});