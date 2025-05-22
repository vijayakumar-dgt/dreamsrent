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
        if (startDate && startTime) {
            $(".summary_start_date").text(startDate + " " + startTime);
        } else {
            $(".summary_start_date").text("-");
        }

        if (endDate && endTime) {
            $(".summary_end_date").text(endDate + " " + endTime);
        } else {
            $(".summary_end_date").text("-");
        }

        if (startDate && startTime && endDate && endTime) {
            let startDateTime = moment(
                startDate + " " + startTime,
                "DD-MM-YYYY HH:mm"
            );
            let endDateTime = moment(
                endDate + " " + endTime,
                "DD-MM-YYYY HH:mm"
            );

            let diffMinutes = endDateTime.diff(startDateTime, "minutes");

            if (diffMinutes < 60) {
                $("#end_date, #end_time")
                    .addClass("is-invalid")
                    .removeClass("is-valid");
                $("#end_date_error").text(
                    _l("admin.bookings.duration_must_be_atleast_one_hour")
                );
                $(".summary_rental_period").text("-");
                $("#vehicle_list_main_container").addClass("d-none");
                return;
            } else {
                $("#end_date, #end_time").removeClass("is-invalid");
                $("#end_date_error").text("");
            }

            if (startDateTime.isSame(endDateTime)) {
                no_of_days = 1;
            } else if (
                startDateTime.format("DD-MM-YYYY") ===
                endDateTime.format("DD-MM-YYYY")
            ) {
                no_of_days = 1;
            } else if (diffMinutes === 1440) {
                no_of_days = 1;
            } else if (diffMinutes > 1440 && diffMinutes <= 2880) {
                no_of_days = 2;
            } else {
                no_of_days = Math.ceil(diffMinutes / 1440);
            }
            let rentalPeriodText =
                no_of_days + " day" + (no_of_days > 1 ? "s" : "");
            $(".summary_rental_period").text(rentalPeriodText.trim());

            no_of_months = 0;
            let tempStart = moment(
                startDate + " " + startTime,
                "DD-MM-YYYY HH:mm"
            );
            while (tempStart.isBefore(endDateTime, "month")) {
                let daysInMonth = tempStart.daysInMonth();
                if (tempStart.add(daysInMonth, "days").isAfter(endDateTime))
                    break;
                no_of_months++;
            }

            no_of_years = 0;
            let startMoment = moment(startDate, "DD-MM-YYYY");
            let endMoment = moment(endDate, "DD-MM-YYYY");
            let totalDaysLeft = endMoment.diff(startMoment, "days");
            let daysInYear = 365;
            let remainingDays = totalDaysLeft;

            while (remainingDays >= daysInYear) {
                no_of_years++;
                remainingDays -= daysInYear;
                if (startMoment.isLeapYear()) {
                    daysInYear = 366;
                }
            }
            if (remainingDays > 0) {
                no_of_years++;
            }
        } else {
            $(".summary_rental_period").text("-");
        }

        if (
            startDate &&
            startTime &&
            endDate &&
            endTime &&
            pickup_location_val &&
            return_location_val
        ) {
            $("#vehicle_list_main_container").removeClass("d-none");
            lastPage = false;
            currentPage = 1;
            getVehicles();
        }
    }

    $(document).on("change", "#tariff", function () {
        tariff = $(this).find("option:selected").text().toLowerCase();

        let todayDateTime = moment();
        let todayDate = todayDateTime.clone().startOf("day");
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
                if (isLoadMore == false) {
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

                    let options = data
                        .map(function (item) {
                            return `
                        <div class="card vehicle-card" id="vehicle_${
                            item.id
                        }" data-image="${item.image}" data-type="${item.vehicle_type}" data-name="${item.vehicle_name}" data-price="${item.vehicle_price}" data-price_type="${item.vehicle_price_type}">
                            <div class="card-body">
                                <div class="row gy-3">
                                    <div class="col-3">
                                        <div class="d-flex align-items-center">
                                            <div class="form-check form-check-md me-3">
                                                <input class="form-check-input vehicle_select" name="vehicle_id" id="vehicle_select_${
                                                    item.id
                                                }" value="${item.id}" type="radio" data-price="${item.vehicle_price}" data-price_type="${item.vehicle_price_type}" data-vehicle_name="${item.vehicle_name}" data-vehicle_season_id="${item.vehicle_season_id ?? ""}" data-vehicle_tariff_id="${item.vehicle_tariff_id ?? ""}">
                                            </div>
                                            <span class="avatar flex-shrink-0 me-2">
                                                <img src="${item.image}" alt="">
                                            </span>
                                            <div>
                                                <p class="mb-1">${
                                                    item.vehicle_type
                                                }</p>
                                                <h6 class="fs-14">${
                                                    item.vehicle_name
                                                }</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex gy-3 gap-5">
                                            <div class="">
                                                <div>
                                                    <p class="mb-1">${_l(
                                                        "admin.common.color"
                                                    )}</p>
                                                    <h6 class="fs-14 d-inline-flex align-items-center">
                                                        <i class="ti ti-square-filled me-1" style="color: ${
                                                            item.color_value
                                                        };"></i>${item.color_name}
                                                    </h6>
                                                </div>
                                            </div>
                                            <div class="">
                                                <div>
                                                    <p class="mb-1">${_l(
                                                        "admin.common.year"
                                                    )}</p>
                                                    <h6 class="fs-14">${
                                                        item.year
                                                    }</h6>
                                                </div>
                                            </div>
                                            <div class="">
                                                <div>
                                                    <p class="mb-1">${_l(
                                                        "admin.common.price"
                                                    )}</p>
                                                    <h6 class="fs-14">${default_currency}${item.vehicle_price}<span class="text-gray-5 mt-1">/${item.vehicle_price_type}</span></h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="float-md-start">
                                            <span class="badge bg-orange-transparent d-inline-flex align-items-center badge-sm mb-1">
                                                <i class="ti ti-point-filled me-1"></i>${_l(
                                                    "admin.common.available"
                                                )}
                                            </span>
                                            <h6 class="fs-14">${
                                                item.model_name
                                            }</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                        })
                        .join("");

                    if (isLoadMore) {
                        $("#vehicle_list_container").append(options);
                    } else {
                        $("#vehicle_list_container").empty().html(options);
                    }

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

                    $("#customer_details_list").html(`
                    <div class="card bg-light" id="customer_detail" data-image="${
                        data.profile_image
                    }" data-name="${data.full_name}" data-phone="${
                        data.phone_number
                    }">
                        <div class="card-body">
                            <div class="row align-items-center gy-3">
                                <div class="col-md-11">
                                    <div class="row gx-2 gy-3">
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-rounded flex-shrink-0 me-2">
                                                    <img src="${
                                                        data.profile_image
                                                    }" alt="">
                                                </span>
                                                <div>
                                                    <h6 class="fs-14 mb-1">${
                                                        data.full_name
                                                    }</h6>
                                                    <span class="badge bg-info-transparent">${
                                                        data.bookings_count
                                                    } ${_l(
                        "admin.bookings.bookings"
                    )}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div>
                                                <h6 class="fs-14 mb-1">${_l(
                                                    "admin.common.phone"
                                                )}</h6>
                                                <p>${data.phone_number}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div>
                                                <h6 class="fs-14 mb-1">${_l(
                                                    "admin.common.email"
                                                )}</h6>
                                                <p>${data.email}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <a href="javascript:void(0);" id="remove_customer"><i class="ti ti-trash"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        });
    });

    $(document).on("click", "#remove_customer", function () {
        $("#customer_id").val("").trigger("change");
        $("#customer_details_list").html("");
    });

    $("#driver_id").on("change", function () {
        let driver_id = $(this).val();
        selected_driver_id = driver_id;

        if (driver_id !== "") {
            $(this).valid();
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

                    $("#driver_details_list").html(`
                    <div class="d-flex align-items-center justify-content-end mb-3">
                        <a href="javascript:void(0);" class="text-purple text-decoration-underline fw-medium edit_driver_price" data-bs-toggle="modal" data-bs-target="#edit_price_modal"
                        data-image="${data.image}"
                        data-driver_name="${data.driver_name}"
                        data-phone="${data.phone_number}" data-price="0">${_l(
                        "admin.bookings.edit_price"
                    )}</a>
                    </div>
                    <div class="card bg-light" id="driver_detail" data-image="${
                        data.image
                    }" data-name="${data.driver_name}" data-phone="${
                        data.phone_number
                    }">
                        <div class="card-body">
                            <div class="row align-items-center gy-3">
                                <div class="col-md-11">
                                    <div class="row gx-2 gy-3">
                                        <div class="col-md-5">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-rounded flex-shrink-0 me-2">
                                                    <img src="${
                                                        data.image
                                                    }" alt="">
                                                </span>
                                                <div>
                                                    <h6 class="fs-14 mb-1">${
                                                        data.driver_name
                                                    }</h6>
                                                    <span class="badge bg-violet-transparent">0 ${_l(
                                                        "admin.bookings.rides"
                                                    )}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div>
                                                <h6 class="fs-14 mb-1">${_l(
                                                    "admin.common.phone"
                                                )}</h6>
                                                <p>${data.phone_number}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div>
                                                <h6 class="fs-14 mb-1">${_l(
                                                    "admin.common.price"
                                                )}</h6>
                                                <p>${default_currency}<span class="td-driver-price">0</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <a href="javascript:void(0);" id="remove_driver"><i class="ti ti-trash"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
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
                .replace(/[^0-9]/g, "")
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

                    let options = data
                        .map((item) => {
                            return `<option value="${item.id}" ${
                                item.id == selected_driver_id ? "selected" : ""
                            }>${item.driver_name}</option>`;
                        })
                        .join("");

                    $("#driver_id").find("option:not(:first)").remove();
                    $("#driver_id").append(options);
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
                .replace(/[^0-9]/g, "")
        );
    });

    $("#no_of_passengers").on("input", function () {
        $(this).val(
            $(this)
                .val()
                .replace(/[^0-9]/g, "")
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
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            } else {
                var errorId = element.attr("id") + "_error";
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
            var errorId = element.id + "_error";
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
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            } else {
                var errorId = element.attr("id") + "_error";
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
            var errorId = element.id + "_error";
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
        let tariffVal = $("#tariff")
            .find("option:selected")
            .text()
            .toLowerCase();
        let vehiclePriceRate = vehiclePrice;
        var driverPriceVal = parseFloat($("#driver_price").val()) || 0;
        var securityDeposit =
            $("#security_deposit").val() !== ""
                ? parseFloat($("#security_deposit").val())
                : 0;
        var response = [];

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

        var total_extra_service_price = 0;
        var total_extra_service = 0;
        var extraServiceName = "";
        var isExtraService;
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

        var total_insurance_price = 0;
        var total_insurance = 0;
        var insuranceName = "";
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

            var totalAmount = calculateVehiclePrice();

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
        var response = calculateVehiclePrice();
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

                    let options = data
                        .map((item) => {
                            let isChecked = selected_extra_service_ids.includes(
                                item.id.toString()
                            )
                                ? "checked"
                                : "";
                            let isActive = selected_extra_service_ids.includes(
                                item.id.toString()
                            )
                                ? "active"
                                : "";

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

                            return `
                        <div class="col-md-6">
                            <div class="custom-checkbox ${isActive}">
                                <div class="form-check form-check-md">
                                    <input class="form-check-input vehicle_extra_service" type="checkbox" name="extra_services[]" value="${
                                        item.id
                                    }" id="extra-service-${
                                item.id
                            }" ${isChecked} data-price="${
                                item.price
                            }" data-price_type="${
                                item.extra_service_type
                            }" data-name="${item.name}">
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="form-check-label ms-2 ps-4" for="extra-service-${
                                        item.id
                                    }">
                                        <span class="fw-semibold text-gray-9 d-block mb-1">${
                                            item.name
                                        }</span>
                                        <span class="d-block">${
                                            item.description
                                        }</span>
                                    </label>
                                    <div class="text-end">
                                        <p class="mb-1">${extraServiceType}</p>
                                        ${
                                            item.extra_service_type ==
                                            "percentage"
                                                ? `<h6>${item.price}%</h6>`
                                                : `<h6>${default_currency}${item.price}</h6>`
                                        }
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                        })
                        .join("");

                    $("#extra_service_list_container").empty().html(options);
                    var response = calculateVehiclePrice();
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
        var response = calculateVehiclePrice();

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

                    let options = data
                        .map((item) => {
                            let isChecked = selected_insurance_ids.includes(
                                item.id.toString()
                            )
                                ? "checked"
                                : "";
                            let isActive = selected_insurance_ids.includes(
                                item.id.toString()
                            )
                                ? "active"
                                : "";
                            let benefits = item.insurance_benefits
                                .map((b) => b.benefit)
                                .join(", ");
                            let tooltip = benefits
                                ? `data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="${benefits}"`
                                : "";

                            let insuranceType = "";
                            switch (item.insurance_type) {
                                case "fixed":
                                    insuranceType = "Fixed";
                                    break;
                                case "daily":
                                    insuranceType = "Daily";
                                    break;
                                case "percentage":
                                    insuranceType = "Percentage";
                                    break;
                                default:
                                    insuranceType = item.insurance_type;
                            }

                            return `
                        <div class="col-md-6">
                            <div class="custom-checkbox ${isActive}">
                                <div class="form-check form-check-md">
                                    <input class="form-check-input vehicle_insurance" type="checkbox" name="insurances[]" id="insurance_${
                                        item.id
                                    }" value="${
                                item.id
                            }" ${isChecked} data-price="${
                                item.price
                            }" data-price_type="${
                                item.insurance_type
                            }" data-name="${item.insurance_name}">
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="form-check-label ms-2 ps-4" for="insurance_${
                                        item.id
                                    }">
                                        <span class="fw-semibold text-gray-9 d-block mb-1">${
                                            item.insurance_name
                                        }</span>
                                        <span class="d-block text-info">+${
                                            item.insurance_benefits_count
                                        } ${_l(
                                "admin.common.benefits"
                            )}<i class="ti ti-info-circle-filled text-gray-5 ms-1" ${tooltip}></i></span>
                                    </label>
                                    <div class="text-end">
                                        <p class="mb-1">${insuranceType}</p>
                                        ${
                                            item.insurance_type == "percentage"
                                                ? `<h6>${item.price}%</h6>`
                                                : `<h6>${default_currency}${item.price}</h6>`
                                        }
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                        })
                        .join("");

                    $("#insurance_list_container").empty().html(options);
                    var response = calculateVehiclePrice();
                    if (response) {
                        $(".total_insurance_price").text(
                            response[0]["total_insurance_price"]
                        );
                        $(".total_price_val").text(response[0]["total_price"]);
                        $(".insurance_count").text(
                            response[0]["total_insurance"]
                        );
                        $(".insurance_tooltip").attr(
                            "data-bs-original-title",
                            response[0]["insurance_name"]
                        );
                    }
                    initializeTooltips();
                } else {
                    $("#insurance_list_container").html(`
                    <div class="row">
                        <span class="text-center mb-3">${_l(
                            "admin.bookings.no_insurance_found"
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

    $(document).on("click", ".vehicle_insurance", function () {
        $(this).closest(".custom-checkbox").toggleClass("active", this.checked);
        var response = calculateVehiclePrice();

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

        let vehicle_name = $(".vehicle_select:checked").data("vehicle_name");
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

        async function fetchCalendarData() {
            let filterData = {
                status: selectedStatus,
                vehicles: selectedVehicles.length > 0 ? selectedVehicles : [],
                customers:
                    selectedCustomers.length > 0 ? selectedCustomers : [],
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

            document
                .querySelectorAll(".adminCalendar")
                .forEach((calendarEl) => {
                    if (calendarEl.fcInstance) {
                        calendarEl.fcInstance.destroy(); // Destroy previous instance
                    }

                    var calendar = new FullCalendar.Calendar(calendarEl, {
                        headerToolbar: {
                            left: "prev,next today",
                            center: "title",
                            right: "dayGridMonth,timeGridWeek,timeGridDay",
                        },
                        initialView: "dayGridMonth",
                        events: data.data.map((booking) => {
                            let backgroundColor = "#AEEA94";

                            switch (booking.booking_status) {
                                case 1:
                                    backgroundColor = "#FFF6B3";
                                    break;
                                case 4:
                                    backgroundColor = "#AEEA94";
                                    break;
                                case 5:
                                    backgroundColor = "#A1E3F9";
                                    break;
                                case 6:
                                    backgroundColor = "#FFA09B";
                                    break;
                            }

                            return {
                                id: booking.id,
                                title: booking.name,
                                booking_status: booking.booking_status,
                                backgroundColor: backgroundColor,
                                textColor: "#111827",
                                start: booking.start_datetime,
                                end: booking.end_datetime,
                                display: "block",
                            };
                        }),
                        eventClick: function (info) {
                            const bookingId = info.event.id;
                            $.ajax({
                                url: "/admin/calendar-detail",
                                type: "GET",
                                data: { booking_id: bookingId },
                                success: function (response) {
                                    if (response.code === 200) {
                                        const booking = response.booking;
                                        const vehicleType =
                                            response.vehicleType;
                                        const pickupLocation =
                                            response.pickupLocation;
                                        const returnLocation =
                                            response.returnLocation;
                                        const driverDetails =
                                            response.driverDetails;
                                        const customer =
                                            response.customerDetails;

                                        if (customer) {
                                            $("#customer_name").text(
                                                `${customer.first_name} ${customer.last_name}`
                                            );
                                            $("#customer_num").text(
                                                customer.phone_number
                                            );
                                            $("#customer_img").attr(
                                                "src",
                                                customer.profile_image
                                            );
                                            $("#customer_section").removeClass(
                                                "d-none"
                                            ); // assuming it's hidden by default
                                        } else {
                                            $("#customer_section").addClass(
                                                "d-none"
                                            );
                                        }

                                        $("#car_img").attr(
                                            "src",
                                            booking.vehicle.vehicle_image
                                        );
                                        $("#car_title").text(
                                            booking.vehicle.name
                                        );
                                        $("#car_type").text(vehicleType.name);
                                        $("#car_price").html(
                                            `$${booking.vehicle_total_price}<span class="text-gray-5 fw-normal">/${booking.rental_type}</span>`
                                        );
                                        $("#start_date_time").text(
                                            booking.start_datetime
                                        );
                                        $("#end_date_time").text(
                                            booking.end_datetime
                                        );
                                        $("#rent_period").html(
                                            `${booking.no_of_days} Days`
                                        );
                                        $("#drive_type").text(
                                            booking.delivery_type &&
                                                booking.delivery_type !== "N/A"
                                                ? booking.delivery_type
                                                : "N/A"
                                        );

                                        $("#pickLan").text(pickupLocation);
                                        $("#retLan").text(returnLocation);
                                        $("#passenger_name").text(
                                            booking.passenger_name
                                        );
                                        if (
                                            driverDetails.driver_name &&
                                            driverDetails.driver_name.trim() !==
                                                ""
                                        ) {
                                            $("#driver_name").text(
                                                driverDetails.driver_name
                                            );
                                            $("#driver_num").text(
                                                driverDetails.phone_number
                                            );
                                            $("#driver_img").attr(
                                                "src",
                                                driverDetails.image
                                            );
                                            $(".driverInfo").show();
                                        } else {
                                            $(".driverInfo").addClass("d-none");
                                        }
                                        $("#final_price").html(
                                            `$${booking.final_price}`
                                        );

                                        $("#booking_details_modal").modal(
                                            "show"
                                        );
                                    }
                                },
                                error: function () {
                                    alert("Error fetching booking details.");
                                },
                            });
                        },
                        editable: false,
                        eventContent: function (arg) {
                            return { html: `<div>${arg.event.title}</div>` };
                        },
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
            if (calendarEl.fcInstance) {
                calendarEl.fcInstance.destroy(); // Destroy previous instance
            }

            var calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: "prev,next today",
                    center: "title",
                    right: "dayGridMonth,timeGridWeek,timeGridDay",
                },
                initialView: "dayGridMonth",
                events: data.data.map((booking) => {
                    let backgroundColor = "#AEEA94";

                    switch (booking.booking_status) {
                        case 1:
                            backgroundColor = "#FFF6B3";
                            break;
                        case 4:
                            backgroundColor = "#AEEA94";
                            break;
                        case 5:
                            backgroundColor = "#A1E3F9";
                            break;
                        case 6:
                            backgroundColor = "#FFA09B";
                            break;
                    }

                    return {
                        id: booking.id,
                        title: booking.name,
                        booking_status: booking.booking_status,
                        backgroundColor: backgroundColor,
                        textColor: "#111827",
                        start: booking.start_datetime,
                        end: booking.end_datetime,
                        display: "block",
                    };
                }),
                eventClick: function (info) {
                    const bookingId = info.event.id;
                    $.ajax({
                        url: "/admin/calendar-detail",
                        type: "GET",
                        data: { booking_id: bookingId },
                        success: function (response) {
                            if (response.code === 200) {
                                const booking = response.booking;
                                const vehicleType = response.vehicleType;
                                const pickupLocation = response.pickupLocation;
                                const returnLocation = response.returnLocation;
                                const driverDetails = response.driverDetails;
                                const customer = response.customerDetails;

                                if (customer) {
                                    $("#customer_name").text(
                                        `${customer.first_name} ${customer.last_name}`
                                    );
                                    $("#customer_num").text(
                                        customer.phone_number
                                    );
                                    $("#customer_img").attr(
                                        "src",
                                        customer.profile_image
                                    );
                                    $("#customer_section").removeClass(
                                        "d-none"
                                    );
                                } else {
                                    $("#customer_section").addClass("d-none");
                                }

                                const statusMap = {
                                    1: {
                                        text: "In Progress",
                                        class: "badge-soft-warning",
                                    },
                                    2: {
                                        text: "Confirmed",
                                        class: "badge-soft-primary",
                                    },
                                    3: {
                                        text: "Rejected",
                                        class: "badge-soft-danger",
                                    },
                                    4: {
                                        text: "Booked",
                                        class: "badge-soft-info",
                                    },
                                    5: {
                                        text: "Completed",
                                        class: "badge-soft-success",
                                    },
                                    6: {
                                        text: "Cancelled",
                                        class: "badge-soft-secondary",
                                    },
                                };

                                const bookingStatus = booking.booking_status;
                                const statusInfo = statusMap[bookingStatus] || {
                                    text: "Unknown",
                                    class: "badge-soft-dark",
                                };

                                $("#book_status").text(statusInfo.text);

                                $("#book_status")
                                    .removeClass(
                                        "badge-soft-success badge-soft-warning badge-soft-primary badge-soft-danger badge-soft-info badge-soft-secondary badge-soft-dark"
                                    )
                                    .addClass(statusInfo.class);

                                $("#car_img").attr(
                                    "src",
                                    booking.vehicle.vehicle_image
                                );
                                $("#car_title").text(booking.vehicle.name);
                                $("#car_type").text(vehicleType.name);
                                $("#car_price").html(
                                    `$${booking.vehicle_price}<span class="text-gray-5 fw-normal">/${booking.rental_type}</span>`
                                );
                                $("#start_date_time").text(
                                    booking.start_datetime
                                );
                                $("#end_date_time").text(booking.end_datetime);
                                $("#rent_period").html(
                                    `${booking.no_of_days} Days`
                                );
                                $("#drive_type").text(
                                    booking.delivery_type &&
                                        booking.delivery_type !== "N/A"
                                        ? booking.delivery_type
                                        : "N/A"
                                );

                                $("#pickLan").text(pickupLocation);
                                $("#retLan").text(returnLocation);
                                $("#passenger_name").text(
                                    booking.passenger_name
                                );
                                if (
                                    driverDetails.driver_name &&
                                    driverDetails.driver_name.trim() !== ""
                                ) {
                                    $("#driver_name").text(
                                        driverDetails.driver_name
                                    );
                                    $("#driver_num").text(
                                        driverDetails.phone_number
                                    );
                                    $("#driver_img").attr(
                                        "src",
                                        driverDetails.image
                                    );
                                    $(".driverInfo").show();
                                } else {
                                    $(".driverInfo").addClass("d-none");
                                }
                                $("#totalValue").html(
                                    `$${booking.vehicle_total_price}`
                                );
                                $("#taxValue").html(`$${booking.tax_val ?? 0}`);
                                $("#extraService").html(
                                    `$${booking.total_extra_service_price}`
                                );
                                $("#inService").html(
                                    `$${booking.total_insurance_price}`
                                );
                                $("#final_price").html(
                                    `$${booking.final_price}`
                                );

                                $("#booking_details_modal").modal("show");
                            }
                        },
                        error: function () {
                            alert("Error fetching booking details.");
                        },
                    });
                },
                editable: false,
                eventContent: function (arg) {
                    return { html: `<div>${arg.event.title}</div>` };
                },
            });

            calendarEl.fcInstance = calendar;
            calendar.render();
        });
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
