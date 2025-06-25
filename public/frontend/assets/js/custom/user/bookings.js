/* global loadTranslationFile,  document, showToast, setTimeout, moment, FormData, window, _l,  jQuery,  FullCalendar*/

(($) => {
    "use strict";

    (async () => {
        await loadTranslationFile("web", "user,common,home");
        fetchUserBookings();

        const $reviewForm = $("#reviewForm");
        const csrfToken = $("meta[name=\"csrf-token\"]").attr("content");

        $reviewForm.validate({
            rules: {
                comments: {
                    required: true,
                    minlength: 3
                }
            },
            messages: {
                comments: {
                    required: _l("web.home.comments_required"),
                    minlength: _l("web.home.comments_minlength")
                }
            },
            errorPlacement(error, element) {
                const errorId = `${element.attr("id")}_error`;
                $(`#${errorId}`).text(error.text());
            },
            highlight(element) {
                const $element = $(element);
                if ($element.hasClass("select2-hidden-accessible")) {
                    $element.next(".select2-container").addClass("is-invalid").removeClass("is-valid");
                }
                $element.addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight(element) {
                const $element = $(element);
                if ($element.hasClass("select2-hidden-accessible")) {
                    $element.next(".select2-container").removeClass("is-invalid").addClass("is-valid");
                }
                $element.removeClass("is-invalid").addClass("is-valid");
                const errorId = `${element.id}_error`;
                $(`#${errorId}`).text("");
            },
            onkeyup(element) {
                $(element).valid();
            },
            onchange(element) {
                $(element).valid();
            },
            submitHandler() {
                const formData = new FormData();
                formData.append("comments", $("#comments").val());
                formData.append("service_ratings", $("#service_ratings input[type=\"checkbox\"]:checked").length);
                formData.append("location_ratings", $("#location_ratings input[type=\"checkbox\"]:checked").length);
                formData.append("facility_ratings", $("#facility_ratings input[type=\"checkbox\"]:checked").length);
                formData.append("value_for_money_ratings", $("#value_for_money_ratings input[type=\"checkbox\"]:checked").length);
                formData.append("cleanliness_ratings", $("#cleanliness_ratings input[type=\"checkbox\"]:checked").length);
                formData.append("vehicle_id", $("#reviewForm .vehicle_id").val());

                $.ajax({
                    type: "POST",
                    url: "/user/add-review",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    beforeSend() {
                        $(".submit-review").prop("disabled", true).html(
                            "<span class=\"spinner-border spinner-border-sm align-middle\" role=\"status\" aria-hidden=\"true\"></span> " 
                            + _l("web.home.submitting") + ".."
                        );
                    },
                    success(resp) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        $(".submit-review").prop("disabled", false).html(_l("web.home.submit_review"));
                        $reviewForm[0].reset();
                        $(".service_ratings, .location_ratings, .facility_ratings, .value_for_money_ratings, .cleanliness_ratings").prop("checked", false);
                        
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            $("#addReviewModal").modal("hide");
                            fetchUserBookings();
                        }
                    },
                    error(error) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        $(".submit-review").prop("disabled", false).html(_l("web.home.submit_review"));

                        if (error?.responseJSON?.code === 422) {
                            const { errors } = error.responseJSON;
                            Object.keys(errors).forEach((key) => {
                                $(`#${key}`).addClass("is-invalid");
                                $(`#${key}_error`).text(errors[key][0]);
                            });
                        } else {
                            showToast("error", error?.responseJSON?.message || "Something went wrong");
                        }
                    }
                });
            }
      });
})();

const fetchUserBookings = (callback = null) => {
    const status = $(".status_filter.active").data("status") || "";
    const customFrom = $("#custom_from_date").val();
    const customTo = $("#custom_to_date").val();
    const dateFilter = $(".datefilter.active").data("id");
    const sortFilter = $(".sort-filter.active").data("id");
    const csrfToken = $("meta[name=\"csrf-token\"]").attr("content");
    const isCalendar = Boolean(callback);

    $.ajax({
        url: "/user/ajax-bookings",
        type: "POST",
        data: {
            duration: dateFilter,
            status: status,
            custom_from_date: dateFilter === "custom" ? customFrom : null,
            custom_to_date: dateFilter === "custom" ? customTo : null,
            sortby: sortFilter,
            _token: csrfToken
        },
        beforeSend: () => {
            toggleLoader(isCalendar, true);
        },
        success: (response) => {
            if (isCalendar) {
                callback(response);
                return;
            }

            const tableSelector = "#bookingTable";
            let table = $(tableSelector).DataTable();

            if (!$.fn.DataTable.isDataTable(tableSelector)) {
                table = $(tableSelector).DataTable({
                    ordering: false,
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
                            previous: _l("web.common.prev")
                        }
                    }
                });
            }

            table.clear();

            if (response.status === "success" && Array.isArray(response.data) && response.data.length > 0) {
                response.data.forEach((booking) => {
                    table.row.add($(createBookingCard(booking)));
                });
                setTimeout(() => {
                    table.columns.adjust().draw();
                }, 0);
            } else {
                $("#bookingTable tbody").html(
                    `<tr><td colspan="5" class="text-center">${_l("we.common.no_bookings_found")}</td></tr>`
                );
            }

            table.draw();
            $(".booking-headers").trigger("click");
            $("#totalBookingCount").text(response.data.length || 0);
        },
        complete: () => {
            toggleLoader(isCalendar, false);
        }
    });
};

