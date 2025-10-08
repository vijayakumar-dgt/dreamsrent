(async () => {
    await loadTranslationFile("admin", "common, bookings");
    let default_currency = $("body").data("currency");
    let edit_driver_id = "";
    let edit_driver_price = "";
    let edit_vehicle_id = "";
    let edit_insurance = "";
    let edit_extra_service = [];

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

    let currentPage = 1;
    let lastPage = false;
    let totalPage = "";
    let isFetching = false;

    let selected_driver_id = "";
    let selected_extra_service_ids = [];
    let selected_vehicle_ids = [];
    let selected_insurance_ids = [];

    let vehiclePrice = "";
    let vehiclePriceType = "";

    $(document).ready(function () {
        initDateTimePicker();
        initFormValidation();
        getBrands();
        getTypes();
        getModels();
        getColors();
        editReservation();
    });

    function initDateTimePicker() {
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
    }

    function initFormValidation() {
        $("#cancelBookingForm").validate({
            rules: {
                cancel_reason: {
                    required: true,
                    minlength: 5,
                    maxlength: 500,
                },
            },
            messages: {
                cancel_reason: {
                    required: _l("admin.bookings.cancel_reason_required"),
                    minlength: _l("admin.bookings.cancel_reason_minlength"),
                    maxlength: _l("admin.bookings.cancel_reason_maxlength"),
                },
            },
            errorPlacement: function (error, element) {
                    const errorId = element.attr("id") + "_error";
                    $("#" + errorId).text(error.text());
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
            submitHandler: function (form) {
                let formData = new FormData(form);
                formData.append("booking_id", $("#booking_id").val());

                $.ajax({
                    type: "POST",
                    url: "/admin/cancel-booking",
                    data: formData,
                    enctype: "multipart/form-data",
                    processData: false,
                    contentType: false,
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    beforeSend: disableSubmitBtn,
                    success: handleCancelSuccess,
                    error: handleCancelError,
                });
            },
        });

        // Disable submit button with spinner
        function disableSubmitBtn() {
            $(".submitbtn").attr("disabled", true).html(`
                <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l(
                    "admin.common.cancelling"
                )}..
            `);
        }

        // Enable submit button and reset text
        function enableSubmitBtn() {
            $(".submitbtn")
                .removeAttr("disabled")
                .html(_l("admin.bookings.cancel_booking"));
        }

        // Reset form errors
        function resetFormErrors() {
            $(".error-text").text("");
            $(".form-control, .select2-container").removeClass("is-invalid is-valid");
        }

        // Handle AJAX success
        function handleCancelSuccess(resp) {
            resetFormErrors();
            enableSubmitBtn();

            if (resp.code === 200) {
                showToast("success", resp.message);
                $("#add_driver_modal").modal("hide");
                window.location.href = route("reservation.index");
            }
        }

        // Handle AJAX error
        function handleCancelError(error) {
            resetFormErrors();
            enableSubmitBtn();

            if (error.responseJSON?.code === 422) {
                const errors = error.responseJSON.errors;
                Object.keys(errors).forEach((key) => {
                    $("#" + key).addClass("is-invalid");
                    $("#" + key + "_error").text(errors[key][0]);
                });
            } else {
                showToast("error", error.responseJSON?.message || "Something went wrong");
            }
        }

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
                    const errorId = element.attr("id") + "_error";
                    $("#" + errorId).text(error.text());
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
                    const errorId = element.attr("id") + "_error";
                    $("#" + errorId).text(error.text());
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
    }

    function editReservation() {
        $.ajax({
            url: "/admin/get-reservation-details",
            type: "POST",
            data: {
                booking_id: $("#booking_id").val(),
            },
            dataType: "json",
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.code === 200 && response.data) {
                    let data = response.data;

                    if (data.booking_tariff) {
                        $("#tariff").val(data.booking_tariff).trigger("change");
                    }
                    $("#no_of_passengers").val(data.no_of_passengers ?? "");
                    $("#security_deposit").val(data.security_deposit ?? "");
                    $("#return_location")
                        .val(data.return_location)
                        .trigger("change");
                    $("#pickup_location")
                        .val(data.pickup_location)
                        .trigger("change");
                    $("#customer_id").val(data.customer_id).trigger("change");
                    if (data.driving_type) {
                        $("#driving_type")
                            .val(data.driving_type)
                            .trigger("change");
                    }

                    let startDateTime = moment(
                        data.start_datetime,
                        "YYYY-MM-DD HH:mm:ss"
                    );
                    startDate = startDateTime.format("DD-MM-YYYY");
                    startTime = startDateTime.format("HH:mm");
                    $("#start_date").val(startDate);
                    $("#start_time").val(startTime);

                    let endDateTime = moment(
                        data.end_datetime,
                        "YYYY-MM-DD HH:mm:ss"
                    );
                    endDate = endDateTime.format("DD-MM-YYYY");
                    endTime = endDateTime.format("HH:mm");
                    $("#end_date").val(endDate);
                    $("#end_time").val(endTime);

                    let edit_driver_id = data.driver_id;
                    let edit_driver_price = data.driver_price;
                    let edit_vehicle_id = data.vehicle_id;
                    let edit_insurance = data.insurance_formatted;
                    let edit_extra_service = data.extra_service_formatted;
                    let edit_vehicle_price = data.vehicle_price;

                    checkAndFetchVehicles();
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
            complete: function () {
                $(".label-loader, .input-loader").hide();
                $(".real-label, .real-input").removeClass("d-none");
            },
        });
    }

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

    function updateSummaryDates() {
        $(".summary_start_date").text(startDate && startTime ? `${startDate} ${startTime}` : "-");
        $(".summary_end_date").text(endDate && endTime ? `${endDate} ${endTime}` : "-");
    }

    function validateDuration(startDateTime, endDateTime) {
        const diffMinutes = endDateTime.diff(startDateTime, "minutes");

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

    function calculateNoOfDays(startDateTime, endDateTime) {
        const diffMinutes = endDateTime.diff(startDateTime, "minutes");
        if (startDateTime.isSame(endDateTime) || diffMinutes <= 1440) return 1;
        if (diffMinutes <= 2880) return 2;
        return Math.ceil(diffMinutes / 1440);
    }

    function calculateNoOfMonths(startDateTime, endDateTime) {
        let months = 0;
        let temp = startDateTime.clone();
        while (temp.isBefore(endDateTime, "month")) {
            const daysInMonth = temp.daysInMonth();
            if (temp.clone().add(daysInMonth, "days").isAfter(endDateTime)) break;
            months++;
            temp.add(daysInMonth, "days");
        }
        return months;
    }

    function calculateNoOfYears(startDate, endDate) {
        let years = 0;
        let startMoment = moment(startDate, "DD-MM-YYYY");
        let endMoment = moment(endDate, "DD-MM-YYYY");
        let remainingDays = endMoment.diff(startMoment, "days");

        while (remainingDays >= 365) {
            years++;
            remainingDays -= startMoment.isLeapYear() ? 366 : 365;
        }
        if (remainingDays > 0) years++;
        return years;
    }

    function checkAndFetchVehicles() {
        updateSummaryDates();

        if (!startDate || !startTime || !endDate || !endTime) {
            $(".summary_rental_period").text("-");
            return;
        }

        const startDateTime = moment(`${startDate} ${startTime}`, "DD-MM-YYYY HH:mm");
        const endDateTime = moment(`${endDate} ${endTime}`, "DD-MM-YYYY HH:mm");

        if (!validateDuration(startDateTime, endDateTime)) return;

        const no_of_days = calculateNoOfDays(startDateTime, endDateTime);
        $(".summary_rental_period").text(`${no_of_days} day${no_of_days > 1 ? "s" : ""}`.trim());

        const no_of_months = calculateNoOfMonths(startDateTime, endDateTime);
        const no_of_years = calculateNoOfYears(startDate, endDate);

        if (startDate && startTime && endDate && endTime && pickup_location_val && return_location_val) {
            $("#vehicle_list_main_container").removeClass("d-none");
            lastPage = false;
            currentPage = 1;
            getVehicles();
        }
    }

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
            booking_id: $("#booking_id").val(),
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
                    lastPage = result.data.current_page >= result.data.last_page;

                    if (!isLoadMore) {
                        $("#vehicle_list_container").empty();
                    }

                    data.forEach(item => {
                        let isChecked = item.id == edit_vehicle_id;

                        const cardDiv = $('<div>')
                            .addClass('card vehicle-card mb-2')
                            .attr('id', `vehicle_${item.id}`)
                            .attr('data-image', item.image)
                            .attr('data-type', item.vehicle_type)
                            .attr('data-name', item.vehicle_name)
                            .attr('data-price', item.vehicle_price)
                            .attr('data-price_type', item.vehicle_price_type);

                        const cardBody = $('<div>').addClass('card-body');
                        const rowDiv = $('<div>').addClass('row gy-3');

                        // Column 1: Radio + Image + Vehicle Info
                        const col1 = $('<div>').addClass('col-3');
                        const alignDiv = $('<div>').addClass('d-flex align-items-center');

                        const formCheck = $('<div>').addClass('form-check form-check-md me-3');
                        const radioInput = $('<input>', {
                            class: 'form-check-input vehicle_select',
                            name: 'vehicle_id',
                            id: `vehicle_select_${item.id}`,
                            type: 'radio',
                            value: item.id,
                            'data-price': item.vehicle_price,
                            'data-price_type': item.vehicle_price_type,
                            'data-vehicle_name': item.vehicle_name,
                            'data-vehicle_season_id': item.vehicle_season_id || '',
                            'data-vehicle_tariff_id': item.vehicle_tariff_id || ''
                        });
                        if (isChecked) {
                            radioInput.prop('checked', true);
                        }
                        formCheck.append(radioInput);

                        const avatarSpan = $('<span>').addClass('avatar flex-shrink-0 me-2');
                        $('<img>', {
                            src: item.image,
                            class: 'admin-vehicle-image',
                            alt: 'vehicle image'
                        }).appendTo(avatarSpan);

                        const vehicleInfo = $('<div>');
                        $('<p>').addClass('mb-1').text(item.vehicle_type).appendTo(vehicleInfo);
                        $('<h6>').addClass('fs-14').text(item.vehicle_name).appendTo(vehicleInfo);

                        alignDiv.append(formCheck, avatarSpan, vehicleInfo);
                        col1.append(alignDiv);

                        // Column 2: Color, Year, Price
                        const col2 = $('<div>').addClass('col-6');
                        const gapDiv = $('<div>').addClass('d-flex gy-3 gap-5');

                        // Color
                        const colorDiv = $('<div>');
                        $('<p>').addClass('mb-1').text(_l("admin.common.color")).appendTo(colorDiv);
                        const colorH6 = $('<h6>').addClass('fs-14 d-inline-flex align-items-center');
                        $('<i>', {
                            class: 'ti ti-square-filled me-1',
                            style: `color: ${item.color_value}`
                        }).appendTo(colorH6);
                        colorH6.append(document.createTextNode(item.color_name));
                        colorDiv.append(colorH6);
                        gapDiv.append(colorDiv);

                        // Year
                        const yearDiv = $('<div>');
                        $('<p>').addClass('mb-1').text(_l("admin.common.year")).appendTo(yearDiv);
                        $('<h6>').addClass('fs-14').text(item.year).appendTo(yearDiv);
                        gapDiv.append(yearDiv);

                        // Price
                        const priceDiv = $('<div>');
                        $('<p>').addClass('mb-1').text(_l("admin.common.price")).appendTo(priceDiv);
                        $('<h6>').addClass('fs-14').html(`${default_currency}${item.vehicle_price}<span class="text-gray-5 mt-1">/${item.vehicle_price_type}</span>`).appendTo(priceDiv);
                        gapDiv.append(priceDiv);
                        col2.append(gapDiv);

                        // Column 3: Model Name
                        const col3 = $('<div>').addClass('col-3');
                        const modelDiv = $('<div>').addClass('float-md-start');
                        $('<span>').addClass('badge bg-orange-transparent d-inline-flex align-items-center badge-sm mb-1')
                            .html(`<i class="ti ti-point-filled me-1"></i>${_l("admin.common.available")}`)
                            .appendTo(modelDiv);
                        $('<h6>').addClass('fs-14').text(item.model_name).appendTo(modelDiv);
                        col3.append(modelDiv);

                        rowDiv.append(col1, col2, col3);
                        cardBody.append(rowDiv);
                        cardDiv.append(cardBody);

                        $("#vehicle_list_container").append(cardDiv);
                    });

                    currentPage = result.data.current_page;
                    totalPage = result.data.last_page;

                    if (!isLoadMore) {
                        let checkedVehicle = $(".vehicle_select:checked");
                        if (checkedVehicle.length > 0) {
                            checkedVehicle.trigger("click");
                        }
                    }
                } else if (!isLoadMore) {
                    $("#vehicle_list_container").html(`
                        <div class="row">
                            <span class="text-center mb-3">${_l("admin.bookings.no_vehicles_found")}</span>
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
                $(".list-loader, .card-loader, .table-loader").hide();
                $("#vehicle_list_container, .real-table").removeClass("d-none");
            },
        });
    }

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

                    data.forEach(item => {
                        const option = $('<option>', {
                            value: item.id,
                            text: item.driver_name
                        });

                        if (item.id == selected_driver_id) {
                            option.prop('selected', true);
                        }

                        $("#driver_id").append(option);
                    });

                    $("#driver_id").val(edit_driver_id).trigger("change");
                } else {
                    $("#driver_id").find("option:not(:first)").remove();
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

    function getExtraServices() {
       $.ajax({
            url: "/get-vehicle-extra-services",
            type: "POST",
            data: { vehicle_ids: selected_vehicle_ids },
            dataType: "json",
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: handleExtraServicesSuccess,
            error: handleExtraServicesError
        });
    }

    function buildExtraServiceItem(item) {
        const isSelected =
            selected_extra_service_ids.includes(item.id.toString()) ||
            edit_extra_service.find((service) => service.id.toString() === item.id.toString());
        const isChecked = Boolean(isSelected);
        const isActive = isSelected ? 'active' : '';

        let extraServiceType = '';
        switch (item.extra_service_type) {
            case 'per_day': extraServiceType = 'Per Day'; break;
            case 'one_time': extraServiceType = 'One Time'; break;
            case 'percentage': extraServiceType = 'Percentage'; break;
            default: extraServiceType = item.extra_service_type;
        }

        const $col = $('<div>').addClass('col-md-6');
        const $checkboxWrapper = $('<div>').addClass(`custom-checkbox ${isActive}`);

        // Checkbox
        const $formCheck = $('<div>').addClass('form-check form-check-md');
        const $input = $('<input>', {
            type: 'checkbox',
            class: 'form-check-input vehicle_extra_service',
            name: 'extra_services[]',
            id: `extra-service-${item.id}`,
            value: item.id,
            checked: isChecked,
            'data-price': item.price,
            'data-price_type': item.extra_service_type,
            'data-name': item.name
        });
        $formCheck.append($input);

        // Label + description
        const $labelSection = $('<div>').addClass('d-flex align-items-center justify-content-between');
        const $label = $('<label>', {
            class: 'form-check-label ms-2 ps-4',
            for: `extra-service-${item.id}`
        });
        $('<span>').addClass('fw-semibold text-gray-9 d-block mb-1').text(item.name).appendTo($label);
        $('<span>').addClass('d-block').text(item.description).appendTo($label);

        // Price section
        const $priceInfo = $('<div>').addClass('text-end');
        $('<p>').addClass('mb-1').text(extraServiceType).appendTo($priceInfo);
        $('<h6>').html(item.extra_service_type === 'percentage' ? `${item.price}%` : `${default_currency}${item.price}`).appendTo($priceInfo);

        $labelSection.append($label).append($priceInfo);
        $checkboxWrapper.append($formCheck).append($labelSection);
        $col.append($checkboxWrapper);

        return $col;
    }

    // Handle AJAX success
    function handleExtraServicesSuccess(result) {
        const $container = $('#extra_service_list_container');
        $container.empty();

        if (!result.data || result.data.length === 0) {
            $container.html(`
                <div class="row">
                    <span class="text-center mb-3">${_l('admin.bookings.no_extra_services_found')}</span>
                </div>
            `);
            return;
        }

        result.data.forEach(item => {
            const $item = buildExtraServiceItem(item);
            $container.append($item);
        });

        const response = calculateVehiclePrice();
        if (response) {
            $('.extra_service_price').text(response[0]['total_extra_service_price']);
            $('.total_price_val').text(response[0]['total_price_val']);
            $('.extra_service_count').text(response[0]['total_extra_service']);
            $('.extra_service_tooltip').attr('data-bs-original-title', response[0]['extra_service_name']);
        }

        initializeTooltips();
    }

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
            success: handleGetInsurancesSuccess,
            error: handleGetInsurancesError
        });
    }

    function buildInsuranceItem(item) {
        const isSelected = selected_insurance_ids.includes(item.id.toString()) || edit_insurance.find(ins => ins.id == item.id);
        const isActive = isSelected ? 'active' : '';
        const isChecked = Boolean(isSelected);

        const benefits = item.insurance_benefits.map(b => b.benefit).join(', ');
        const tooltipAttr = benefits ? {
            'data-bs-toggle': 'tooltip',
            'data-bs-placement': 'top',
            'data-bs-original-title': benefits
        } : {};

        let insuranceType = '';
        switch (item.insurance_type) {
            case 'fixed': insuranceType = 'Fixed'; break;
            case 'daily': insuranceType = 'Daily'; break;
            case 'percentage': insuranceType = 'Percentage'; break;
            default: insuranceType = item.insurance_type;
        }

        const $col = $('<div>').addClass('col-md-6');
        const $checkboxWrapper = $('<div>').addClass(`custom-checkbox ${isActive}`);
        const $formCheck = $('<div>').addClass('form-check form-check-md');

        const $input = $('<input>', {
            type: 'checkbox',
            class: 'form-check-input vehicle_insurance',
            name: 'insurances[]',
            id: `insurance_${item.id}`,
            value: item.id,
            checked: isChecked,
            'data-price': item.price,
            'data-price_type': item.insurance_type,
            'data-name': item.insurance_name
        });
        $formCheck.append($input);

        const $infoRow = $('<div>').addClass('d-flex align-items-center justify-content-between');
        const $label = $('<label>', {
            class: 'form-check-label ms-2 ps-4',
            for: `insurance_${item.id}`
        });
        $('<span>').addClass('fw-semibold text-gray-9 d-block mb-1').text(item.insurance_name).appendTo($label);

        const $benefitSpan = $('<span>').addClass('d-block text-info').text(`+${item.insurance_benefits_count} ${_l('admin.common.benefits')}`);
        $('<i>').addClass('ti ti-info-circle-filled text-gray-5 ms-1').attr(tooltipAttr).appendTo($benefitSpan);
        $label.append($benefitSpan);

        const $priceInfo = $('<div>').addClass('text-end');
        $('<p>').addClass('mb-1').text(insuranceType).appendTo($priceInfo);
        const priceText = item.insurance_type === 'percentage' ? `${item.price}%` : `${default_currency}${item.price}`;
        $('<h6>').html(priceText).appendTo($priceInfo);

        $infoRow.append($label).append($priceInfo);
        $checkboxWrapper.append($formCheck).append($infoRow);
        $col.append($checkboxWrapper);

        return $col;
    }

    // Handle success of AJAX
    function handleGetInsurancesSuccess(result) {
        const $container = $('#insurance_list_container');
        $container.empty();

        if (!result.data || result.data.length === 0) {
            $container.html(`
                <div class="row">
                    <span class="text-center mb-3">${_l('admin.bookings.no_insurance_found')}</span>
                </div>
            `);
            return;
        }

        result.data.forEach(item => {
            const $item = buildInsuranceItem(item);
            $container.append($item);
        });

        const response = calculateVehiclePrice();
        if (response) {
            $('.total_insurance_price').text(response[0]['total_insurance_price']);
            $('.total_price_val').text(response[0]['total_price']);
            $('.insurance_count').text(response[0]['total_insurance']);
            $('.insurance_tooltip').attr('data-bs-original-title', response[0]['insurance_name']);
        }

        initializeTooltips();
    }

    // Handle AJAX error
    function handleGetInsurancesError(error) {
        if (error.responseJSON?.code === 500) {
            showToast("error", error.responseJSON.message);
        } else {
            showToast("error", _l("admin.common.default_retrieve_error"));
        }
    }

    function calculateVehiclePrice() {
        let vehiclePriceRate = vehiclePrice;
        let driverPriceVal = parseFloat($("#driver_price").val()).toFixed(2) || 0;
        let securityDeposit = $("#security_deposit").val() !== ""
            ? parseFloat($("#security_deposit").val()).toFixed(2)
            : 0;
        const response = [];

        // Calculate vehicle price based on type
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

        // Extra service calculations
        let total_extra_service_price = 0;
        let total_extra_service = 0;
        let extraServiceName = "";

        $(".vehicle_extra_service").each(function () {
            if ($(this).is(":checked")) {
                const priceType = $(this).data("price_type");
                const price = parseFloat($(this).data("price"));
                if (priceType === "per_day") {
                    total_extra_service_price += no_of_days * price;
                } else if (priceType === "one_time") {
                    total_extra_service_price += price;
                } else if (priceType === "percentage") {
                    total_extra_service_price += (vehiclePriceRate * price) / 100;
                }
                total_extra_service++;
                extraServiceName += $(this).data("name") + ", ";
            }
        });

        // Insurance calculations
        let total_insurance_price = 0;
        let total_insurance = 0;
        let insuranceName = "";

        $(".vehicle_insurance").each(function () {
            if ($(this).is(":checked")) {
                const priceType = $(this).data("price_type");
                const price = parseFloat($(this).data("price"));
                if (priceType === "daily") {
                    total_insurance_price += no_of_days * price;
                } else if (priceType === "fixed") {
                    total_insurance_price += price;
                } else if (priceType === "percentage") {
                    total_insurance_price += (vehiclePriceRate * price) / 100;
                }
                total_insurance++;
                insuranceName += $(this).data("name") + ", ";
            }
        });

        // Ensure numbers are fixed to 2 decimals
        vehiclePriceRate = parseFloat(vehiclePriceRate).toFixed(2);
        driverPriceVal = parseFloat(driverPriceVal).toFixed(2);
        securityDeposit = parseFloat(securityDeposit).toFixed(2);
        total_insurance_price = parseFloat(total_insurance_price).toFixed(2);
        total_extra_service_price = parseFloat(total_extra_service_price).toFixed(2);

        const totalPriceVal = (
            parseFloat(driverPriceVal) +
            parseFloat(securityDeposit) +
            parseFloat(vehiclePriceRate) +
            parseFloat(total_extra_service_price)
        ).toFixed(2);

        const totalPriceVal2 = (
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
                    const data = response.data;

                    // Sanitize all remote values
                    const imageUrl = DOMPurify.sanitize(data.profile_image);
                    const fullName = DOMPurify.sanitize(data.full_name);
                    const phone = DOMPurify.sanitize(data.phone_number);
                    const email = DOMPurify.sanitize(data.email);
                    const bookingsCount = DOMPurify.sanitize(data.bookings_count);

                    // --- Build Card ---
                    const $card = $('<div>', {
                        class: 'card bg-light',
                        id: 'customer_detail',
                        'data-image': imageUrl,
                        'data-name': fullName,
                        'data-phone': phone
                    });

                    const $cardBody = $('<div>').addClass('card-body');
                    const $mainRow = $('<div>').addClass('row align-items-center gy-3');

                    // Left column (profile info, phone, email)
                    const $leftCol = $('<div>').addClass('col-md-11');
                    const $detailsRow = $('<div>').addClass('row gx-2 gy-3');

                    // --- Profile ---
                    const $profileCol = $('<div>').addClass('col-md-4');
                    const $profileWrap = $('<div>').addClass('d-flex align-items-center');
                    const $avatar = $('<span>').addClass('avatar avatar-rounded flex-shrink-0 me-2')
                        .append($('<img>', { src: imageUrl, alt: 'Profile Image' }));
                    const $profileInfo = $('<div>')
                        .append(
                            $('<h6>').addClass('fs-14 mb-1').text(fullName),
                            $('<span>').addClass('badge bg-info-transparent').text(`${bookingsCount} ${_l('admin.bookings.bookings')}`)
                        );
                    $profileWrap.append($avatar, $profileInfo);
                    $profileCol.append($profileWrap);

                    // --- Phone ---
                    const $phoneCol = $('<div>').addClass('col-md-4')
                        .append(
                            $('<div>')
                                .append(
                                    $('<h6>').addClass('fs-14 mb-1').text(_l('admin.common.phone')),
                                    $('<p>').text(phone || '-')
                                )
                        );

                    // --- Email ---
                    const $emailCol = $('<div>').addClass('col-md-4')
                        .append(
                            $('<div>')
                                .append(
                                    $('<h6>').addClass('fs-14 mb-1').text(_l('admin.common.email')),
                                    $('<p>').text(email || '-')
                                )
                        );

                    // Append to row
                    $detailsRow.append($profileCol, $phoneCol, $emailCol);
                    $leftCol.append($detailsRow);

                    // Right column (remove button)
                    const $rightCol = $('<div>').addClass('col-md-1');
                    const $btnWrap = $('<div>').addClass('d-flex align-items-center justify-content-end');
                    const $removeBtn = $('<button>', {
                        type: 'button',
                        class: 'btn border-0 bg-transparent',
                        id: 'remove_customer'
                    }).append($('<i>').addClass('ti ti-trash'));
                    $btnWrap.append($removeBtn);
                    $rightCol.append($btnWrap);

                    // Assemble card
                    $mainRow.append($leftCol, $rightCol);
                    $cardBody.append($mainRow);
                    $card.append($cardBody);

                    // Inject into DOM
                    $('#customer_details_list').empty().append($card);
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

    $(document).on("click", "#remove_customer", function () {
        $("#customer_id").val("").trigger("change");
        $("#customer_details_list").html("");
    });

    $("#driver_id").on("change", function () {
        let driver_id = $(this).val();
        selected_driver_id = driver_id;
        $(this).valid();

        if (!driver_id) {
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
                    let driverPrice = edit_driver_price !== '' ? edit_driver_price : 0;

                    const safeText = (value) => DOMPurify.sanitize(value ?? '');

                    // Edit button row
                    let $editBtnRow = $('<div>').addClass('d-flex align-items-center justify-content-end mb-3');
                    let $editBtn = $('<button>', {
                        type: 'button',
                        class: 'text-purple text-decoration-underline fw-medium edit_driver_price border-0 bg-transparent',
                        'data-bs-toggle': 'modal',
                        'data-bs-target': '#edit_price_modal',
                        'data-image': safeText(data.image),
                        'data-driver_name': safeText(data.driver_name),
                        'data-phone': safeText(data.phone_number),
                        'data-price': safeText(driverPrice)
                    }).text(_l('admin.bookings.edit_price'));
                    $editBtnRow.append($editBtn);

                    // Driver card
                    let $card = $('<div>', {
                        class: 'card bg-light',
                        id: 'driver_detail',
                        'data-image': safeText(data.image),
                        'data-name': safeText(data.driver_name),
                        'data-phone': safeText(data.phone_number)
                    });

                    let $cardBody = $('<div>').addClass('card-body');
                    let $mainRow = $('<div>').addClass('row align-items-center gy-3');

                    // Left Column (driver details)
                    let $leftCol = $('<div>').addClass('col-md-11');
                    let $detailsRow = $('<div>').addClass('row gx-2 gy-3');

                    // Profile (Image, Name, Badge)
                    let $profileCol = $('<div>').addClass('col-md-5');
                    let $profileWrap = $('<div>').addClass('d-flex align-items-center');
                    let $avatar = $('<span>').addClass('avatar avatar-rounded flex-shrink-0 me-2')
                        .append($('<img>', { src: safeText(data.image), alt: 'Driver' }));
                    let $info = $('<div>')
                        .append(
                            $('<h6>').addClass('fs-14 mb-1').text(safeText(data.driver_name)),
                            $('<span>').addClass('badge bg-violet-transparent').text(`0 ${_l('admin.bookings.rides')}`)
                        );
                    $profileWrap.append($avatar, $info);
                    $profileCol.append($profileWrap);

                    // Phone
                    let $phoneCol = $('<div>').addClass('col-md-4')
                        .append(
                            $('<div>').append(
                                $('<h6>').addClass('fs-14 mb-1').text(_l('admin.common.phone')),
                                $('<p>').text(safeText(data.phone_number))
                            )
                        );

                    // Price
                    let $priceCol = $('<div>').addClass('col-md-3')
                        .append(
                            $('<div>').append(
                                $('<h6>').addClass('fs-14 mb-1').text(_l('admin.common.price')),
                                $('<p>').html(`${default_currency}<span class="td-driver-price">${safeText(driverPrice) || 0}</span>`)
                            )
                        );

                    $detailsRow.append($profileCol, $phoneCol, $priceCol);
                    $leftCol.append($detailsRow);

                    // Remove Button Column
                    let $rightCol = $('<div>').addClass('col-md-1');
                    let $removeWrap = $('<div>').addClass('d-flex align-items-center justify-content-end');
                    let $removeBtn = $('<button>', {
                        type: 'button',
                        class: 'btn border-0 bg-transparent',
                        id: 'remove_driver'
                    }).append($('<i>').addClass('ti ti-trash'));
                    $removeWrap.append($removeBtn);
                    $rightCol.append($removeWrap);

                    // Assemble everything
                    $mainRow.append($leftCol, $rightCol);
                    $cardBody.append($mainRow);
                    $card.append($cardBody);

                    // Inject into DOM
                    $('#driver_details_list').empty().append($editBtnRow).append($card);

                    // Set driver price value
                    $('#driver_price').val(driverPrice);
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
    });

    $("#driver_price").on("input", function () {
        $(this).val(
            $(this)
                .val()
                .replace(/\D/g, "")
        );
    });

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

            let totalAmount = calculateVehiclePrice();

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
                            <h6 class="d-inline-flex align-items-center fs-14 fw-medium ">Driver<a href="#" class="ms-2 d-none"><i class="ti ti-edit"></i></a></h6>
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
                        <h6 class="fw-medium fs-14">Pricing of Vehicle</h6>
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
                            }">
                                <i class="ti ti-info-circle-filled"></i>
                            </a>
                        </h6>
                        <p>${default_currency}<span class="extra_service_price">${
                totalAmount[0]["total_extra_service_price"]
            }</span></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-medium d-flex align-items-center fs-14">${_l(
                            "admin.bookings.security_deposit"
                        )}</h6>
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
                        )}</h6>
                        <p>${default_currency}${
                totalAmount[0]["driver_price"]
            }</p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2 d-none insurance_summary_container">
                        <h6 class="fw-medium d-flex align-items-center fs-14"> <span class="insurance_count me-1">${
                            totalAmount[0]["total_insurance"]
                        } </span> ${_l("admin.common.insurances")}
                            <a href="javascript:void(0);" class="me-2 ms-2 insurance_tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="${
                                totalAmount[0]["insurance_name"]
                            }">
                                <i class="ti ti-info-circle-filled"></i>
                            </a>
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
        let response = calculateVehiclePrice();
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

    $(document).on("click", ".vehicle_extra_service", function () {
        $(this).closest(".custom-checkbox").toggleClass("active", this.checked);
        let response = calculateVehiclePrice();

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

    $(document).on("click", ".vehicle_insurance", function () {
        $(this).closest(".custom-checkbox").toggleClass("active", this.checked);
        let response = calculateVehiclePrice();

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

        finalFormData.append("base_km", $("#base_km").val());
        finalFormData.append("km_extra_price", $("#km_extra_price").val());
        finalFormData.append("expenses", $("#expenses").val());
        finalFormData.append("delivery_price", $("#delivery_price").val());
        finalFormData.append("tax_type", $("#tax_type").val());
        finalFormData.append("tax_val", $("#tax_val").val());

        $.ajax({
            url: "/admin/store-quotations",
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
                        `${_l("admin.common.finish")} & ${_l(
                            "admin.common.save"
                        )} <i class="ti ti-chevron-right ms-1"></i>`
                    );
                if (response.code === 200) {
                    $("#final_vehicle_name").html(vehicle_name);
                    $("#final_reservation_date").html(
                        moment().format("DD MMM YYYY")
                    );
                    $("#reservation_view_details").attr(
                        "href",
                        response.view_details_url
                    );
                    $("#reservation_completed").modal("show");
                    setTimeout(function () {
                        window.location.href = "/admin/quotations";
                    }, 2000);
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
                } else if (error.responseJSON.code === 422) {
                    $(".error-text").html("");
                    $.each(error.responseJSON.errors, function (key, value) {
                        $("#" + key + "_error").html(value[0]);
                    });
                } else {
                    showToast("error", _l("admin.common.default_create_error"));
                }
            },
        });
    });
})();
