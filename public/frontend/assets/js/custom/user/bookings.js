(($) => {
"use strict";

(async () => {
    await loadTranslationFile('web', 'user,common');
    fetchUserBookings();
})();

const fetchUserBookings = (callback = null) => {
    const limit = 3;
    const status = $(".status_filter.active").data("status") || "";
    const customFrom = $("#custom_from_date").val();
    const customTo = $("#custom_to_date").val();
    const dateFilter = $(".datefilter.active").data("id");
    const sortFilter = $(".sort-filter.active").data("id");

    $.ajax({
    url: "/user/ajax-bookings",
    type: "POST",
    data: {
        duration: dateFilter,
        status,
        custom_from_date: dateFilter === "custom" ? customFrom : null,
        custom_to_date: dateFilter === "custom" ? customTo : null,
        sortby: sortFilter,
        _token: $('meta[name="csrf-token"]').attr("content")
    },
    beforeSend: () => {
        $("#booking-loader-table tbody").empty();
        for (let i = 0; i < 7; i++) {
            $("#booking-loader-table thead tr").clone().appendTo("#booking-loader-table tbody");    
        }
        $(".table-loader").removeClass("d-none");
        $(".real-table").addClass("d-none");
    },
    success: (response) => {
        if (callback) return callback(response);

        const tableSelector = "#bookingTable";

        if (!$.fn.DataTable.isDataTable(tableSelector)) {
        $(tableSelector).DataTable({
            ordering: true,
            searching: false,
            pageLength: 10,
            lengthChange: false,
            language: {
            emptyTable: _l("web.common.empty_table"),
            info: `${_l("web.common.showing")} _START_ ${_l("web.common.to")} _END_ ${_l("web.common.of")} _TOTAL_ ${_l("web.common.entries")}`,
            infoEmpty: `${_l("web.common.showing")} 0 ${_l("web.common.to")} 0 ${_l("web.common.of")} 0 ${_l("web.common.entries")}`,
            infoFiltered: `(${_l("web.common.filtered_from")} _MAX_ ${_l("web.common.total_entries")})`,
            lengthMenu: `${_l("web.common.show")} _MENU_ ${_l("web.common.entries")}`,
            search: `${_l("web.common.search")}:`,
            zeroRecords: _l("web.common.no_matching_records"),
            paginate: {
                first: _l("web.common.first"),
                last: _l("web.common.last"),
                next: _l("web.common.next"),
                previous: _l("web.common.prev"),
            }
            },
        });
        }

        const table = $(tableSelector).DataTable();
        table.clear();

        if (response.status === 'success' && response.data.length > 0) {
        response.data.forEach(booking => {
            table.row.add($(createBookingCard(booking)));
        });
        } else {
        $("#bookingTable tbody").html(`<tr><td colspan="5" class="text-center">${_l("we.common.no_bookings_found")}</td></tr>`);
        }

        table.draw();
        $(".booking-headers").trigger("click");
        $("#totalBookingCount").html(response.data.length || 0);
    },
    complete: () => {
        $(".table-loader").addClass("d-none");
        $(".real-table").removeClass("d-none");
    },
    error: console.log
    });
};

const initializeCalendar = () => {
    const calendarEl = document.getElementById("fullcalendar");
    if (!calendarEl) return;

    if (calendarEl.fullCalendarInstance) calendarEl.fullCalendarInstance.destroy();

    fetchUserBookings((response) => {
    if (response.status !== "success") return;

    const events = (response.data || []).map(({
        id,
        vehicle_name,
        start_datetime,
        end_datetime,
        status,
        vehicle_image
    }) => ({
        id,
        title: vehicle_name.length > 15 ? `${vehicle_name.substring(0, 15)}...` : ucfirst(vehicle_name),
        start: start_datetime,
        end: end_datetime,
        classNames: getStatusClass(status),
        extendedProps: { image: vehicle_image }
    }));

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        editable: false,
        aspectRatio: 1.5,
        events,
        headerToolbar: { start: "title", end: "prev,dayGridMonth,next book" },
        views: { dayGridMonth: { titleFormat: { month: "long" } } },
        customButtons: {
        book: {
            text: _l("web.user.add_booking"),
            click: () => window.location.href = "/vehicles"
        }
        },
        eventContent: ({ event }) => ({
        html: `
            <div class="view_booking" data-id="${event.id}" style="display: flex; align-items: center; cursor: pointer; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; position: relative; padding: 2px;">
            <img src="${event.extendedProps.image}" width="15" height="15" style="border-radius:3px; margin-right:5px;">
            <span>${event.title}</span>
            </div>`
        }),
        eventDidMount: ({ el }) => {
        Object.assign(el.style, {
            position: "relative",
            left: "0",
            right: "0",
            width: "100%",
            overflow: "hidden",
            display: "flex",
            alignItems: "center",
            justifyContent: "flex-start"
        });
        }
    });

    calendar.render();
    calendarEl.fullCalendarInstance = calendar;
    });
};

