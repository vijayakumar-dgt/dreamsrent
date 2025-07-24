/* global loadTranslationFile,  document, showToast, setTimeout, _l,  jQuery*/

(($) => {
    "use strict";

    const fetchUserTransactions = async () => {
        const status = $(".status_filter.active").data("status") || "";
        const customFromDate = $("#custom_from_date").val();
        const customToDate = $("#custom_to_date").val();
        const dateFilter = $(".datefilter.active").data("id");
        const sortFilter = $(".sort-filter.active").data("id");

        try {
            $(".table-loader").show();
            $(".real-table").addClass("d-none");

            const requestData = {
                duration: dateFilter,
                status: status,
                custom_from_date: dateFilter === "custom" ? customFromDate : null,
                custom_to_date: dateFilter === "custom" ? customToDate : null,
                sortby: sortFilter,
                _token: $("meta[name=\"csrf-token\"]").attr("content")
            };

            const response = await $.ajax({
                url: "/user/ajax-transactions",
                type: "POST",
                data: requestData
            });

            const table = $("#bookingTable").DataTable({
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
                },
                destroy: true
            });

            table.clear();

            if (response.status === "success" && response.data.length > 0) {
                response.data.forEach(booking => {
                    table.row.add($(createBookingCard(booking)));
                });
                setTimeout(() => {
                    table.columns.adjust().draw();
                });
            } else {
                table.clear().draw();
                $("#bookingTable tbody").html(`<tr><td colspan="5" class="text-center">${_l("we.common.no_bookings_found")}</td></tr>`);
            }

            table.draw();
            $(".payment-header").trigger("click");
        } catch (error) {
            showToast("error", "Error fetching transactions:", $(error.message));
        } finally {
            $(".table-loader").hide();
            $(".real-table").removeClass("d-none");
        }
    };

    const createBookingCard = (booking) => {
        const statusLabel = formatStatusLabel(booking.payment_status);
        const drivingType = (booking.driving_type || "").replace(/_/g, " ").replace(/\b\w/g, (char) => char.toUpperCase());
        const paymentType = booking.payment_type ? booking.payment_type.charAt(0).toUpperCase() + booking.payment_type.slice(1) : "";

        return `
            <tr>
                <td><a href="javascript:void(${booking.id});" class="view_booking" data-id="${booking.id}">#${booking.reservation_id}</a></td>
                <td>
                    <div class="table-avatar">
                        <a href="${booking.vehicle_page_url}" target="_blank" class="avatar flex-shrink-0">
                            <img class="avatar-img" src="${booking.vehicle_image}" alt="${booking.vehicle_name || ""}">
                        </a>
                        <div class="table-head-name flex-grow-1">
                            <a href="${booking.vehicle_page_url}" target="_blank">${booking.vehicle_name || ""}</a>
                            <p>${drivingType}</p>
                        </div>
                    </div>
                </td>
                <td><p><span class="d-block">${booking.formated_booked_on || ""}</span></p></td>
                <td><p class="text-darker">${booking.currency}${booking.total_amount}</p></td>
                <td><span class="badge badge-light-secondary">${paymentType}</span></td>
                <td>${statusLabel}</td>
            </tr>`;
    };

    const formatStatusLabel = (status) => {
      const statusLabels = {
        1: `<span class="badge badge-light-warning">${_l("web.common.pending")}</span>`,
        2: `<span class="badge badge-light-success">${_l("web.common.completed")}</span>`,
        3: `<span class="badge badge-light-danger">${_l("web.common.failed")}</span>`
      };
      return statusLabels[status] || "<span class='badge badge-light-danger'>" + "NA</span>";
    };

    $(document).on("click", "#apply-custom-filter", () => {
        const customFromDate = $("#custom_from_date").val();
        const customToDate = $("#custom_to_date").val();

        if (!customFromDate || !customToDate) {
            $("#custom_date_error").text(`${_l("web.common.enter_from_to_date")}`);
            return;
        }

        const fromDate = new Date(customFromDate);
        const toDate = new Date(customToDate);

        if (toDate < fromDate) {
            $("#custom_date_error").text(`${_l("web.common.to_date_must_greater")}`);
            return;
        }

        $("#custom_date_error").text("");
        $("#custom_date").modal("hide");
        fetchUserTransactions();
    });

    $(document).on("click", ".datefilter", function () {
        const selectedFilter = $(this).data("id");

        $(".datefilter").removeClass("active");
        $(this).addClass("active");
        $(".datefilter_text").text($(this).text().trim());

        if (selectedFilter !== "custom") {
            $("#custom_from_date, #custom_to_date").val("");
            fetchUserTransactions();
        }
    });

    $(document).on("click", ".sort-filter", function () {
        $(".sort-filter").removeClass("active");
        $(this).addClass("active");
        $(".sortfilter_text").text($(this).text().trim());
        fetchUserTransactions();
    });

    (async () => {
        await loadTranslationFile("web", "user,common");
        fetchUserTransactions();
    })();

})(jQuery);