function toggleLoader(isCalendar, show) {
    if (isCalendar) {
        $(".calendar-loader").toggleClass("d-none", !show);
        $(".real-calendar").toggleClass("d-none", show);
    } else {
        $(".table-loader").toggleClass("d-none", !show);
        $(".real-table").toggleClass("d-none", show);
    }
}

const initializeCalendar = () => {
    const calendarEl = document.getElementById("fullcalendar");
    if (!calendarEl) return;

    if (calendarEl.fullCalendarInstance) {
        calendarEl.fullCalendarInstance.destroy();
    }

    fetchUserBookings((response) => {
        if (response.status !== "success") return;

        const events = (response.data || []).map((item) => {
            const title = item.vehicle_name.length > 15
                ? `${item.vehicle_name.substring(0, 15)}...`
                : ucfirst(item.vehicle_name);
            return {
                id: item.id,
                title: title,
                start: item.start_datetime,
                end: item.end_datetime,
                classNames: getStatusClass(item.status),
                extendedProps: { image: item.vehicle_image }
            };
        });

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: "dayGridMonth",
            editable: false,
            aspectRatio: 1.5,
            events: events,
            headerToolbar: {
                start: "title",
                end: "prev,dayGridMonth,next book"
            },
            views: {
                dayGridMonth: {
                    titleFormat: { month: "long" }
                }
            },
            customButtons: {
                book: {
                    text: _l("web.user.add_booking"),
                    click: () => {
                        window.location.href = "/vehicles";
                    }
                }
            },
            eventContent: (arg) => {
                const event = arg.event;
                return {
                    html:
                        "<div class=\"view_booking\" data-id=\"" + event.id + "\" " +
                        "style=\"display: flex; align-items: center; cursor: pointer; " +
                        "white-space: nowrap; overflow: hidden; text-overflow: ellipsis; " +
                        "position: relative; padding: 2px;\">" +
                        "<img src=\"" + event.extendedProps.image + "\" width=\"15\" height=\"15\" " +
                        "style=\"border-radius:3px; margin-right:5px;\">" +
                        "<span>" + event.title + "</span>" +
                        "</div>"
                };
            },
            eventDidMount: (arg) => {
                const el = arg.el;
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
        case 5:
            return "event-completed";
        case 1:
            return "event-inprogress";
        case 6:
            return "event-cancel";
        case "upcoming":
            return "event-upcoming";
        default:
            return "event-default";
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
    const drivingType = (booking.driving_type ?? "").replace(/_/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());
    const statusLabel = formatStatusLabel(booking.status);
    const vehicleName = ucfirst(booking.vehicle_name ?? "");
    const rentalType = ucfirst(booking.rental_type ?? "");
    const pickupLocation = ucfirst(booking.pickup_location ?? "");
    const returnLocation = ucfirst(booking.return_location ?? "");
    const addReviewButton = `<a class="dropdown-item add_review" href="javascript:void(0);" data-vehicle_id="${booking.vehicle_id}">
        <i class="feather-plus"></i> ${_l("web.user.add_review")}
    </a>`;

    return `
<tr>
    <td>
        <a href="javascript:void(${booking.id});" class="view_booking" data-id="${booking.id}">#${booking.reservation_id}</a>
    </td>
    <td>
        <div class="table-avatar">
            <a href="${booking.vehicle_page_url}" target="_blank" class="avatar flex-shrink-0">
                <img class="avatar-img" src="${booking.vehicle_image}" alt="${vehicleName}">
            </a>
            <div class="table-head-name flex-grow-1">
                <a href="${booking.vehicle_page_url}" target="_blank">${vehicleName}</a>
                <p>${drivingType}</p>
            </div>
        </div>
    </td>
    <td><p>${rentalType}</p></td>
    <td><p>${pickupLocation}<span class="d-block">${booking.formated_start_datetime ?? ""}</span></p></td>
    <td><p>${returnLocation}<span class="d-block">${booking.formated_end_datetime ?? ""}</span></p></td>
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
                ${booking.status === 5 && booking.review_added === false ? addReviewButton : ""}
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
const formatDrivingType = type => type?.replace(/_/g, " ").replace(/\b\w/g, c => c.toUpperCase());
const setElementContent = (selector, content = "") => $(selector).html(content);
const setImageSrc = (selector, src = "") => $(selector).attr("src", src);

const renderButtons = (data) => {
    "use strict";

    const now = moment();
    const start = moment(data.start_datetime);
    const end = moment(data.end_datetime);
    const showStartRideButton = now.isBetween(start, end, null, "[]");
    let html = "";

    switch (data.status) {
        case 4:
            html += `<a href="javascript:void(0);" id="cancel_booking" data-id="${data.id}" class="btn me-2 btn-sm btn-secondary">
                        ${_l("web.common.cancel")} ${_l("web.user.booking")}
                    </a>`;
            if (showStartRideButton) {
                html += `<a href="javascript:void(0);" id="start_ride" data-id="${data.id}" class="btn btn-sm btn-primary">
                            ${_l("web.user.start_ride")}
                        </a>`;
            }
            break;
        case 1:
            html += `<a href="javascript:void(0);" id="complete_booking" data-id="${data.id}" class="btn btn-sm btn-primary">
                        ${_l("web.user.complete_ride")}
                     </a>`;
            break;
        default:
            html = "";
            break;
    }

    document.querySelector(".modal_footer").innerHTML = html;
};

// Fetch & Show Booking Details
$(document).on("click", ".view_booking", (e) => {
    "use strict";

    const bookingId = $(e.currentTarget).data("id");

    $.get(`/user/booking-details/${bookingId}`, (response) => {
        if (response.status !== "success") return;

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

        const fullName = `${data.booking_user_info?.first_name ?? ""} ${data.booking_user_info?.last_name ?? ""}`;
        setElementContent(".user-name", fullName);
        setElementContent(".user-email", data.booking_user_info?.email ?? "");
        setElementContent(".user-phone", data.booking_user_info?.phone_number ?? "");
        setElementContent(".user-address", data.booking_user_info?.address ?? "");
        setElementContent(".user-passengers", data.no_of_passengers ?? "");

        if (data.status === 6) {
            $(".cancel-reason-section").html(`
                <div class="cancel-reason">
                    <h6>${_l("web.user.cancel_reason")}</h6>
                    <p>${data.cancel_reason ?? ""}</p>
                </div>
                <div class="cancel-box">
                    <p>${_l("web.user.cancelled_by")} ${data.cancel_by ?? ""} ${_l("web.user.on")} ${data.formated_cancel_date ?? ""}</p>
                </div>
            `);
        } else {
            $(".cancel-reason-section").empty();
        }

        $modal.modal("show");
    });
});

// Cancel Booking Modal Toggle
$(document).on("click", "#cancel_booking", (e) => {
    "use strict";

    const bookingId = $(e.currentTarget).data("id");
    $("#cancelRideForm #booking_id").val(bookingId);
    $("#booking_details").modal("hide");
    $("#cancel_ride").modal("show");
});

// Cancel Booking Submission
$(document).on("submit", "#cancelRideForm", (e) => {
    "use strict";

    e.preventDefault();

    const $form = $(e.currentTarget);
    const reason = $(".cancel-reason", $form).val().trim();
    const $error = $(".cancel-reason-error", $form);
    const $input = $(".cancel-reason", $form);

    if (!reason) {
        $input.addClass("is-invalid");
        $error.text(_l("web.common.enter_cancel_reason"));
        return;
    }

    $input.removeClass("is-invalid");
    $error.text("");

    const id = $("#booking_id", $form).val();
    const csrfToken = $("meta[name='csrf-token']").attr("content");

    $.ajax({
        type: "POST",
        url: "/user/cancel-ride",
        data: { id: id, reason: reason, _token: csrfToken },
        beforeSend: () => {
            $(".submitbtn", $form).attr("disabled", true).html(spinnerHTML());
        },
        success: (response) => {
            $("#cancel_ride").modal("hide");
            showToast(response.status, response.message);
            if (response.status === "success") {
                fetchUserBookings();
            }
        },
        complete: () => {
            $(".submitbtn", $form).attr("disabled", false).text("Submit");
        }
    });
});

// Real-time Cancel Reason Validation
$(document).on("keyup", ".cancel-reason", (e) => {
    "use strict";

    const $input = $(e.currentTarget);
    if ($input.val().trim()) {
        $input.removeClass("is-invalid");
        $(".cancel-reason-error").text("");
    }
});

// Booking Actions
$(document).on("click", "#complete_booking", (e) => {
    "use strict";

    const id = $(e.currentTarget).data("id");
    sendBookingAction(id, "/user/complete-ride", "#complete_booking", _l("web.user.complete_ride"), "#ride_completed");
});

$(document).on("click", "#start_ride", (e) => {
    "use strict";

    const id = $(e.currentTarget).data("id");
    sendBookingAction(id, "/user/start-ride", "#start_ride", _l("web.user.start_ride"), "#ride_started");
});

const sendBookingAction = (id, url, btnSelector, btnText, modalToShow) => {
    "use strict";

    const csrfToken = $("meta[name='csrf-token']").attr("content");

    $.ajax({
        type: "POST",
        url: url,
        data: { id: id, _token: csrfToken },
        beforeSend: () => {
            $(btnSelector).attr("disabled", true).html(spinnerHTML());
        },
        success: (response) => {
            if (response.status === "success") {
                $("#booking_details").modal("hide");
                $(modalToShow).modal("show");
                fetchUserBookings();
            } else {
                showToast("error", response.message);
            }
        },
        complete: () => {
            $(btnSelector).attr("disabled", false).html(btnText);
        }
    });
};

// Delete Booking
$(document).on("click", "#delete_booking", (e) => {
    "use strict";

    const id = $(e.currentTarget).data("id");
    $("#deleteForm #delete_id").val(id);
});

$(document).on("submit", "#deleteForm", (e) => {
    "use strict";

    e.preventDefault();

    const $form = $(e.currentTarget);
    const id = $("#delete_id", $form).val();
    const csrfToken = $("meta[name='csrf-token']").attr("content");

    $.ajax({
        type: "POST",
        url: "/user/delete-ride",
        data: { id: id, _token: csrfToken },
        beforeSend: () => {
            $(".submitbtn", $form).attr("disabled", true).html(spinnerHTML());
        },
        success: (response) => {
            showToast(response.status, response.message);
            if (response.status === "success") {
                fetchUserBookings();
            }
        },
        complete: () => {
            $(".submitbtn", $form).attr("disabled", false).html(_l("web.common.delete"));
        }
    });
});

// Filtering & Sorting
const handleBookingReload = () => {
    "use strict";

    const view = $(".booking_view.active").data("view");
    if (view === "list") {
        fetchUserBookings();
    } else {
        fetchUserBookings(initializeCalendar);
    }
};

$(document).on("click", "#apply-custom-filter", () => {
    "use strict";

    const from = $("#custom_from_date").val();
    const to = $("#custom_to_date").val();

    if (!from || !to) {
        $("#custom_date_error").text(_l("web.common.enter_from_to_date"));
        return;
    }

    if (new Date(to) < new Date(from)) {
        $("#custom_date_error").text(_l("web.common.to_date_must_greater"));
        return;
    }

    $("#custom_date_error").text("");
    $("#custom_date").modal("hide");
    handleBookingReload();
});

$(document).on("click", ".datefilter", (e) => {
    "use strict";

    const $el = $(e.currentTarget);
    $(".datefilter").removeClass("active");
    $el.addClass("active");
    $(".datefilter_text").text($el.text().trim());

    if ($el.data("id") === "custom") {
        return;
    }

    $("#custom_from_date, #custom_to_date").val("");
    handleBookingReload();
});

$(document).on("click", ".sort-filter", (e) => {
    "use strict";

    const $el = $(e.currentTarget);
    $(".sort-filter").removeClass("active");
    $el.addClass("active");
    $(".sortfilter_text").text($el.text().trim());
    handleBookingReload();
});


// Spinner HTML Helper
const spinnerHTML = () => "<span class=\"spinner-border spinner-border-sm align-middle\" role=\"status\" aria-hidden=\"true\"></span>";

const handleRatingClick = (selector) => {
    $(selector).on("click", function () {
        const selectedValue = $(this).val();
        $(selector).each(function () {
            $(this).prop("checked", $(this).val() >= selectedValue);
        });
    });
};

$(document).ready(function () {
    handleRatingClick(".service_ratings");
    handleRatingClick(".location_ratings");
    handleRatingClick(".facility_ratings");
    handleRatingClick(".value_for_money_ratings");
    handleRatingClick(".cleanliness_ratings");

    $(document).on("click", ".add_review", function (e) {
        const vehicleId = $(e.currentTarget).data("vehicle_id");
        $("#reviewForm .vehicle_id").val(vehicleId);
        $("#addReviewModal").modal("show");
    });
});
})(jQuery);