const getStatusClass = (status) => {
    switch (status) {
    case 5: return "event-completed";
    case 1: return "event-inprogress";
    case 6: return "event-cancel";
    case "upcoming": return "event-upcoming";
    default: return "event-default";
    }
};

$(document).on("click", ".booking_view", function () {
    const view = $(this).data("view");
    $(".booking_view").removeClass("active");
    $(this).addClass("active");
    $("#sort_filter").toggleClass("d-none", view !== "list");
    $("#booking_list").toggleClass("d-none", view !== "list");
    $("#calendar_view").toggleClass("d-none", view === "list");

    fetchUserBookings(view === "list" ? null : initializeCalendar);
});

$(document).on("click", ".status_filter", function () {
    $(".status_filter").removeClass("active");
    $(this).addClass("active");
    const view = $(".booking_view.active").data("view");
    fetchUserBookings(view === "list" ? null : initializeCalendar);
});

const createBookingCard = (booking) => {
    let drivingType = (booking.driving_type ?? "").replace(/_/g, " ").replace(/\b\w/g, c => c.toUpperCase());
    let statusLabel = formatStatusLabel(booking.status);

    return `<tr>
    <td><a href="javascript:void(${booking.id});" class="view_booking" data-id="${booking.id}">#${booking.reservation_id}</a></td>
    <td>
        <div class="table-avatar">
        <a href="${booking.vehicle_page_url}" target="_blank" class="avatar flex-shrink-0">
            <img class="avatar-img" src="${booking.vehicle_image}" alt="${ucfirst(booking.vehicle_name ?? "")}">
        </a>
        <div class="table-head-name flex-grow-1">
            <a href="${booking.vehicle_page_url}" target="_blank">${ucfirst(booking.vehicle_name ?? "")}</a>
            <p>${drivingType}</p>
        </div>
        </div>
    </td>
    <td><p>${ucfirst(booking.rental_type ?? "")}</p></td>
    <td><p>${ucfirst(booking.pickup_location ?? "")}<span class="d-block">${booking.formated_start_datetime ?? ""}</span></p></td>
    <td><p>${ucfirst(booking.return_location ?? "")}<span class="d-block">${booking.formated_end_datetime ?? ""}</span></p></td>
    <td><p>${booking.formated_booked_on ?? ""}</p></td>
    <td><p class="text-darker">${booking.currency}${booking.total_amount}</p></td>
    <td>${statusLabel}</td>
    <td class="text-end">
        <div class="dropdown dropdown-action">
        <a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-ellipsis-vertical"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-end">
            <a class="dropdown-item view_booking" href="javascript:void(0);" data-id="${booking.id}">
            <i class="feather-eye"></i> ${_l("web.common.view")}
            </a>
            <a class="dropdown-item" href="javascript:void(0);" id="delete_booking" data-id="${booking.id}" data-bs-toggle="modal" data-bs-target="#delete_modal">
            <i class="feather-trash-2"></i> ${_l("web.common.delete")}
            </a>
        </div>
        </div>
    </td>
    </tr>`;
};

