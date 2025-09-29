(async function () {
    "use strict";

    await loadTranslationFile("admin", "common, bookings");

    $(document).ready(function () {
        if ($(".start_time").length > 0) {
            $(".start_time").datetimepicker({
                format: "HH:mm",
                icons: {
                    up: "fas fa-angle-up",
                    down: "fas fa-angle-down",
                    next: "fas fa-angle-right",
                    previous: "fas fa-angle-left",
                },
            });
        }

        if ($(".end_time").length > 0) {
            $(".end_time").datetimepicker({
                format: "HH:mm",
                icons: {
                    up: "fas fa-angle-up",
                    down: "fas fa-angle-down",
                    next: "fas fa-angle-right",
                    previous: "fas fa-angle-left",
                },
            });
        }

        if ($(".start_date").length > 0) {
            $(".start_date")
                .datetimepicker({
                    format: "DD-MM-YYYY",
                    icons: {
                        up: "fas fa-angle-up",
                        down: "fas fa-angle-down",
                        next: "fas fa-angle-right",
                        previous: "fas fa-angle-left",
                    },
                    minDate: moment().startOf("day"),
                })
                .on("dp.change", function (e) {
                    let selectedDate = e.date
                        ? e.date.format("DD/MM/YYYY")
                        : null;
                    let todayDate = moment().format("DD/MM/YYYY");

                    if (selectedDate === todayDate) {
                        $(".start_time")
                            .data("DateTimePicker")
                            .minDate(moment().startOf("minute"));
                    } else {
                        $(".start_time").data("DateTimePicker").minDate(false);
                    }
                });
        }

        if ($(".end_date").length > 0) {
            $(".end_date")
                .datetimepicker({
                    format: "DD-MM-YYYY",
                    icons: {
                        up: "fas fa-angle-up",
                        down: "fas fa-angle-down",
                        next: "fas fa-angle-right",
                        previous: "fas fa-angle-left",
                    },
                    minDate: moment().startOf("day"),
                })
                .on("dp.change", function (e) {
                    let selectedDate = e.date
                        ? e.date.format("DD/MM/YYYY")
                        : null;
                    let todayDate = moment().format("DD/MM/YYYY");

                    if (selectedDate === todayDate) {
                        $(".end_time")
                            .data("DateTimePicker")
                            .minDate(moment().startOf("minute"));
                    } else {
                        $(".end_time").data("DateTimePicker").minDate(false);
                    }
                });
        }

        getBrands();
        getTypes();
        getModels();
        getColors();
    });

    let default_currency = $("body").data("currency");

    function getColors(search = "") {
        $.ajax({
            url: "/get-vehicle-colors",
            type: "POST",
            data: {
                search: search,
                order_by: "asc",
            },
            dataType: "json",
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                let colorList = $("#colorList .custom-scroll");
                colorList.empty();

                if (response.code === 200 && response.data.length > 0) {
                    let data = response.data;

                    $.each(data, function (index, value) {
                        colorList.append(`
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2 color_checkbox" type="checkbox" value="${value.id}">${value.name}
                            </label>
                        </li>
                    `);
                    });
                } else {
                    colorList.append(
                        `<li class="text-center p-2">${_l(
                            "admin.common.no_data_found"
                        )}</li>`
                    );
                }
            },
            error: function (error) {
                if (error.responseJSON.code === 500) {
                    showToast("errror", error.responseJSON.message);
                } else {
                    showToast(
                        "error",
                        _l("admin.common.default_retrieve_error")
                    );
                }
            },
        });
    }

    function getModels(search = "") {
        $.ajax({
            url: "/get-vehicle-models",
            type: "POST",
            data: {
                search: search,
                order_by: "asc",
            },
            dataType: "json",
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                let modelList = $("#modelList .custom-scroll");
                modelList.empty();

                if (response.code === 200 && response.data.length > 0) {
                    let data = response.data;

                    $.each(data, function (index, value) {
                        modelList.append(`
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2 model_checkbox" type="checkbox" value="${value.id}">${value.model_name}
                            </label>
                        </li>
                    `);
                    });
                } else {
                    modelList.append(
                        `<li class="text-center p-2">${_l(
                            "admin.common.no_data_found"
                        )}</li>`
                    );
                }
            },
            error: function (error) {
                if (error.responseJSON.code === 500) {
                    showToast("error", error.responseJSON.message);
                } else {
                    showToast(
                        "error",
                        _l("admin.common.default_retrieve_error")
                    );
                }
            },
        });
    }

    function getBrands(search = "") {
        $.ajax({
            url: "/get-brands",
            type: "POST",
            data: {
                search: search,
                order_by: "asc",
            },
            dataType: "json",
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                let brandList = $("#brandList .custom-scroll");
                brandList.empty();

                if (response.code === 200 && response.data.length > 0) {
                    let data = response.data;

                    $.each(data, function (index, value) {
                        brandList.append(`
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2 brand_checkbox" type="checkbox" value="${value.id}">${value.brand_name}
                            </label>
                        </li>
                    `);
                    });
                } else {
                    brandList.append(
                        `<li class="text-center p-2">${_l(
                            "admin.common.no_data_found"
                        )}</li>`
                    );
                }
            },
            error: function (error) {
                if (error.responseJSON.code === 500) {
                    showToast("error", error.responseJSON.message);
                } else {
                    showToast(
                        "error",
                        _l("admin.common.default_retrieve_error")
                    );
                }
            },
        });
    }

    function getTypes(search = "") {
        $.ajax({
            url: "/get-vehicle-types",
            type: "POST",
            data: {
                search: search,
                order_by: "asc",
            },
            dataType: "json",
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                let typeList = $("#typeList .custom-scroll");
                typeList.empty();

                if (response.code === 200 && response.data.length > 0) {
                    let data = response.data;

                    $.each(data, function (index, value) {
                        typeList.append(`
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2 vehicle_type_checkbox" type="checkbox" value="${value.id}">${value.name}
                            </label>
                        </li>
                    `);
                    });
                } else {
                    typeList.append(
                        `<li class="text-center p-2">${_l(
                            "admin.common.no_data_found"
                        )}</li>`
                    );
                }
            },
            error: function (error) {
                if (error.responseJSON.code === 500) {
                    showToast("error", error.responseJSON.message);
                } else {
                    showToast(
                        "error",
                        _l("admin.common.default_retrieve_error")
                    );
                }
            },
        });
    }

    let startDate = "";
    let startTime = "";
    let endDate = "";
    let endTime = "";
    let tariff = "";
    let pickup_location_val = "";
    let return_location_val = "";
    let no_of_days = "";
    let no_of_months = "";
    let no_of_years = "";

    $(document).on(
        "blur",
        "#start_date, #end_date, #start_time, #end_time",
        function () {
            startDate = $("#start_date").val();
            endDate = $("#end_date").val();
            startTime = $("#start_time").val();
            endTime = $("#end_time").val();

            $("#start_date, #end_date, #start_time, #end_time").valid();
            checkAndFetchVehicles();
        }
    );

    $("#driving_type").on("change", function () {
        $(".summary_driving_type").text($(this).find("option:selected").text());
    });

    $("#pickup_location").on("change", function () {
        let pickup_location = $(this).find("option:selected").text();
        pickup_location_val = $(this).val();
        $(this).valid();
        $("#return_same_location").prop("checked", false);
        if (pickup_location_val != "") {
            $(".summary_pickup_location").text(pickup_location);
        }
        checkAndFetchVehicles();
    });

    $("#return_location").on("change", function () {
        let return_location = $(this).find("option:selected").text();
        return_location_val = $(this).val();
        $(this).valid();
        if (return_location_val != "") {
            $(".summary_return_location").text(return_location);
        }
        checkAndFetchVehicles();
    });

    function checkAndFetchVehicles() {
        updateSummaryDate(".summary_start_date", startDate, startTime);
        updateSummaryDate(".summary_end_date", endDate, endTime);

        if (!areDatesAndTimesValid()) {
            $(".summary_rental_period").text("-");
            return;
        }

        const startDateTime = moment(`${startDate} ${startTime}`, "DD-MM-YYYY HH:mm");
        const endDateTime = moment(`${endDate} ${endTime}`, "DD-MM-YYYY HH:mm");
        const diffMinutes = endDateTime.diff(startDateTime, "minutes");

        if (!validateDuration(diffMinutes)) return;

        const no_of_days = calculateNumberOfDays(startDateTime, endDateTime, diffMinutes);
        $(".summary_rental_period").text(formatRentalPeriod(no_of_days));

        const no_of_months = calculateNumberOfMonths(startDateTime, endDateTime);
        const no_of_years = calculateNumberOfYears(startDate, endDate);

        if (shouldFetchVehicles()) {
            $("#vehicle_list_main_container").removeClass("d-none");
            lastPage = false;
            currentPage = 1;
            getVehicles();
        }
    }

    // --- Helper Functions ---

    function updateSummaryDate(selector, date, time) {
        $(selector).text(date && time ? `${date} ${time}` : "-");
    }

    function areDatesAndTimesValid() {
        return startDate && startTime && endDate && endTime;
    }

    function validateDuration(diffMinutes) {
        if (diffMinutes < 60) {
            $("#end_date, #end_time").addClass("is-invalid").removeClass("is-valid");
            $("#end_date_error").text(_l("admin.bookings.duration_must_be_atleast_one_hour"));
            $(".summary_rental_period").text("-");
            $("#vehicle_list_main_container").addClass("d-none");
            return false;
        }
        $("#end_date, #end_time").removeClass("is-invalid");
        $("#end_date_error").text("");
        return true;
    }

    function calculateNumberOfDays(start, end, diffMinutes) {
        if (start.isSame(end) || start.format("DD-MM-YYYY") === end.format("DD-MM-YYYY") || diffMinutes === 1440) {
            return 1;
        } else if (diffMinutes > 1440 && diffMinutes <= 2880) {
            return 2;
        } else {
            return Math.ceil(diffMinutes / 1440);
        }
    }

    function formatRentalPeriod(days) {
        return `${days} day${days > 1 ? "s" : ""}`.trim();
    }

    function calculateNumberOfMonths(start, end) {
        let months = 0;
        let temp = start.clone();
        while (temp.isBefore(end, "month")) {
            const daysInMonth = temp.daysInMonth();
            if (temp.add(daysInMonth, "days").isAfter(end)) break;
            months++;
        }
        return months;
    }

    function calculateNumberOfYears(startDate, endDate) {
        let years = 0;
        let startMoment = moment(startDate, "DD-MM-YYYY");
        let endMoment = moment(endDate, "DD-MM-YYYY");
        let remainingDays = endMoment.diff(startMoment, "days");
        let daysInYear = 365;

        while (remainingDays >= daysInYear) {
            years++;
            remainingDays -= daysInYear;
            if (startMoment.isLeapYear()) daysInYear = 366;
        }

        if (remainingDays > 0) years++;
        return years;
    }

    function shouldFetchVehicles() {
        return startDate && startTime && endDate && endTime && pickup_location_val && return_location_val;
    }

    $(document).on("change", "#tariff", function () {
        tariff = $(this).find("option:selected").text().toLowerCase();

        let todayDateTime = moment();
        let lastDateTime;

        if (tariff === "daily") {
            lastDateTime = todayDateTime
                .clone()
                .add(1, "days")
                .subtract(1, "seconds");
            $("#start_date, #end_date, #start_time, #end_time").attr(
                "readonly",
                false
            );
        } else if (tariff === "weekly") {
            lastDateTime = todayDateTime
                .clone()
                .add(1, "weeks")
                .subtract(1, "seconds");
            $("#start_date, #end_date, #start_time, #end_time").attr(
                "readonly",
                true
            );
        } else if (tariff === "monthly") {
            lastDateTime = todayDateTime
                .clone()
                .add(1, "months")
                .subtract(1, "seconds");
            $("#start_date, #end_date, #start_time, #end_time").attr(
                "readonly",
                true
            );
        } else if (tariff === "yearly") {
            lastDateTime = todayDateTime
                .clone()
                .add(1, "years")
                .subtract(1, "seconds");
            $("#start_date, #end_date, #start_time, #end_time").attr(
                "readonly",
                true
            );
        }

        if ($(this).val() == "") {
            tariff = "";
            startDate = "";
            endDate = "";
            startTime = "";
            endTime = "";
            $("#start_date").val(startDate).removeAttr("readonly");
            $("#end_date").val(endDate).removeAttr("readonly");
            $("#start_time").val(startTime).removeAttr("readonly");
            $("#end_time").val(endTime).removeAttr("readonly");
            $("#basicInfoForm")[0].reset();
            $("#pickup_location").val("").trigger("change");
            $("#return_location").val("").trigger("change");
            $("#driving_type").val("").trigger("change");
            $(".error-text").text("");
            $(".form-control, .select2-container").removeClass(
                "is-invalid is-valid"
            );
            $("#vehicle_list_main_container").addClass("d-none");
            return;
        } else {
            startDate = todayDateTime.format("DD-MM-YYYY");
            endDate = lastDateTime.format("DD-MM-YYYY");
            startTime = todayDateTime.format("HH:mm");
            endTime = lastDateTime.format("HH:mm");

            $("#start_date").val(startDate);
            $("#end_date").val(endDate);
            $("#start_time").val(startTime);
            $("#end_time").val(endTime);
            $("#basicInfoForm").valid();
        }

        lastPage = false;
        currentPage = 1;
        checkAndFetchVehicles();
    });

    $("#return_same_location").on("click", function () {
        let is_return_same_location = $(this).is(":checked");
        let pickup_location = $("#pickup_location").val();
        if (pickup_location != "" && is_return_same_location) {
            $("#return_location").val(pickup_location).trigger("change");
        } else {
            $("#return_location").val("").trigger("change");
            $(".summary_return_location").text("");
        }
    });

    $(document).on("keyup", "#brand_search", function () {
        let search = $(this).val().trim();
        getBrands(search);
    });

    $(document).on("keyup", "#type_search", function () {
        let search = $(this).val().trim();
        getTypes(search);
    });

    $(document).on("keyup", "#model_search", function () {
        let search = $(this).val().trim();
        getModels(search);
    });

    $(document).on("keyup", "#color_search", function () {
        let search = $(this).val().trim();
        getColors(search);
    });

    $(document).on("click", "#apply_filter", function () {
        let brand_ids = [];
        let type_ids = [];
        let model_ids = [];
        let color_ids = [];

        $(".brand_checkbox:checked").each(function () {
            brand_ids.push($(this).val());
        });

        $(".vehicle_type_checkbox:checked").each(function () {
            type_ids.push($(this).val());
        });

        $(".model_checkbox:checked").each(function () {
            model_ids.push($(this).val());
        });

        $(".color_checkbox:checked").each(function () {
            color_ids.push($(this).val());
        });

        let filterData = {
            brand_ids: brand_ids,
            type_ids: type_ids,
            model_ids: model_ids,
            color_ids: color_ids,
        };
        lastPage = false;
        currentPage = 1;
        getVehicles(filterData);
    });

    $(document).on("click", "#reset_filter", function () {
        $("#brandList input:checkbox").prop("checked", false);
        $("#modelList input:checkbox").prop("checked", false);
        $("#colorList input:checkbox").prop("checked", false);
        $("#typeList input:checkbox").prop("checked", false);
        lastPage = false;
        currentPage = 1;
        getVehicles();
    });

    $(document).on("keyup", "#overall_search", function () {
        let search = $(this).val();
        let filterData = {
            search: search,
        };
        lastPage = false;
        currentPage = 1;
        getVehicles(filterData);
    });

    let currentPage = 1;
    let lastPage = false;
    let totalPage = "";
    let isFetching = false;

    function getVehicles(filterData = {}, isLoadMore = false) {
        if (lastPage || isFetching) return;

        isFetching = true;

        let extraData = {
            start_date: startDate,
            start_time: startTime,
            end_date: endDate,
            end_time: endTime,
            tariff: tariff,
            pickup_location: pickup_location_val,
            return_location: return_location_val,
            page: currentPage,
            per_page: 10,
        };

        filterData = $.extend(filterData, extraData);

        $.ajax({
            url: "/get-filter-vehicles",
            type: "POST",
            data: JSON.stringify(filterData),
            contentType: "application/json",
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            beforeSend: function () {
                if (!isLoadMore) {
                    $(".list-loader").show();
                    $(".card-loader").show();
                    $("#vehicle_list_container").addClass("d-none");
                }
            },
            success: function (result) {
                if (result.data && result.data.data.length > 0) {
                    let data = result.data.data;
                    lastPage =
                        result.data.current_page >= result.data.last_page;

                    if (!isLoadMore) {
                        $("#vehicle_list_container").empty();
                    }

                    data.forEach((item) => {
                        const cardDiv = $("<div>")
                            .addClass("card vehicle-card mb-2")
                            .attr("id", `vehicle_${item.id}`)
                            .attr("data-image", item.image)
                            .attr("data-type", item.vehicle_type)
                            .attr("data-name", item.vehicle_name)
                            .attr("data-price", item.vehicle_price)
                            .attr("data-price_type", item.vehicle_price_type);

                        const cardBody = $("<div>").addClass("card-body");
                        const rowDiv = $("<div>").addClass("row gy-3");

                        // Column 1: Vehicle info
                        const col1 = $("<div>").addClass("col-3");
                        const alignDiv = $("<div>").addClass(
                            "d-flex align-items-center"
                        );

                        const formCheck = $("<div>").addClass(
                            "form-check form-check-md me-3"
                        );
                        const radioInput = $("<input>", {
                            class: "form-check-input vehicle_select",
                            name: "vehicle_id",
                            id: `vehicle_select_${item.id}`,
                            type: "radio",
                            value: item.id,
                            "data-price": item.vehicle_price,
                            "data-price_type": item.vehicle_price_type,
                            "data-vehicle_name": item.vehicle_name,
                            "data-vehicle_season_id":
                                item.vehicle_season_id || "",
                            "data-vehicle_tariff_id":
                                item.vehicle_tariff_id || "",
                        });
                        formCheck.append(radioInput);

                        const avatarSpan = $("<span>").addClass(
                            "avatar flex-shrink-0 me-2"
                        );
                        $("<img>", {
                            src: item.image,
                            class: "admin-vehicle-image",
                            alt: "vehicle image",
                        }).appendTo(avatarSpan);

                        const vehicleInfo = $("<div>");
                        $("<p>")
                            .addClass("mb-1")
                            .text(item.vehicle_type)
                            .appendTo(vehicleInfo);
                        $("<h6>")
                            .addClass("fs-14")
                            .text(item.vehicle_name)
                            .appendTo(vehicleInfo);

                        alignDiv.append(formCheck, avatarSpan, vehicleInfo);
                        col1.append(alignDiv);

                        // Column 2: Color, Year, Price
                        const col2 = $("<div>").addClass("col-6");
                        const gapDiv = $("<div>").addClass("d-flex gy-3 gap-5");

                        // Color
                        const colorDiv = $("<div>");
                        $("<p>")
                            .addClass("mb-1")
                            .text(_l("admin.common.color"))
                            .appendTo(colorDiv);
                        const colorH6 = $("<h6>").addClass(
                            "fs-14 d-inline-flex align-items-center"
                        );
                        $("<i>", {
                            class: "ti ti-square-filled me-1",
                            style: `color: ${item.color_value}`,
                        }).appendTo(colorH6);
                        colorH6.append(
                            document.createTextNode(item.color_name)
                        );
                        colorDiv.append(colorH6);
                        gapDiv.append(colorDiv);

                        // Year
                        const yearDiv = $("<div>");
                        $("<p>")
                            .addClass("mb-1")
                            .text(_l("admin.common.year"))
                            .appendTo(yearDiv);
                        $("<h6>")
                            .addClass("fs-14")
                            .text(item.year)
                            .appendTo(yearDiv);
                        gapDiv.append(yearDiv);

                        // Price
                        const priceDiv = $("<div>");
                        $("<p>")
                            .addClass("mb-1")
                            .text(_l("admin.common.price"))
                            .appendTo(priceDiv);
                        $("<h6>")
                            .addClass("fs-14")
                            .html(
                                `${default_currency}${item.vehicle_price}<span class="text-gray-5 mt-1">/${item.vehicle_price_type}</span>`
                            ).appendTo(priceDiv);
                        priceDiv.appendTo(gapDiv);
                        col2.append(gapDiv);

                        // Column 3: Model name
                        const col3 = $("<div>").addClass("col-3");
                        const modelDiv = $("<div>").addClass("float-md-start");
                        $("<span>")
                            .addClass(
                                "badge bg-orange-transparent d-inline-flex align-items-center badge-sm mb-1"
                            )
                            .html(
                                `<i class="ti ti-point-filled me-1"></i>${_l(
                                    "admin.common.available"
                                )}`
                            )
                            .appendTo(modelDiv);
                        $("<h6>")
                            .addClass("fs-14")
                            .text(item.model_name)
                            .appendTo(modelDiv);
                        col3.append(modelDiv);

                        rowDiv.append(col1, col2, col3);
                        cardBody.append(rowDiv);
                        cardDiv.append(cardBody);

                        $("#vehicle_list_container").append(cardDiv);
                    });

                    currentPage = result.data.current_page;
                    totalPage = result.data.last_page;
                } else if (!isLoadMore) {
                    $("#vehicle_list_container").html(`
                        <div class="row">
                            <span class="text-center mb-3">${_l(
                                "admin.bookings.no_vehicles_found"
                            )}</span>
                        </div>
                    `);
                }
            },
            error: function (error) {
                if (error.responseJSON.code === 500) {
                    showToast("errror", error.responseJSON.message);
                } else {
                    showToast(
                        "errror",
                        _l("admin.common.default_retrieve_error")
                    );
                }
            },
            complete: function () {
                isFetching = false;
                $(
                    ".list-loader, .label-loader, .card-loader, .table-loader"
                ).hide();
                $(
                    "#vehicle_list_container, .real-table, .real-label, .real-input"
                ).removeClass("d-none");
            },
        });
    }

    $("#vehicle_list_container").on("scroll", function () {
        let container = $(this);
        let scrollTop = container.scrollTop();
        let containerHeight = container.innerHeight();
        let scrollHeight = container[0].scrollHeight;
        let threshold = 350;

        if (
            scrollTop + containerHeight >= scrollHeight - threshold &&
            !isFetching
        ) {
            if (currentPage < totalPage) {
                currentPage++;
                getVehicles({}, true);
            }
        }
    });

    let selected_driver_id = "";
    let selected_extra_service_ids = [];
    let selected_vehicle_ids = [];
    let selected_insurance_ids = [];

    $("#customer_id").on("change", function () {
        let customerId = $(this).val();

        if (customerId !== "") {
            $(this).valid();
        }

        $.ajax({
            url: "/get-customer-details",
            type: "POST",
            data: {
                customer_id: customerId,
            },
            dataType: "json",
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.code === 200 && response.data) {
                    let data = response.data;

                    let safeImage = DOMPurify.sanitize(data.profile_image);
                    let fullName = DOMPurify.sanitize(data.full_name || "-");
                    let phoneNumber = DOMPurify.sanitize(
                        data.phone_number || "-"
                    );
                    let email = DOMPurify.sanitize(data.email || "-");
                    let bookingsCount = parseInt(data.bookings_count) || 0;

                    let $card = $("<div>", {
                        class: "card bg-light",
                        id: "customer_detail",
                        "data-image": safeImage,
                        "data-name": fullName,
                        "data-phone": phoneNumber,
                    });

                    let $cardBody = $("<div>").addClass("card-body");
                    let $row = $("<div>").addClass(
                        "row align-items-center gy-3"
                    );

                    let $infoCol = $("<div>").addClass("col-md-11");
                    let $infoInnerRow = $("<div>").addClass("row gx-2 gy-3");

                    // --- Profile image & name ---
                    let $profileCol = $("<div>").addClass("col-md-4");
                    let $profileWrapper = $("<div>").addClass(
                        "d-flex align-items-center"
                    );

                    let $img = $("<img>", {
                        src: safeImage,
                        alt: "Profile Image",
                        loading: "lazy",
                    });

                    let $avatar = $("<span>")
                        .addClass("avatar avatar-rounded flex-shrink-0 me-2")
                        .append($img);

                    let $profileInfo = $("<div>")
                        .append($("<h6>").addClass("fs-14 mb-1").text(fullName))
                        .append(
                            $("<span>")
                                .addClass("badge bg-info-transparent")
                                .text(
                                    `${bookingsCount} ${_l(
                                        "admin.bookings.bookings"
                                    )}`
                                )
                        );

                    $profileWrapper.append($avatar, $profileInfo);
                    $profileCol.append($profileWrapper);

                    // --- Phone ---
                    let $phoneCol = $("<div>")
                        .addClass("col-md-4")
                        .append(
                            $("<div>").append(
                                $("<h6>")
                                    .addClass("fs-14 mb-1")
                                    .text(_l("admin.common.phone")),
                                $("<p>").text(phoneNumber)
                            )
                        );

                    // --- Email ---
                    let $emailCol = $("<div>")
                        .addClass("col-md-4")
                        .append(
                            $("<div>").append(
                                $("<h6>")
                                    .addClass("fs-14 mb-1")
                                    .text(_l("admin.common.email")),
                                $("<p>").text(email)
                            )
                        );

                    $infoInnerRow.append($profileCol, $phoneCol, $emailCol);
                    $infoCol.append($infoInnerRow);

                    // --- Remove Button ---
                    let $removeCol = $("<div>").addClass("col-md-1");
                    let $removeWrapper = $("<div>").addClass(
                        "d-flex align-items-center justify-content-end"
                    );
                    let $removeBtn = $("<button>", {
                        type: "button",
                        class: "btn border-0 bg-transparent",
                        id: "remove_customer",
                    }).append($("<i>").addClass("ti ti-trash"));

                    $removeWrapper.append($removeBtn);
                    $removeCol.append($removeWrapper);

                    $row.append($infoCol, $removeCol);
                    $cardBody.append($row);
                    $card.append($cardBody);

                    $("#customer_details_list").empty().append($card);
                }
            },
            error: function (error) {
                if (error.responseJSON.code === 500) {
                    showToast("errror", error.responseJSON.message);
                } else {
                    showToast(
                        "errror",
                        _l("admin.common.default_retrieve_error")
                    );
                }
            },
        });
    });

    $(document).on("click", "#remove_customer", function () {
        $("#customer_id").val("").trigger("change");
        $("#customer_details_list").html("");
    });

    $("#driver_id").on("change", function () {
        let driver_id = $(this).val();
        selected_driver_id = driver_id;

        if (!driver_id) {
            $(this).valid();
            return;
        }

        $.ajax({
            url: "/get-driver-details",
            type: "POST",
            data: {
                driver_id: driver_id,
            },
            dataType: "json",
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.code === 200 && response.data) {
                    let data = response.data;

                    // --- Helpers ---
                    const safeText = (value) => DOMPurify.sanitize(value ?? "");
                    const safePhone = safeText(data.phone_number);
                    const safeDriverName = safeText(data.driver_name);

                    // --- Edit Price Button ---
                    let $editBtnWrapper = $("<div>").addClass(
                        "d-flex align-items-center justify-content-end mb-3"
                    );
                    let $editBtn = $("<button>", {
                        type: "button",
                        class: "text-purple text-decoration-underline fw-medium edit_driver_price border-0 bg-transparent",
                        "data-bs-toggle": "modal",
                        "data-bs-target": "#edit_price_modal",
                        "data-image": DOMPurify.sanitize(data.image ?? ""),
                        "data-driver_name": safeDriverName,
                        "data-phone": safePhone,
                        "data-price": 0,
                    }).text(_l("admin.bookings.edit_price"));
                    $editBtnWrapper.append($editBtn);

                    // --- Driver Card ---
                    let $card = $("<div>", {
                        class: "card bg-light",
                        id: "driver_detail",
                    }).data({
                        image: DOMPurify.sanitize(data.image ?? ""),
                        name: safeDriverName,
                        phone: safePhone,
                    });

                    let $cardBody = $("<div>").addClass("card-body");
                    let $row = $("<div>").addClass(
                        "row align-items-center gy-3"
                    );

                    let $leftCol = $("<div>").addClass("col-md-11");
                    let $innerRow = $("<div>").addClass("row gx-2 gy-3");

                    // --- Driver profile ---
                    let $profileCol = $("<div>").addClass("col-md-5");
                    let $profileWrap = $("<div>").addClass(
                        "d-flex align-items-center"
                    );
                    let $img = $("<img>")
                        .attr("src", DOMPurify.sanitize(data.image ?? ""))
                        .attr("alt", "Driver");
                    let $avatar = $("<span>")
                        .addClass("avatar avatar-rounded flex-shrink-0 me-2")
                        .append($img);
                    let $profileInfo = $("<div>")
                        .append(
                            $("<h6>")
                                .addClass("fs-14 mb-1")
                                .text(safeDriverName)
                        )
                        .append(
                            $("<span>")
                                .addClass("badge bg-violet-transparent")
                                .text(`0 ${_l("admin.bookings.rides")}`)
                        );
                    $profileWrap.append($avatar, $profileInfo);
                    $profileCol.append($profileWrap);

                    // --- Phone ---
                    let $phoneCol = $("<div>")
                        .addClass("col-md-4")
                        .append(
                            $("<div>")
                                .append(
                                    $("<h6>")
                                        .addClass("fs-14 mb-1")
                                        .text(_l("admin.common.phone"))
                                )
                                .append($("<p>").text(safePhone || "-"))
                        );

                    // --- Price ---
                    let $priceCol = $("<div>")
                        .addClass("col-md-3")
                        .append(
                            $("<div>")
                                .append(
                                    $("<h6>")
                                        .addClass("fs-14 mb-1")
                                        .text(_l("admin.common.price"))
                                )
                                .append(
                                    $("<p>").append(
                                        document.createTextNode(
                                            default_currency
                                        ),
                                        $("<span>")
                                            .addClass("td-driver-price")
                                            .text(0)
                                    )
                                )
                        );

                    $innerRow.append($profileCol, $phoneCol, $priceCol);
                    $leftCol.append($innerRow);

                    // --- Remove Button ---
                    let $removeCol = $("<div>").addClass("col-md-1");
                    let $removeWrap = $("<div>").addClass(
                        "d-flex align-items-center justify-content-end"
                    );
                    let $removeBtn = $("<button>", {
                        type: "button",
                        class: "btn border-0 bg-transparent",
                        id: "remove_driver",
                    }).append($("<i>").addClass("ti ti-trash"));
                    $removeWrap.append($removeBtn);
                    $removeCol.append($removeWrap);

                    $row.append($leftCol, $removeCol);
                    $cardBody.append($row);
                    $card.append($cardBody);

                    $("#driver_details_list")
                        .empty()
                        .append($editBtnWrapper, $card);
                }
            },
            error: function (error) {
                if (error.responseJSON.code === 500) {
                    showToast("errror", error.responseJSON.message);
                } else {
                    showToast(
                        "error",
                        _l("admin.common.default_retrieve_error")
                    );
                }
            },
        });
    });

    $(document).on("click", "#remove_driver", function () {
        $("#driver_id").val("").trigger("change");
        $("#driver_details_list").html("");
    });

    $(document).on("click", ".edit_driver_price", function () {
        let driver_image = $(this).data("image");
        let driver_name = $(this).data("driver_name");
        let driver_price = $(this).data("price");

        $(".edit_driver_img").attr("src", driver_image);
        $(".edit_driver_name").text(driver_name);
        if (driver_price !== 0 && driver_price !== "") {
            $("#driver_price").val(driver_price);
        } else {
            $("#driver_price").val("0");
            $(".td-driver-price").text("0");
        }
    });

    $("#driverPriceForm").submit(function (e) {
        e.preventDefault();
        if ($("#driver_price").val() === "") {
            $("#driver_price").val("0");
            $(".td-driver-price").text("0");
        } else {
            $(".edit_driver_price").data("price", $("#driver_price").val());
            $(".td-driver-price").text($("#driver_price").val());
        }
        $("#edit_price_modal").modal("hide");
        $("#add_booking").modal("show");
    });

    $("#driver_price").on("input", function () {
        $(this).val(
            $(this)
                .val()
                .replace(/\D/g, "")
        );
    });

    function getDrivers() {
        $.ajax({
            url: "/get-drivers",
            type: "POST",
            data: {
                vehicle_ids: selected_vehicle_ids,
            },
            dataType: "json",
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (result) {
                if (result.data && result.data.length > 0) {
                    let data = result.data;

                    $("#driver_id").find("option:not(:first)").remove();

                    data.forEach((item) => {
                        const option = $("<option>", {
                            value: item.id,
                            text: item.driver_name,
                        });

                        if (item.id == selected_driver_id) {
                            option.prop("selected", true);
                        }

                        $("#driver_id").append(option);
                    });
                }
            },
            error: function (error) {
                if (error.responseJSON.code === 500) {
                    showToast("error", error.responseJSON.message);
                } else {
                    showToast(
                        "error",
                        _l("admin.common.default_retrieve_error")
                    );
                }
            },
        });
    }

    $("#security_deposit").on("input", function () {
        $(this).val(
            $(this)
                .val()
                .replace(/\D/g, "")
        );
    });

    $("#no_of_passengers").on("input", function () {
        $(this).val(
            $(this)
                .val()
                .replace(/\D/g, "")
        );
    });

    $("#basicInfoForm").validate({
        rules: {
            start_date: {
                required: true,
                dateLessThan: true,
            },
            start_time: {
                required: true,
            },
            end_date: {
                required: true,
                dateGreaterThan: true,
            },
            end_time: {
                required: true,
            },
            return_location: {
                required: true,
            },
            pickup_location: {
                required: true,
            },
        },
        messages: {
            start_date: {
                required: _l("admin.bookings.start_date_required"),
                dateLessThan: _l(
                    "admin.bookings.start_date_less_than_equal_to_end_date"
                ),
            },
            start_time: {
                required: _l("admin.bookings.start_time_required"),
            },
            end_date: {
                required: _l("admin.bookings.end_date_required"),
                dateGreaterThan: _l(
                    "admin.bookings.end_date_greater_than_equal_to_start_date"
                ),
            },
            end_time: {
                required: _l("admin.bookings.end_time_required"),
            },
            return_location: {
                required: _l("admin.bookings.return_location_required"),
            },
            pickup_location: {
                required: _l("admin.bookings.pickup_location_required"),
            },
        },
        errorPlacement: function (error, element) {
            if (element.hasClass("select2-hidden-accessible")) {
                const errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            }
        },
        highlight: function (element) {
            if ($(element).hasClass("select2-hidden-accessible")) {
                $(element)
                    .next(".select2-container")
                    .addClass("is-invalid")
                    .removeClass("is-valid");
            }
            $(element).addClass("is-invalid").removeClass("is-valid");
            $("#" + element.id)
                .siblings("span")
                .addClass("me-3");
        },
        unhighlight: function (element) {
            if ($(element).hasClass("select2-hidden-accessible")) {
                $(element)
                    .next(".select2-container")
                    .removeClass("is-invalid")
                    .addClass("is-valid");
            }
            $(element).removeClass("is-invalid").addClass("is-valid");
            $("#" + element.id)
                .siblings("span")
                .addClass("me-3");
            const errorId = element.id + "_error";
            $("#" + errorId).text("");
        },
        onkeyup: function (element) {
            $(element).valid();
        },
        onchange: function (element) {
            $(element).valid();
        },
    });

    $.validator.addMethod(
        "dateLessThan",
        function (value, element, param) {
            let startDate = moment($("#start_date").val(), "DD-MM-YYYY");
            let endDate = moment($("#end_date").val(), "DD-MM-YYYY");
            return startDate.isSameOrBefore(endDate);
        },
        "Start date must be less than or equal to end date."
    );

    $.validator.addMethod(
        "dateGreaterThan",
        function (value, element) {
            let startDate = moment($("#start_date").val(), "DD-MM-YYYY");
            let endDate = moment($("#end_date").val(), "DD-MM-YYYY");
            return endDate.isSameOrAfter(startDate);
        },
        "End date must be greater than or equal to start date."
    );

    $("#basic_info_btn").on("click", function (event) {
        event.preventDefault();

        selected_vehicle_ids = [];

        $(".vehicle_select").each(function () {
            let isVehicle = $(this).is(":checked");
            if (isVehicle) {
                selected_vehicle_ids.push($(this).val());
            }
        });

        if ($("#basicInfoForm").valid()) {
            if (selected_vehicle_ids.length > 0) {
                getDrivers();

                $.each(selected_vehicle_ids, function (index, item) {
                    let vehicle_image = $("#vehicle_" + item).data("image");
                    let vehicle_name = $("#vehicle_" + item).data("name");
                    let vehicle_type = $("#vehicle_" + item).data("type");
                    let vehicle_price = $("#vehicle_" + item).data("price");
                    let vehicle_price_type = $("#vehicle_" + item).data(
                        "price_type"
                    );

                    $("#customer_summary").append(`
                    <div class="border rounded p-3 bg-light mb-3">
                        <div class="row">
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <span class="avatar flex-shrink-0 me-2">
                                        <img src="${vehicle_image}" alt="">
                                    </span>
                                    <div>
                                        <p class="mb-0">${vehicle_type}</p>
                                        <h6 class="fs-14">${vehicle_name}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-end">
                                    <p class="mb-0">${_l(
                                        "admin.common.price"
                                    )}</p>
                                    <h6 class="fs-14">${default_currency}${vehicle_price}<span class="text-gray-5">/${vehicle_price_type}</span></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
                });

                let clonedDiv = $("#basic_info_summary").children().clone();
                $("#customer_summary").append(clonedDiv);

                $("#first-field").hide();
                $("#second-field").show();
            } else {
                showToast("error", _l("admin.bookings.vehicle_required"));
            }
        }
    });

    let vehiclePrice = "";
    let vehiclePriceType = "";

    $(document).on("click", ".vehicle_select", function () {
        $("#driver_details_list").empty();
        selected_driver_id = "";
        selected_extra_service_ids = [];
        selected_vehicle_ids = [];
        selected_insurance_ids = [];
        vehiclePrice = $(this).data("price");
        vehiclePriceType = $(this).data("price_type");
    });

    $("#customer_prev_btn").on("click", function (e) {
        e.preventDefault();
        $("#first-field").show();
        $("#second-field").hide();
        $(".error-text").text("");
        $(".form-control, .select2-container").removeClass(
            "is-invalid is-valid"
        );
        $("#customer_summary").empty();
    });

    $("#customerForm").validate({
        rules: {
            customer_id: {
                required: true,
            },
            driver_id: {
                required: false,
            },
        },
        messages: {
            customer_id: {
                required: _l("admin.bookings.customer_required"),
            },
            driver_id: {
                required: _l("admin.bookings.driver_required"),
            },
        },
        errorPlacement: function (error, element) {
            if (element.hasClass("select2-hidden-accessible")) {
                const errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            }
        },
        highlight: function (element) {
            if ($(element).hasClass("select2-hidden-accessible")) {
                $(element)
                    .next(".select2-container")
                    .addClass("is-invalid")
                    .removeClass("is-valid");
            }
            $(element).addClass("is-invalid").removeClass("is-valid");
        },
        unhighlight: function (element) {
            if ($(element).hasClass("select2-hidden-accessible")) {
                $(element)
                    .next(".select2-container")
                    .removeClass("is-invalid")
                    .addClass("is-valid");
            }
            $(element).removeClass("is-invalid").addClass("is-valid");
            const errorId = element.id + "_error";
            $("#" + errorId).text("");
        },
        onkeyup: function (element) {
            $(element).valid();
        },
        onchange: function (element) {
            $(element).valid();
        },
    });

    function calculateVehiclePrice() {
        let vehiclePriceRate = vehiclePrice;
        const driverPriceVal = parseFloat($("#driver_price").val()) || 0;
        const securityDeposit =
            $("#security_deposit").val() !== ""
                ? parseFloat($("#security_deposit").val())
                : 0;
        const response = [];

        if (vehiclePriceType === "daily") {
            vehiclePriceRate = no_of_days * vehiclePrice;
        } else if (vehiclePriceType === "weekly") {
            vehiclePriceRate = Math.ceil(no_of_days / 7) * vehiclePrice;
        } else if (vehiclePriceType === "monthly") {
            vehiclePriceRate = no_of_months * vehiclePrice;
        } else if (vehiclePriceType === "yearly") {
            vehiclePriceRate = no_of_years * vehiclePrice;
        } else {
            vehiclePriceRate = no_of_days * vehiclePrice;
        }

        let total_extra_service_price = 0;
        let total_extra_service = 0;
        let extraServiceName = "";
        let isExtraService;
        if ($(".vehicle_extra_service").length > 0) {
            $(".vehicle_extra_service").each(function () {
                isExtraService = $(this).is(":checked");
                if (isExtraService) {
                    if ($(this).data("price_type") == "per_day") {
                        total_extra_service_price +=
                            no_of_days * parseFloat($(this).data("price"));
                        total_extra_service++;
                    } else if ($(this).data("price_type") == "one_time") {
                        total_extra_service_price += parseFloat(
                            $(this).data("price")
                        );
                        total_extra_service++;
                    } else if ($(this).data("price_type") == "percentage") {
                        total_extra_service_price +=
                            (vehiclePriceRate *
                                parseFloat($(this).data("price"))) /
                            100;
                        total_extra_service++;
                    }
                    extraServiceName += $(this).data("name") + ", ";
                }
            });
        }

        let total_insurance_price = 0;
        let total_insurance = 0;
        let insuranceName = "";
        if ($(".vehicle_insurance").length > 0) {
            $(".vehicle_insurance").each(function () {
                if ($(this).is(":checked")) {
                    if ($(this).data("price_type") == "daily") {
                        total_insurance_price +=
                            no_of_days * parseFloat($(this).data("price"));
                        total_insurance++;
                    } else if ($(this).data("price_type") == "fixed") {
                        total_insurance_price += parseFloat(
                            $(this).data("price")
                        );
                        total_insurance++;
                    } else if ($(this).data("price_type") == "percentage") {
                        total_insurance_price +=
                            (vehiclePriceRate *
                                parseFloat($(this).data("price"))) /
                            100;
                        total_insurance++;
                    }
                    insuranceName += $(this).data("name") + ", ";
                }
            });
        }

        vehiclePriceRate = parseFloat(vehiclePriceRate).toFixed(2);
        driverPriceVal = parseFloat(driverPriceVal).toFixed(2);
        securityDeposit = parseFloat(securityDeposit).toFixed(2);
        total_insurance_price = parseFloat(total_insurance_price).toFixed(2);
        total_extra_service_price = parseFloat(
            total_extra_service_price
        ).toFixed(2);

        let totalPriceVal = (
            parseFloat(driverPriceVal) +
            parseFloat(securityDeposit) +
            parseFloat(vehiclePriceRate) +
            parseFloat(total_extra_service_price)
        ).toFixed(2);

        let totalPriceVal2 = (
            parseFloat(driverPriceVal) +
            parseFloat(securityDeposit) +
            parseFloat(vehiclePriceRate) +
            parseFloat(total_extra_service_price) +
            parseFloat(total_insurance_price)
        ).toFixed(2);

        response.push({
            vehicle_price_rate: vehiclePriceRate,
            vehicle_price_type: vehiclePriceType,
            driver_price: driverPriceVal,
            security_deposit: securityDeposit,
            total_extra_service_price: total_extra_service_price,
            total_extra_service: total_extra_service,
            total_price_val: totalPriceVal,
            total_price: totalPriceVal2,
            total_insurance_price: total_insurance_price,
            total_insurance: total_insurance,
            insurance_name: insuranceName,
            extra_service_name: extraServiceName,
        });

        return response;
    }

    $("#customer_next_btn").on("click", function (event) {
        event.preventDefault();

        if ($("#customerForm").valid()) {
            getExtraServices();

            let clonedDiv = $("#customer_summary").children().clone();
            $("#extra_service_summary").append(clonedDiv);

            let customer_image = $("#customer_detail").data("image");
            let customer_name = $("#customer_detail").data("name");
            let customer_phone = $("#customer_detail").data("phone");
            let driver_image = $("#driver_detail").data("image") ?? "";
            let driver_name = $("#driver_detail").data("name") ?? "";
            let driver_phone = $("#driver_detail").data("phone") ?? "";

            const totalAmount = calculateVehiclePrice();

            $("#extra_service_summary").append(`
            <div class="border-bottom mb-3 border-top pt-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="mb-3">
                        <h6 class="d-inline-flex align-items-center fs-14 fw-medium ">${_l(
                            "admin.common.customer"
                        )}<a href="#" class="ms-2 d-none"><i class="ti ti-edit"></i></a></h6>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <span class="avatar avatar-rounded flex-shrink-0 me-2">
                            <img src="${customer_image}" alt="">
                        </span>
                        <div>
                            <h6 class="fs-14 fw-medium mb-1">${customer_name}</h6>
                            <p>${customer_phone}</p>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between ${
                    typeof driver_name !== "undefined" && driver_name !== ""
                        ? ""
                        : "d-none"
                }">
                    <div class="mb-3">
                        <h6 class="d-inline-flex align-items-center fs-14 fw-medium ">${_l(
                            "admin.common.driver"
                        )}<a href="#" class="ms-2 d-none"><i class="ti ti-edit"></i></a></h6>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <span class="avatar avatar-rounded flex-shrink-0 me-2">
                            <img src="${driver_image}" alt="">
                        </span>
                        <div>
                            <h6 class="fs-14 fw-medium mb-1">${driver_name}</h6>
                            <p>${driver_phone}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-bottom mb-3 pb-2" id="price_container">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="fw-medium fs-14">${_l(
                        "admin.bookings.pricing_of_vehicle"
                    )}</h6>
                    <p>${default_currency}${
                totalAmount[0]["vehicle_price_rate"]
            }</p>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="fw-medium d-flex align-items-center fs-14"> <span class="extra_service_count me-1">${
                        totalAmount[0]["total_extra_service"]
                    } </span> ${_l("admin.common.extra_services")}
                        <a href="javascript:void(0);" class="me-2 ms-2 extra_service_tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="${
                            totalAmount[0]["extra_service_name"]
                        }"><i class="ti ti-info-circle-filled"></i></a>
                        <a href="javascript:void(0);" class="d-none"><i class="ti ti-edit"></i></a>
                    </h6>
                    <p>${default_currency}<span class="extra_service_price">${
                totalAmount[0]["total_extra_service_price"]
            }</span></p>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="fw-medium d-flex align-items-center fs-14">${_l(
                        "admin.bookings.security_deposit"
                    )}
                        <a href="javascript:void(0);" class="ms-2 d-none"><i class="ti ti-edit"></i></a>
                    </h6>
                    <p>${default_currency}${
                totalAmount[0]["security_deposit"]
            }</p>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-2 ${
                    typeof driver_name !== "undefined" && driver_name !== ""
                        ? ""
                        : "d-none"
                }">
                    <h6 class="fw-medium d-flex align-items-center fs-14">${_l(
                        "admin.bookings.driver_price"
                    )}
                        <a href="javascript:void(0);" class="ms-2 d-none"><i class="ti ti-edit"></i></a>
                    </h6>
                    <p>${default_currency}${totalAmount[0]["driver_price"]}</p>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-2 d-none insurance_summary_container">
                    <h6 class="fw-medium d-flex align-items-center fs-14"> <span class="insurance_count me-1">${
                        totalAmount[0]["total_insurance"]
                    } </span> ${_l("admin.common.insurances")}
                        <a href="javascript:void(0);" class="me-2 ms-2 insurance_tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="${
                            totalAmount[0]["insurance_name"]
                        }"><i class="ti ti-info-circle-filled"></i></a>
                        <a href="javascript:void(0);" class="d-none"><i class="ti ti-edit"></i></a>
                    </h6>
                    <p>${default_currency}<span class="total_insurance_price">${
                totalAmount[0]["total_insurance_price"]
            }</span></p>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <h6>${_l("admin.common.total_price")}</h6>
                <h6 class="total_price">${default_currency}<span class="total_price_val">${
                totalAmount[0]["total_price"]
            }</span></h6>
            </div>
        `);

            $("#first-field").hide();
            $("#second-field").hide();
            $("#third-field").show();
        }
    });

    $("#extra_service_prev_btn").on("click", function (event) {
        event.preventDefault();
        $("#second-field").show();
        $("#third-field").hide();
        $(".error-text").text("");
        $(".form-control, .select2-container").removeClass(
            "is-invalid is-valid"
        );
        $("#extra_service_summary").empty();
        selected_extra_service_ids = [];

        $(".vehicle_extra_service").each(function () {
            let isExtraService = $(this).is(":checked");
            if (isExtraService) {
                selected_extra_service_ids.push($(this).val());
            }
        });
    });

    $("#extra_service_next_btn").on("click", function (event) {
        event.preventDefault();

        let clonedDiv = $("#extra_service_summary").children().clone();
        $("#billing_summary").append(clonedDiv);

        $("#first-field").hide();
        $("#second-field").hide();
        $("#third-field").hide();
        $("#fourth-field").show();
        getInsurances();
        $(".insurance_summary_container").removeClass("d-none");
    });

    $("#billing_prev_btn").on("click", function (event) {
        event.preventDefault();
        $("#third-field").show();
        $("#fourth-field").hide();
        $(".error-text").text("");
        $(".form-control, .select2-container").removeClass(
            "is-invalid is-valid"
        );
        $("#billing_summary").empty();

        selected_insurance_ids = [];

        $(".vehicle_insurance").each(function () {
            let isInsurance = $(this).is(":checked");
            if (isInsurance) {
                selected_insurance_ids.push($(this).val());
            }
        });
        $(".insurance_summary_container").addClass("d-none");
        const response = calculateVehiclePrice();
        if (response) {
            $(".extra_service_price").text(
                response[0]["total_extra_service_price"]
            );
            $(".total_price_val").text(response[0]["total_price_val"]);
            $(".extra_service_count").text(response[0]["total_extra_service"]);
            $(".extra_service_tooltip").attr(
                "data-bs-original-title",
                response[0]["extra_service_name"]
            );
        }
        initializeTooltips();
    });

    function getExtraServices() {
        $.ajax({
            url: "/get-vehicle-extra-services",
            type: "POST",
            data: {
                vehicle_ids: selected_vehicle_ids,
            },
            dataType: "json",
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (result) {
                if (result.data && result.data.length > 0) {
                    let data = result.data;
                    $("#extra_service_list_container").empty();

                    data.forEach((item) => {
                        let isChecked = selected_extra_service_ids.includes(
                            item.id.toString()
                        );
                        let isActive = isChecked ? "active" : "";

                        let extraServiceType = "";
                        switch (item.extra_service_type) {
                            case "per_day":
                                extraServiceType = "Per Day";
                                break;
                            case "one_time":
                                extraServiceType = "One Time";
                                break;
                            case "percentage":
                                extraServiceType = "Percentage";
                                break;
                            default:
                                extraServiceType = item.extra_service_type;
                        }

                        // Outer column div
                        const colDiv = $("<div>").addClass("col-md-6");

                        // Custom checkbox container
                        const customCheckbox = $("<div>").addClass(
                            `custom-checkbox ${isActive}`
                        );

                        // Form check input
                        const formCheckDiv = $("<div>").addClass(
                            "form-check form-check-md"
                        );
                        const inputCheckbox = $("<input>", {
                            class: "form-check-input vehicle_extra_service",
                            type: "checkbox",
                            name: "extra_services[]",
                            value: item.id,
                            id: `extra-service-${item.id}`,
                            "data-price": item.price,
                            "data-price_type": item.extra_service_type,
                            "data-name": item.name,
                        });
                        if (isChecked) {
                            inputCheckbox.prop("checked", true);
                        }
                        formCheckDiv.append(inputCheckbox);

                        // Flex container for label and price
                        const flexDiv = $("<div>").addClass(
                            "d-flex align-items-center justify-content-between"
                        );

                        // Label container
                        const label = $("<label>", {
                            class: "form-check-label ms-2 ps-4",
                            for: `extra-service-${item.id}`,
                        });
                        $("<span>")
                            .addClass("fw-semibold text-gray-9 d-block mb-1")
                            .text(item.name)
                            .appendTo(label);
                        $("<span>")
                            .addClass("d-block")
                            .text(item.description)
                            .appendTo(label);

                        // Price container
                        const priceDiv = $("<div>").addClass("text-end");
                        $("<p>")
                            .addClass("mb-1")
                            .text(extraServiceType)
                            .appendTo(priceDiv);
                        if (item.extra_service_type === "percentage") {
                            $("<h6>").text(`${item.price}%`).appendTo(priceDiv);
                        } else {
                            $("<h6>")
                                .html(`${default_currency}${item.price}`)
                                .appendTo(priceDiv);
                        }

                        // Combine
                        flexDiv.append(label, priceDiv);
                        customCheckbox.append(formCheckDiv, flexDiv);
                        colDiv.append(customCheckbox);

                        $("#extra_service_list_container").append(colDiv);
                    });

                    // Price Calculation
                    let response = calculateVehiclePrice();
                    if (response) {
                        $(".extra_service_price").text(
                            response[0]["total_extra_service_price"]
                        );
                        $(".total_price_val").text(
                            response[0]["total_price_val"]
                        );
                        $(".extra_service_count").text(
                            response[0]["total_extra_service"]
                        );
                        $(".extra_service_tooltip").attr(
                            "data-bs-original-title",
                            response[0]["extra_service_name"]
                        );
                    }

                    initializeTooltips();
                } else {
                    $("#extra_service_list_container").html(`
        <div class="row">
            <span class="text-center mb-3">${_l(
                "admin.bookings.no_extra_services_found"
            )}</span>
        </div>
    `);
                }
            },
            error: function (error) {
                if (error.responseJSON.code === 500) {
                    showToast("error", error.responseJSON.message);
                } else {
                    showToast(
                        "error",
                        _l("admin.common.default_retrieve_error")
                    );
                }
            },
        });
    }

    $(document).on("click", ".vehicle_extra_service", function () {
        $(this).closest(".custom-checkbox").toggleClass("active", this.checked);
        const response = calculateVehiclePrice();

        if (response) {
            $(".extra_service_price").text(
                response[0]["total_extra_service_price"]
            );
            $(".total_price_val").text(response[0]["total_price"]);
            $(".extra_service_count").text(response[0]["total_extra_service"]);
            $(".extra_service_tooltip").attr(
                "data-bs-original-title",
                response[0]["extra_service_name"]
            );
        }
        initializeTooltips();
    });

    function getInsurances() {
        $.ajax({
            url: "/get-vehicle-insurances",
            type: "POST",
            data: { vehicle_ids: selected_vehicle_ids },
            dataType: "json",
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: handleInsuranceSuccess,
            error: handleInsuranceError,
        });
    }

    // --- Helper Functions ---

    function handleInsuranceSuccess(result) {
        const data = result?.data || [];

        if (!data.length) {
            showNoInsuranceMessage();
            return;
        }

        $("#insurance_list_container").empty();

        data.forEach((item) => {
            const colDiv = createInsuranceColumn(item);
            $("#insurance_list_container").append(colDiv);
        });

        updateInsurancePrice();
        initializeTooltips();
    }

    function handleInsuranceError(error) {
        const msg =
            error?.responseJSON?.code === 500
                ? error.responseJSON.message
                : _l("admin.common.default_retrieve_error");
        showToast("error", msg);
    }

    function showNoInsuranceMessage() {
        $("#insurance_list_container").html(`
            <div class="row">
                <span class="text-center mb-3">${_l("admin.bookings.no_insurance_found")}</span>
            </div>
        `);
    }

    function createInsuranceColumn(item) {
        const isChecked = selected_insurance_ids.includes(item.id.toString());
        const isActive = isChecked ? "active" : "";

        const colDiv = $("<div>").addClass("col-md-6");
        const customCheckbox = $("<div>").addClass(`custom-checkbox ${isActive}`);

        const formCheckDiv = $("<div>").addClass("form-check form-check-md");
        const inputCheckbox = $("<input>", {
            class: "form-check-input vehicle_insurance",
            type: "checkbox",
            name: "insurances[]",
            id: `insurance_${item.id}`,
            value: item.id,
            "data-price": item.price,
            "data-price_type": item.insurance_type,
            "data-name": item.insurance_name,
        });
        if (isChecked) inputCheckbox.prop("checked", true);
        formCheckDiv.append(inputCheckbox);

        const flexDiv = $("<div>").addClass("d-flex align-items-center justify-content-between");
        flexDiv.append(createInsuranceLabel(item), createInsurancePrice(item));

        customCheckbox.append(formCheckDiv, flexDiv);
        colDiv.append(customCheckbox);

        return colDiv;
    }

    function createInsuranceLabel(item) {
        const label = $("<label>", {
            class: "form-check-label ms-2 ps-4",
            for: `insurance_${item.id}`,
        });

        $("<span>")
            .addClass("fw-semibold text-gray-9 d-block mb-1")
            .text(item.insurance_name)
            .appendTo(label);

        if (item.insurance_benefits?.length) {
            const benefits = item.insurance_benefits.map((b) => b.benefit).join(", ");
            const benefitsSpan = $("<span>")
                .addClass("d-block text-info")
                .text(`+${item.insurance_benefits_count} ${_l("admin.common.benefits")}`);

            const tooltipIcon = $("<i>", {
                class: "ti ti-info-circle-filled text-gray-5 ms-1",
                "data-bs-toggle": "tooltip",
                "data-bs-placement": "top",
                "data-bs-original-title": benefits,
            });
            benefitsSpan.append(tooltipIcon);
            label.append(benefitsSpan);
        }

        return label;
    }

    function createInsurancePrice(item) {
        const priceDiv = $("<div>").addClass("text-end");

        const insuranceType = mapInsuranceType(item.insurance_type);
        $("<p>").addClass("mb-1").text(insuranceType).appendTo(priceDiv);

        const priceText =
            item.insurance_type === "percentage"
                ? `${item.price}%`
                : `${default_currency}${item.price}`;
        $("<h6>").html(priceText).appendTo(priceDiv);

        return priceDiv;
    }

    function mapInsuranceType(type) {
        switch (type) {
            case "fixed":
                return "Fixed";
            case "daily":
                return "Daily";
            case "percentage":
                return "Percentage";
            default:
                return type;
        }
    }

    function updateInsurancePrice() {
        const response = calculateVehiclePrice();
        if (!response) return;

        const data = response[0];
        $(".total_insurance_price").text(data.total_insurance_price);
        $(".total_price_val").text(data.total_price);
        $(".insurance_count").text(data.total_insurance);
        $(".insurance_tooltip").attr("data-bs-original-title", data.insurance_name);
    }

    $(document).on("click", ".vehicle_insurance", function () {
        $(this).closest(".custom-checkbox").toggleClass("active", this.checked);
        const response = calculateVehiclePrice();

        if (response) {
            $(".total_insurance_price").text(
                response[0]["total_insurance_price"]
            );
            $(".total_price_val").text(response[0]["total_price"]);
            $(".insurance_count").text(response[0]["total_insurance"]);
            $(".insurance_tooltip").attr(
                "data-bs-original-title",
                response[0]["insurance_name"]
            );
        }
        initializeTooltips();
    });

    $(document).on("click", "#reservation_complete_btn", function (event) {
        event.preventDefault();
        let selected_insurances = [];
        $(".vehicle_insurance").each(function () {
            if ($(this).is(":checked")) {
                selected_insurances.push($(this).val());
            }
        });

        let vehicle_season_id = $(".vehicle_select:checked").data(
            "vehicle_season_id"
        );
        let vehicle_tariff_id = $(".vehicle_select:checked").data(
            "vehicle_tariff_id"
        );

        let basicInfoData = $("#basicInfoForm").serializeArray();
        let customInfoData = $("#customerForm").serializeArray();
        let driverPriceForm = $("#driverPriceForm").serializeArray();

        let finalFormData = new FormData();

        [...basicInfoData, ...customInfoData, ...driverPriceForm].forEach(
            function (item) {
                finalFormData.append(item.name, item.value);
            }
        );

        let response = calculateVehiclePrice();

        let extraServicePayload = [];
        $(".vehicle_extra_service").each(function () {
            if ($(this).is(":checked")) {
                extraServicePayload.push({
                    id: parseInt($(this).val().trim()),
                    price: parseFloat($(this).data("price")),
                    type: $(this).data("price_type"),
                });
            }
        });

        let insurancePayload = [];
        $(".vehicle_insurance").each(function () {
            if ($(this).is(":checked")) {
                insurancePayload.push({
                    id: parseInt($(this).val().trim()),
                    price: parseFloat($(this).data("price")),
                    type: $(this).data("price_type"),
                });
            }
        });

        finalFormData.append("vehicle_price", vehiclePrice);
        finalFormData.append("final_price", response[0]["total_price"]);
        finalFormData.append(
            "extra_service",
            JSON.stringify(extraServicePayload)
        );
        finalFormData.append("insurance", JSON.stringify(insurancePayload));
        finalFormData.append(
            "total_insurance_price",
            response[0]["total_insurance_price"]
        );
        finalFormData.append(
            "total_extra_service_price",
            response[0]["total_extra_service_price"]
        );
        finalFormData.append("no_of_days", no_of_days);
        finalFormData.append("vehicle_season_id", vehicle_season_id);
        finalFormData.append("vehicle_tariff_id", vehicle_tariff_id);
        finalFormData.append("vehicle_price_type", vehiclePriceType);
        finalFormData.append(
            "vehicle_total_price",
            response[0]["vehicle_price_rate"]
        );

        $.ajax({
            url: "/admin/store-reservation",
            type: "POST",
            data: finalFormData,
            dataType: "json",
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            contentType: false,
            processData: false,
            cache: false,
            beforeSend: function () {
                $("#reservation_complete_btn").attr("disabled", true).html(`
                    <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l(
                        "admin.common.saving"
                    )}..
                `);
            },
            success: function (response) {
                $("#reservation_complete_btn")
                    .removeAttr("disabled")
                    .html(
                        `Finish & Save <i class="ti ti-chevron-right ms-1"></i>`
                    );
                if (response.code === 200) {
                    $("#add_booking").modal("hide");
                    loadCalendar();
                }
            },
            error: function (error) {
                $("#reservation_complete_btn")
                    .removeAttr("disabled")
                    .html(
                        `${_l("admin.common.finish")} & ${_l(
                            "admin.common.save"
                        )} <i class="ti ti-chevron-right ms-1"></i>`
                    );
                if (error.responseJSON.code === 500) {
                    showToast("error", error.responseJSON.message);
                } else {
                    showToast("error", _l("admin.common.default_create_error"));
                }
            },
        });

        let selectedStatus = ""; // Default: Get all bookings
        let selectedVehicles = [];
        let selectedCustomers = [];
        let selectedDrivers = [];
        let selectedCartypes = [];

       function getCsrfToken() {
            const tokenEl = document.querySelector('meta[name="csrf-token"]');
            return tokenEl ? tokenEl.getAttribute("content") : "";
        }

        async function fetchCalendarData() {
            const filterData = {
                status: selectedStatus || null,
                vehicles: selectedVehicles.length ? selectedVehicles : undefined,
                customers: selectedCustomers.length ? selectedCustomers : undefined,
                drivers: selectedDrivers.length ? selectedDrivers : undefined,
                cartypes: selectedCartypes.length ? selectedCartypes : undefined,
            };

            try {
                const res = await fetch("/admin/calendar-info", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": getCsrfToken(),
                    },
                    body: JSON.stringify(filterData),
                });

                if (!res.ok) {
                    console.warn("Fetch returned non-OK status:", res.status);
                    return { data: [] };
                }

                const json = await res.json();
                return json.data ? json : { data: [] };
            } catch (err) {
                console.error("Failed to fetch calendar data:", err);
                return { data: [] };
            }
        }


        function getEventBackgroundColor(status) {
            const colorMap = {
                1: "#FFF6B3", // In Progress
                4: "#AEEA94", // Booked
                5: "#A1E3F9", // Completed
                6: "#FFA09B", // Cancelled
            };
            return colorMap[status] || "#AEEA94";
        }

        function buildCalendarEvents(bookings) {
            return bookings.map((booking) => ({
                id: booking.id,
                title: booking.name,
                booking_status: booking.booking_status,
                backgroundColor: getEventBackgroundColor(booking.booking_status),
                textColor: "#111827",
                start: booking.start_datetime,
                end: booking.end_datetime,
                display: "block",
            }));
        }

        function renderBookingDetails(response) {
            if (response.code !== 200) return;

            const { booking, vehicleType, pickupLocation, returnLocation, driverDetails, customerDetails, currency } = response;

            if (customerDetails) {
                $("#customer_name").text(`${customerDetails.first_name} ${customerDetails.last_name}`);
                $("#customer_num").text(customerDetails.phone_number);
                $("#customer_img").attr("src", customerDetails.profile_image);
                $("#customer_section").removeClass("d-none");
            } else {
                $("#customer_section").addClass("d-none");
            }

            $("#car_img").attr("src", booking.vehicle.vehicle_image);
            $("#car_title").text(booking.vehicle.name);
            $("#car_price").text(`${currency}${booking.vehicle_price}/${booking.rental_type}`);

            $("#start_date_time").text(booking.start_datetime);
            $("#end_date_time").text(booking.end_datetime);
            $("#rent_period").text(`${booking.no_of_days} Days`);

            $("#drive_type").text(
                booking.delivery_type && booking.delivery_type !== "N/A" ? booking.delivery_type : "N/A"
            );

            $("#pickLan").text(pickupLocation);
            $("#retLan").text(returnLocation);
            $("#passenger_name").text(booking.passenger_name);

            if (driverDetails.driver_name?.trim()) {
                $("#driver_name").text(driverDetails.driver_name);
                $("#driver_num").text(driverDetails.phone_number);
                $("#driver_img").attr("src", driverDetails.image);
                $(".driverInfo").show();
            } else {
                $(".driverInfo").addClass("d-none");
            }

            $("#final_price").text(`$${booking.final_price}`);
            $("#booking_details_modal").modal("show");
        }

        function handleEventClick(info) {
            $.ajax({
                url: "/admin/calendar-detail",
                type: "GET",
                data: { booking_id: info.event.id },
                success: renderBookingDetails,
                error: () => alert("Error fetching booking details."),
            });
        }

        async function loadCalendar() {
            const data = await fetchCalendarData();

            document.querySelectorAll(".adminCalendar").forEach((calendarEl) => {
                if (calendarEl.fcInstance) {
                    calendarEl.fcInstance.destroy();
                }

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    headerToolbar: {
                        left: "prev,next today",
                        center: "title",
                        right: "dayGridMonth,timeGridWeek,timeGridDay",
                    },
                    initialView: "dayGridMonth",
                    events: buildCalendarEvents(data.data),
                    eventClick: handleEventClick,
                    editable: false,
                    eventContent: (arg) => ({ html: `<div>${arg.event.title}</div>` }),
                });

                calendarEl.fcInstance = calendar;
                calendar.render();
            });
        }

    });

    let selectedStatus = "";
    let selectedVehicles = [];
    let selectedCustomers = [];
    let selectedDrivers = [];
    let selectedCartypes = [];

    async function fetchCalendarData() {
        let filterData = {
            status: selectedStatus,
            vehicles: selectedVehicles.length > 0 ? selectedVehicles : [],
            customers: selectedCustomers.length > 0 ? selectedCustomers : [],
            drivers: selectedDrivers.length > 0 ? selectedDrivers : [],
            cartypes: selectedCartypes.length > 0 ? selectedCartypes : [],
        };

        try {
            const response = await fetch("/admin/calendar-info", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                body: JSON.stringify(filterData),
            });

            return await response.json();
        } catch (error) {
            console.error("Error fetching calendar data:", error);
            return { data: [] }; // Return empty array on error
        }
    }

    async function loadCalendar() {
        const data = await fetchCalendarData();

        document.querySelectorAll(".adminCalendar").forEach((calendarEl) => {
            if (calendarEl.fcInstance) calendarEl.fcInstance.destroy();

            const calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: "prev,next today",
                    center: "title",
                    right: "dayGridMonth,timeGridWeek,timeGridDay",
                },
                initialView: "dayGridMonth",
                events: data.data.map((booking) => mapBookingToEvent(booking)),
                eventClick: (info) => handleBookingClick(info.event.id),
                editable: false,
                eventContent: (arg) => ({ html: `<div>${arg.event.title}</div>` }),
            });

            calendarEl.fcInstance = calendar;
            calendar.render();
        });
    }

    // --- Helper Functions ---

    function mapBookingToEvent(booking) {
        const backgroundColor = getBookingColor(booking.booking_status);
        return {
            id: booking.id,
            title: booking.name,
            booking_status: booking.booking_status,
            backgroundColor,
            textColor: "#111827",
            start: booking.start_datetime,
            end: booking.end_datetime,
            display: "block",
        };
    }

    function getBookingColor(status) {
        switch (status) {
            case 1:
                return "#FFF6B3";
            case 4:
                return "#AEEA94";
            case 5:
                return "#A1E3F9";
            case 6:
                return "#FFA09B";
            default:
                return "#AEEA94";
        }
    }

    function handleBookingClick(bookingId) {
        $.ajax({
            url: "/admin/calendar-detail",
            type: "GET",
            data: { booking_id: bookingId },
            success: (response) => renderBookingDetails(response),
            error: () => alert("Error fetching booking details."),
        });
    }

    function renderBookingDetails(response) {
        if (response.code !== 200) return;

        const { booking, vehicleType, pickupLocation, returnLocation, driverDetails, customerDetails: customer, currency } = response;

        // Customer info
        if (customer) {
            $("#customer_name").text(`${customer.first_name} ${customer.last_name}`);
            $("#customer_num").text(customer.phone_number);
            $("#customer_img").attr("src", customer.profile_image);
            $("#customer_section").removeClass("d-none");
        } else {
            $("#customer_section").addClass("d-none");
        }

        // Booking status
        const statusMap = {
            1: { text: "In Progress", class: "badge-soft-warning" },
            2: { text: "Confirmed", class: "badge-soft-primary" },
            3: { text: "Rejected", class: "badge-soft-danger" },
            4: { text: "Booked", class: "badge-soft-info" },
            5: { text: "Completed", class: "badge-soft-success" },
            6: { text: "Cancelled", class: "badge-soft-secondary" },
        };
        const statusInfo = statusMap[booking.booking_status] || { text: "Unknown", class: "badge-soft-dark" };
        $("#book_status").text(statusInfo.text)
            .removeClass("badge-soft-success badge-soft-warning badge-soft-primary badge-soft-danger badge-soft-info badge-soft-secondary badge-soft-dark")
            .addClass(statusInfo.class);

        // Vehicle info
        $("#car_img").attr("src", booking.vehicle.vehicle_image);
        $("#car_title").text(booking.vehicle.name);
        $("#car_type").text(vehicleType.name);
        $("#car_price").text(`${currency}${booking.vehicle_price}/${booking.rental_type}`);

        // Booking details
        $("#start_date_time").text(booking.start_datetime);
        $("#end_date_time").text(booking.end_datetime);
        $("#rent_period").text(`${booking.no_of_days} Days`);
        $("#drive_type").text(booking.delivery_type && booking.delivery_type !== "N/A" ? booking.delivery_type : "N/A");
        $("#pickLan").text(pickupLocation);
        $("#retLan").text(returnLocation);
        $("#passenger_name").text(booking.passenger_name);

        // Driver info
        if (driverDetails.driver_name?.trim()) {
            $("#driver_name").text(driverDetails.driver_name);
            $("#driver_num").text(driverDetails.phone_number);
            $("#driver_img").attr("src", driverDetails.image);
            $(".driverInfo").show();
        } else {
            $(".driverInfo").addClass("d-none");
        }

        // Prices
        $("#totalValue").text(`${currency}${booking.vehicle_total_price}`);
        $("#taxValue").text(`${currency}${booking.tax_val ?? 0}`);
        $("#extraService").text(`${currency}${booking.total_extra_service_price}`);
        $("#inService").text(`${currency}${booking.total_insurance_price}`);
        $("#final_price").text(`${currency}${booking.final_price}`);

        $("#booking_details_modal").modal("show");
    }


    loadCalendar();

    document
        .querySelectorAll("#bookingStatusFilter .nav-link")
        .forEach((tab) => {
            tab.addEventListener("click", function () {
                document
                    .querySelector("#bookingStatusFilter .nav-link.active")
                    ?.classList.remove("active");
                this.classList.add("active");

                switch (this.innerText.trim()) {
                    case "In Progress":
                        selectedStatus = "1";
                        break;
                    case "Confirmed":
                        selectedStatus = "4";
                        break;
                    case "Completed":
                        selectedStatus = "5";
                        break;
                    case "Rejected":
                        selectedStatus = "6";
                        break;
                    default:
                        selectedStatus = "";
                }

                loadCalendar();
            });
        });

    document
        .getElementById("applyFilter")
        .addEventListener("click", function () {
            const selectedVehicles = [];
            const selectedCustomers = [];
            const selectedDrivers = [];
            const selectedCartypes = [];

            document
                .querySelectorAll(".selectedVehicle:checked")
                .forEach((el) => selectedVehicles.push(el.value));
            document
                .querySelectorAll(".selectedCustomer:checked")
                .forEach((el) => selectedCustomers.push(el.value));
            document
                .querySelectorAll(".selectedDriver:checked")
                .forEach((el) => selectedDrivers.push(el.value));
            document
                .querySelectorAll(".selectedCartype:checked")
                .forEach((el) => selectedCartypes.push(el.value));

            loadCalendar(); // or whatever function uses these arrays
        });

    document
        .getElementById("clearFilter")
        .addEventListener("click", function () {
            document.querySelectorAll(".form-check-input").forEach((el) => {
                el.checked = false;
            });

            selectedStatus = "";
            selectedVehicles = [];
            selectedCustomers = [];
            selectedDrivers = [];
            selectedCartypes = [];

            loadCalendar();
        });
})();