const formatStatusLabel = (status) => {
    const labels = {
    1: "warning",
    2: "success",
    3: "danger",
    4: "secondary",
    5: "success",
    6: "danger"
    };

    const texts = {
    1: "inprogress",
    2: "confirmed",
    3: "rejected",
    4: "booked",
    5: "completed",
    6: "cancelled"
    };

    return `<span class="badge badge-light-${labels[status] ?? "dark"}">${_l(`web.common.${texts[status] ?? "-"}`)}</span>`;
};
// Utility Functions
const ucfirst = str => str?.charAt(0).toUpperCase() + str.slice(1);
const formatDrivingType = type => type?.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
const setElementContent = (selector, content = "") => $(selector).html(content);
const setImageSrc = (selector, src = "") => $(selector).attr("src", src);

const renderButtons = data => {
    let now = moment();
    let start = moment(data.start_datetime);
    let end = moment(data.end_datetime);
    let showStartRideButton = now.isBetween(start, end, null, '[]'); // inclusive
    
    switch (data.status) {
        case 4:
            return `<a href="javascript:void(0);" id="cancel_booking" data-id="${data.id}" class="btn btn-sm btn-secondary">${_l('web.common.cancel')} ${_l('web.user.booking')}</a>
                    ${showStartRideButton ? `<a href="javascript:void(0);" id="start_ride" data-id="${data.id}" class="btn btn-sm btn-primary">${_l('web.user.start_ride')}</a>` : ""}`;
    }
};

// Fetch & Show Booking Details
$(document).on('click', '.view_booking', e => {
    const bookingId = $(e.currentTarget).data('id');
    $.get(`/user/booking-details/${bookingId}`, response => {
        if (response.status !== 'success') return;

        const data = response.data;
        const $modal = $("#booking_details");

        setElementContent(".bk-name", ucfirst(data.vehicle_name));
        setImageSrc(".bk-img", data.vehicle_image);
        setElementContent(".bk-location", ucfirst(data.main_location));
        setElementContent(".bk-amount", data.currency + (data.total_amount ?? ""));
        setElementContent(".bk-type", formatDrivingType(data.driving_type));
        setElementContent(".bk-rental", ucfirst(data.rental_type));
        setElementContent(".bk-pickup-location", ucfirst(data.pickup_location));
        setElementContent(".bk-drop-location", ucfirst(data.return_location));
        setElementContent(".bk-start-date", data.formated_start_datetime);
        setElementContent(".bk-end-date", data.formated_end_datetime);
        setElementContent(".bk-booked-on", data.formated_booked_on);
        setElementContent(".bk-status", formatStatusLabel(data.status));
        setElementContent(".bk-extra-service", data.extra_services);

        $modal.find(".modal_footer").html(renderButtons(data));
        setElementContent(".user-name", data.customer?.name);
        setElementContent(".user-email", data.customer?.email);
        setElementContent(".user-phone", data.customer?.phone_number);
        setElementContent(".user-address", data.customer_detail?.address);
        setElementContent(".user-passengers", data.no_of_passengers);

        if (data.status == 6) {
            $(".cancel-reason-section").html(`
                <div class="cancel-reason">
                    <h6>${_l('web.user.cancel_reason')}</h6>
                    <p>${data.cancel_reason ?? ""}</p>
                </div>
                <div class="cancel-box">
                    <p>${_l('web.user.cancelled_by')} ${data.cancel_by ?? ""} ${_l('web.user.on')} ${data.formated_cancel_date}</p>
                </div>`);
        } else {
            $(".cancel-reason-section").empty();
        }

        $modal.modal("show");
    });
});

// Cancel Booking Modal Toggle
$(document).on('click', '#cancel_booking', e => {
    $("#cancelRideForm #booking_id").val($(e.currentTarget).data('id'));
    $("#booking_details").modal("hide");
    $("#cancel_ride").modal("show");
});

// Cancel Booking Submission
$(document).on('submit', '#cancelRideForm', e => {
    e.preventDefault();
    const $form = $(e.currentTarget);
    const reason = $(".cancel-reason", $form).val().trim();
    const $error = $(".cancel-reason-error", $form);
    const $input = $(".cancel-reason", $form);

    if (!reason) {
        $input.addClass("is-invalid");
        $error.text(`${_l('web.common.enter_cancel_reason')}`);
        return;
    }

    $input.removeClass("is-invalid");
    $error.text("");

    const id = $("#booking_id", $form).val();

    $.ajax({
        type: "POST",
        url: "/user/cancel-ride",
        data: { id, reason, _token: $('meta[name="csrf-token"]').attr('content') },
        beforeSend: () => $(".submitbtn", $form).attr("disabled", true).html(spinnerHTML()),
        success: response => {
            $("#cancel_ride").modal("hide");
            showToast(response.status, response.message);
            if (response.status === 'success') fetchUserBookings();
        },
        complete: () => $(".submitbtn", $form).attr("disabled", false).text("Submit")
    });
});

// Real-time Cancel Reason Validation
$(document).on('keyup', '.cancel-reason', e => {
    const $input = $(e.currentTarget);
    if ($input.val().trim()) {
        $input.removeClass("is-invalid");
        $(".cancel-reason-error").text("");
    }
});

// Booking Actions
$(document).on('click', '#complete_booking', e => {
    const id = $(e.currentTarget).data('id');
    sendBookingAction(id, "/user/complete-ride", "#complete_booking", _l('web.user.complete_ride'), "#ride_completed");
});

$(document).on('click', '#start_ride', e => {
    const id = $(e.currentTarget).data('id');
    sendBookingAction(id, "/user/start-ride", "#start_ride", _l('web.user.start_ride'), "#ride_started");
});

const sendBookingAction = (id, url, btnSelector, btnText, modalToShow) => {
    $.ajax({
        type: "POST",
        url,
        data: { id, _token: $('meta[name="csrf-token"]').attr('content') },
        beforeSend: () => $(btnSelector).attr("disabled", true).html(spinnerHTML()),
        success: response => {
            if (response.status === 'success') {
                $("#booking_details").modal("hide");
                $(modalToShow).modal("show");
                fetchUserBookings();
            } else {
                showToast('error', response.message);
            }
        },
        complete: () => $(btnSelector).attr("disabled", false).html(btnText)
    });
};

// Delete Booking
$(document).on('click', '#delete_booking', e => {
    $("#deleteForm #delete_id").val($(e.currentTarget).data('id'));
});

$(document).on('submit', '#deleteForm', e => {
    e.preventDefault();
    const $form = $(e.currentTarget);
    const id = $("#delete_id", $form).val();

    $.ajax({
        type: "POST",
        url: "/user/delete-ride",
        data: { id, _token: $('meta[name="csrf-token"]').attr('content') },
        beforeSend: () => $(".submitbtn", $form).attr("disabled", true).html(spinnerHTML()),
        success: response => {
            showToast(response.status, response.message);
            if (response.status === 'success') fetchUserBookings();
        },
        complete: () => $(".submitbtn", $form).attr("disabled", false).html(`${_l('web.common.delete')}`)
    });
});

// Filtering & Sorting
const handleBookingReload = () => {
    const view = $(".booking_view.active").data('view');
    view === 'list' ? fetchUserBookings() : fetchUserBookings(initializeCalendar);
};

$(document).on('click', '#apply-custom-filter', () => {
    const from = $("#custom_from_date").val(), to = $("#custom_to_date").val();

    if (!from || !to) {
        $("#custom_date_error").text(_l('web.common.enter_from_to_date'));
        return;
    }

    if (new Date(to) < new Date(from)) {
        $("#custom_date_error").text(_l('web.common.to_date_must_greater'));
        return;
    }

    $("#custom_date_error").text("");
    $("#custom_date").modal("hide");
    handleBookingReload();
});

$(document).on('click', '.datefilter', e => {
    const $el = $(e.currentTarget);
    $(".datefilter").removeClass("active");
    $el.addClass("active");
    $(".datefilter_text").text($el.text().trim());

    if ($el.data('id') === "custom") return;

    $("#custom_from_date, #custom_to_date").val("");
    handleBookingReload();
});

$(document).on('click', '.sort-filter', e => {
    $(".sort-filter").removeClass("active");
    $(e.currentTarget).addClass("active");
    $(".sortfilter_text").text($(e.currentTarget).text().trim());
    handleBookingReload();
});

// Spinner HTML Helper
const spinnerHTML = () => `<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span>`;

})(jQuery);


