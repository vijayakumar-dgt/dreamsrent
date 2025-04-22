(async () => {
    await loadTranslationFile("web", "user,common,home");
    $(document).ready(function () {
        if ($(".userTimepicker").length > 0) {
            $(".userTimepicker").datetimepicker({
                format: "HH:mm",
                icons: {
                    up: "fas fa-angle-up",
                    down: "fas fa-angle-down",
                    next: "fas fa-angle-right",
                    previous: "fas fa-angle-left",
                },
            });
        }

        $(".scrolUp").on("click", function () {
            let middlePosition = $(document).height() / 6.5;
            $("html, body").animate({ scrollTop: middlePosition }, 200);
        });

        $("#country_id").select2({
            placeholder: _l("web.home.select_country"),
        });

        $("#state_id").select2({
            placeholder: _l("web.home.select_state"),
        });

        $("#city_id").select2({
            placeholder: _l("web.home.select_city"),
        });

        $("#select2").select2({
            placeholder: _l("web.common.select"),
        });

        function toggleContainer() {
            if ($("#location_delivery").is(":checked")) {
                $("#devliveryCOntainer").show();
                $("#selfCOntainer").hide();
            } else {
                $("#devliveryCOntainer").hide();
                $("#selfCOntainer").show();
            }
        }

        // Attach event listeners
        $("input[name='rent_type']").on("change", toggleContainer);

        // Initial state
        toggleContainer();
    });

    //----------------------------------------------------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------------------------------------------------
    //Valication and Submitted -------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------------------------------------------------

    $(document).ready(function () {
        $("#bookLocationForm").validate({
            rules: {
                delivery_location: {
                    required: true,
                },
                delivery_return_location: {
                    required: true,
                },
                pickup_location: {
                    required: true,
                },
                pickup_return_location: {
                    required: true,
                },
            },
            messages: {
                delivery_location: {
                    required: _l("web.home.delivery_location_required"),
                },
                delivery_return_location: {
                    required: _l("web.home.delivery_return_location_required"),
                },
                pickup_location: {
                    required: _l("web.home.pickup_location_required"),
                },
                pickup_return_location: {
                    required: _l("web.home.pickup_return_location_required"),
                },
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("text-danger");

                if (element.attr("type") === "checkbox") {
                    element.closest(".custom_check").append(error);
                } else if (element.attr("type") === "file") {
                    $("#driver_file_error").html(error);
                } else {
                    element.closest(".input-block").append(error);
                }
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
                $(element)
                    .closest(".input-block")
                    .find(".invalid-feedback")
                    .addClass("text-danger")
                    .show();
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
                $(element)
                    .closest(".input-block")
                    .find(".invalid-feedback")
                    .removeClass("text-danger")
                    .hide();
            },
        });

        $(".extraBtn").on("click", function (event) {
            event.preventDefault();

            let carLocationInfoFormDate =
                $("#bookLocationForm").serializeArray();

            if ($("#bookLocationForm").valid()) {
                let formDataCollection = {};
                carLocationInfoFormDate.forEach(function (item) {
                    formDataCollection[item.name] = item.value;
                });

                // Get selected rental type
                let selectedRentType = $(
                    "input[name='rent_type']:checked"
                ).attr("id");
                let rentTypeText = selectedRentType
                    ? selectedRentType.replace("location_", "")
                    : "N/A";

                // Capitalize only the first letter
                rentTypeText =
                    rentTypeText.charAt(0).toUpperCase() +
                    rentTypeText.slice(1);

                // Set the text inside the <p> tag
                $("#rentalTypeText").text(rentTypeText);

                // Show next section
                $("#first-field").hide();
                $("#second-field").removeClass("d-none").show();
                $("#location-card").removeClass("d-none").show();
                $("#firstBar").addClass("activated");
                $("#secondBar").addClass("active");
            }
        });

        $(".backLocationBtn").on("click", function () {
            $("#first-field").show();
            $("#location-card").addClass("d-none").hide();
            $("#second-field").addClass("d-none").hide();
            $("#firstBar").removeClass("activated");
            $("#secondBar").removeClass("active");
        });

        $(".locationBack").on("click", function () {
            $("#first-field").show();
            $("#location-card").addClass("d-none").hide();
            $("#second-field").addClass("d-none").hide();
            $("#firstBar").removeClass("activated");
            $("#secondBar").removeClass("active");
        });

        let totalExtraServicePrice = 0;
        let totalInsurancePrice = 0;
        let basePrice = parseFloat($("#total_price").val()) || 0;

        let $extraChargesList = $(".extra-charges-list"); // Extra services list
        let $insuranceChargesList = $(".insurance-charges-list"); // Insurance list
        let $extraChargesTotal = $(".extra-services-total"); // Extra services total price
        let $insuranceChargesTotal = $(".insurance-total"); // Insurance total price
        let $totalPriceElement = $("#total_price"); // Hidden input for total price
        let $totalPriceExtra = $("#extra_price_total"); // Hidden input for total price
        let $totalPriceInsurance = $("#insurance_price_total"); // Hidden input for total price
        let $totalPriceDriver = $("#driver_price_total"); // Hidden input for total price
        let $totalPriceSpan = $(".vehicle-total-price span"); // Display total price
        let $submitButton = $("#sumbit_btn"); // Display final price button

        let noServiceMessage = `<li class="no-service-message">${_l(
            "web.home.no_extra_service_added_to_this_vehicle"
        )}</li>`;
        let noInsuranceMessage = `<li class="no-insurance-message">${_l(
            "webkitURL.home.no_insurance_available"
        )}</li>`;

        function toggleDriverInfo() {
            let driverPrice = parseFloat($("#driver_price").val()) || 0;

            if ($("#self_driver").is(":checked")) {
                $(".self-driver-info").show();
                $(".acting-driver-info").hide();
                totalDriverPrice = 0;
                $("#driver_id").prop("disabled", true);
            } else if ($("#acting_driver").is(":checked")) {
                $(".self-driver-info").hide();
                $(".acting-driver-info").show();
                totalDriverPrice = driverPrice;
                $("#driver_id").prop("disabled", false);
            } else {
                $(".self-driver-info").hide();
                $(".acting-driver-info").hide();
                totalDriverPrice = 0;
                $("#driver_id").prop("disabled", true);
            }

            updateTotalPrice();
        }

        // Get currency symbol on initial load
        let currencySymbol = $("#currency").val() || "$";

        // Debug output (optional)
        console.log("Currency Symbol:", currencySymbol);

        // Update Total Price
        function updateTotalPrice() {
            let finalTotal =
                basePrice +
                totalExtraServicePrice +
                totalInsurancePrice +
                totalDriverPrice;

            $totalPriceDriver.val(totalDriverPrice.toFixed(2));
            $totalPriceExtra.val(totalExtraServicePrice.toFixed(2));
            $totalPriceInsurance.val(totalInsurancePrice.toFixed(2));
            $totalPriceElement.val(finalTotal.toFixed(2));

            $totalPriceSpan.text(`${currencySymbol}${finalTotal.toFixed(2)}`);
            $submitButton.text(
                `${_l("web.home.pay")} ${currencySymbol}${finalTotal.toFixed(
                    2
                )} & ${_l("web.home.place_reservation")}`
            );
            $extraChargesTotal.text(
                `${currencySymbol}${totalExtraServicePrice.toFixed(2)}`
            );
            $insuranceChargesTotal.text(
                `${currencySymbol}${totalInsurancePrice.toFixed(2)}`
            );
        }

        // Check if extra services or insurance lists are empty
        function checkEmptyCart() {
            if ($extraChargesList.children("li").length === 0) {
                $extraChargesList.append(noServiceMessage);
            } else {
                $extraChargesList.find(".no-service-message").remove();
            }

            if ($insuranceChargesList.children("li").length === 0) {
                $insuranceChargesList.append(noInsuranceMessage);
            } else {
                $insuranceChargesList.find(".no-insurance-message").remove();
            }
        }

        // Add Extra Service
        $(".add-addon-btn").on("click", function () {
            let parent = $(this).closest("li");
            let serviceId = parent.data("service-id");
            let serviceName = parent.find(".adon-name h6").text();
            let servicePrice = parseFloat(
                parent
                    .find(".adon-price")
                    .text()
                    .replace(currencySymbol, "")
                    .trim()
            );

            parent.find("input[name='add_extra']").prop("checked", true);
            $(this).hide();
            parent.find(".remove-adon-btn").show();

            $extraChargesList.find(".no-service-message").remove();

            if (
                $extraChargesList.find(`li[data-service-id="${serviceId}"]`)
                    .length === 0
            ) {
                $extraChargesList.append(`
                    <li data-service-id="${serviceId}">
                        <h6>${serviceName}</h6>
                        <h5>${currencySymbol}${servicePrice.toFixed(2)}</h5>
                    </li>
                `);
                totalExtraServicePrice += servicePrice;
            }

            updateTotalPrice();
        });

        // Remove Extra Service
        $(".remove-adon-btn").on("click", function () {
            let parent = $(this).closest("li");
            let serviceId = parent.data("service-id");
            let servicePrice = parseFloat(
                parent
                    .find(".adon-price")
                    .text()
                    .replace(currencySymbol, "")
                    .trim()
            );

            parent.find("input[name='add_extra']").prop("checked", false);
            $(this).hide();
            parent.find(".add-addon-btn").show();

            let $selectedService = $extraChargesList.find(
                `li[data-service-id="${serviceId}"]`
            );
            if ($selectedService.length > 0) {
                totalExtraServicePrice -= servicePrice;
                $selectedService.remove();
            }

            checkEmptyCart();
            updateTotalPrice();
        });

        // Select Insurance
        $(".insurance-select").on("click", function () {
            let parent = $(this);
            let checkbox = parent.find("input[name='add_insurance']");
            let insuranceId = parent.find("input[name='insurance_id[]']").val();
            let insuranceName = parent.find("p.fs-14").text().trim();
            let insurancePrice = parseFloat(
                parent.find("h6").text().replace(currencySymbol, "")
            );

            if (parent.hasClass("active")) {
                parent.removeClass("active");
                checkbox.prop("checked", false);

                let $selectedInsurance = $insuranceChargesList.find(
                    `li[data-insurance-id="${insuranceId}"]`
                );
                if ($selectedInsurance.length > 0) {
                    totalInsurancePrice -= insurancePrice;
                    $selectedInsurance.remove();
                }
            } else {
                parent.addClass("active");
                checkbox.prop("checked", true);

                $insuranceChargesList.find(".no-insurance-message").remove();

                if (
                    $insuranceChargesList.find(
                        `li[data-insurance-id="${insuranceId}"]`
                    ).length === 0
                ) {
                    $insuranceChargesList.append(`
                <li data-insurance-id="${insuranceId}">
                    <h6>${insuranceName}</h6>
                    <h5>${currencySymbol}${insurancePrice.toFixed(2)}</h5>
                </li>
            `);
                    totalInsurancePrice += insurancePrice;
                }
            }

            checkEmptyCart();
            updateTotalPrice();
        });

        $("input[name='driver_type']").on("change", toggleDriverInfo);

        // Initial call
        toggleDriverInfo();

        // Initial checks
        checkEmptyCart();
        updateTotalPrice();

        $("#bookExtraDetailsForm").validate({
            rules: {
                driver_first_name: {
                    required: true,
                },
                driver_last_name: {
                    required: true,
                },
                driver_age: {
                    required: true,
                    number: true,
                    min: 20, // Ensure the driver is at least 20 years old
                },
                driver_file: {
                    required: true,
                    extension: "jpg|jpeg|png", // Only allow images
                },
                driver_licence: {
                    required: true,
                },
                driver_mobile_number: {
                    required: true,
                    minlength: 10,
                    maxlength: 15,
                },
                driver_check: {
                    required: true,
                },
            },
            messages: {
                driver_first_name: {
                    required: _l("web.home.first_name_required"),
                },
                driver_last_name: {
                    required: _l("web.home.last_name_required"),
                },
                driver_age: {
                    required: _l("web.home.driver_age_required"),
                    number: _l("web.home.valid_age"),
                    min: _l("web.home.min_driver_age"),
                },
                driver_file: {
                    required: _l("web.home.document_required"),
                    extension: _l("web.home.doc_extension"),
                },
                driver_licence: {
                    required: _l("web.home.license_number_required"),
                },
                driver_mobile_number: {
                    required: _l("web.home.mobile_number_required"),
                    minlength: _l("web.home.min_length_10"),
                    maxlength: _l("web.home.max_length_15"),
                },
                driver_check: {
                    required: _l("web.home.confirm_driver_age"),
                },
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("text-danger"); // Ensure the error text is red

                if (element.attr("type") === "checkbox") {
                    element.closest(".custom_check").append(error);
                } else if (element.attr("type") === "file") {
                    $("#driver_file_error").html(error);
                } else {
                    element.closest(".input-block").append(error);
                }
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
                $(element)
                    .closest(".input-block")
                    .find(".invalid-feedback")
                    .addClass("text-danger")
                    .show();
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
                $(element)
                    .closest(".input-block")
                    .find(".invalid-feedback")
                    .removeClass("text-danger")
                    .hide();
            },
        });

        $(".userInfoBtn").on("click", function (event) {
            event.preventDefault();

            let carExtraInfoFormDate = $(
                "#bookExtraDetailsForm"
            ).serializeArray();

            if ($("#bookExtraDetailsForm").valid()) {
                let formDataCollection = {};
                carExtraInfoFormDate.forEach(function (item) {
                    formDataCollection[item.name] = item.value;
                });

                $("#second-field").hide();
                $("#third-field").removeClass("d-none").show();
                $("#extra-card").removeClass("d-none").show();
                $("#secondBar").addClass("activated");
                $("#thirdBar").addClass("active");
            }
        });

        $(".backToExtra").on("click", function () {
            $("#second-field").show();
            $("#third-field").addClass("d-none").hide();
            $("#extra-card").addClass("d-none").hide();
            $("#secondBar").removeClass("activated");
            $("#thirdBar").removeClass("active");
        });
        $(".backExtra").on("click", function () {
            $("#second-field").show();
            $("#third-field").addClass("d-none").hide();
            $("#fourth-field").addClass("d-none").hide();
            $("#extra-card").addClass("d-none").hide();
            $("#secondBar").removeClass("activated");
            $("#thirdBar").removeClass("active");
        });

        $("#bookUserInfoForm").validate({
            rules: {
                first_name: {
                    required: true,
                },
                last_name: {
                    required: true,
                },
                no_person: {
                    required: true,
                },
                address: {
                    required: true,
                },
                country_id: {
                    required: true,
                },
                state_id: {
                    required: true,
                },
                city_id: {
                    required: false,
                },
                pincode: {
                    required: true,
                    digits: true,
                },
                email: {
                    required: true,
                    email: true,
                },
                phone_number: {
                    required: true,
                },
                trems: {
                    required: true,
                },
            },
            messages: {
                first_name: {
                    required: _l("web.home.first_name_required"),
                },
                last_name: {
                    required: _l("web.home.last_name_required"),
                },
                no_person: {
                    required: _l("web.home.select_no_of_persons"),
                },
                address: {
                    required: _l("web.home.address_required"),
                },
                country_id: {
                    required: _l("web.home.select_country"),
                },
                state_id: {
                    required: _l("web.home.select_state"),
                },
                city_id: {
                    required: _l("web.home.select_city"),
                },
                pincode: {
                    required: _l("web.home.pincode_required"),
                    digits: _l("web.home.pincode_digits"),
                },
                email: {
                    required: _l("web.home.email_required"),
                    email: _l("web.home.valid_email"),
                },
                phone_number: {
                    required: _l("web.home.mobile_number_required"),
                },
                trems: {
                    required: _l("web.home.terms_required"),
                },
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("text-danger"); // Ensure the error text is red

                if (element.attr("type") === "checkbox") {
                    element.closest(".custom_check").append(error);
                } else if (element.attr("type") === "file") {
                    $("#driver_file_error").html(error);
                } else if (element.hasClass("select2-hidden-accessible")) {
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
                $(element)
                    .closest(".input-block")
                    .find(".invalid-feedback")
                    .addClass("text-danger")
                    .show();
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
        });

        $(".goCheckOut").on("click", function (event) {
            event.preventDefault();

            let caruserInfoFormDate = $("#bookUserInfoForm").serializeArray();

            if ($("#bookUserInfoForm").valid()) {
                let formDataCollection = {};
                caruserInfoFormDate.forEach(function (item) {
                    formDataCollection[item.name] = item.value;
                });

                $("#third-field").hide();
                $("#fourth-field").removeClass("d-none").show();
                $("#thirdBar").addClass("activated");
                $("#fourthBar").addClass("active");
            }
        });

        $(".backUserInfo").on("click", function (event) {
            event.preventDefault();

            let caruserInfoFormDate = $("#bookUserInfoForm").serializeArray();

            if ($("#bookUserInfoForm").valid()) {
                let formDataCollection = {};
                caruserInfoFormDate.forEach(function (item) {
                    formDataCollection[item.name] = item.value;
                });

                $("#fourth-field").hide();
                $("#third-field").removeClass("d-none").show();
                $("#thirdBar").removeClass("activated");
                $("#fourthBar").addClass("active");
            }
        });

        //++++++++++++++++  =================   =================================================================
        //Final payment button
        $("#sumbit_btn").on("click", function (event) {
            event.preventDefault();

            let carLocationInfoData = $("#bookLocationForm").serializeArray();
            let carExtraInfoData = $("#bookExtraDetailsForm").serializeArray();
            let carUserInfoData = $("#bookUserInfoForm").serializeArray();
            let carpaymentInfoData = $("#bookPaymentForm").serializeArray();

            if ($("#bookPaymentForm").valid()) {
                let finalFormData = new FormData();

                finalFormData.append(
                    "_token",
                    $('meta[name="csrf-token"]').attr("content")
                );

                [
                    ...carLocationInfoData,
                    ...carExtraInfoData,
                    ...carUserInfoData,
                    ...carpaymentInfoData,
                ].forEach(function (item) {
                    finalFormData.append(item.name, item.value);
                });

                let selectedExtras = [];

                $('input[name="add_extra"]:checked').each(function () {
                    let parent = $(this).closest("li");
                    let extraId = parent.data("service-id");
                    let extraPrice = parseFloat(
                        parent.find(".adon-price").text().replace("$", "")
                    );
                    let extraType = parent
                        .find("input[name='extra_type[]']")
                        .val();

                    selectedExtras.push({
                        id: extraId,
                        price: extraPrice,
                        value: extraType,
                    });
                });

                finalFormData.append(
                    "extra_services",
                    JSON.stringify(selectedExtras)
                );

                let selectedInsurance = [];

                $('input[name="add_insurance"]:checked').each(function () {
                    let parent = $(this).closest(".insurance-select");
                    let insuranceId = parent
                        .find('input[name="insurance_id[]"]')
                        .val();
                    let insurancePrice = parseFloat(
                        parent.find("h6").text().replace("$", "")
                    );
                    let insuranceType = parent
                        .find('input[name="insurance_type[]"]')
                        .val();

                    selectedInsurance.push({
                        id: insuranceId,
                        price: insurancePrice,
                        value: insuranceType,
                    });
                });

                finalFormData.append(
                    "insurance",
                    JSON.stringify(selectedInsurance)
                );

                let vehicleExtraPrice =
                    parseFloat($("#extra_price_total").val()) || 0;
                finalFormData.append("extra_price_total", vehicleExtraPrice);

                let vehicleInsurancePrice =
                    parseFloat($("#insurance_price_total").val()) || 0;
                finalFormData.append(
                    "insurance_price_total",
                    vehicleInsurancePrice
                );

                let vehicleDriverPrice =
                    parseFloat($("#driver_price_total").val()) || 0;
                finalFormData.append("driver_price_total", vehicleDriverPrice);

                let vehiclePrice = parseFloat($("#vehicle_price").val()) || 0;
                finalFormData.append("vehicle_price", vehiclePrice);

                let vehicleTotalPrice =
                    parseFloat($("#vehicle_price_total").val()) || 0;
                finalFormData.append("vehicle_price_total", vehicleTotalPrice);

                let totalPrice = parseFloat($("#total_price").val()) || 0;
                finalFormData.append("total_price", totalPrice);

                let totalTax = parseFloat($("#tax_val").val()) || 0;
                finalFormData.append("tax_val", totalTax);

                let rentType = $('input[name="rent_type"]:checked').val();
                finalFormData.append("rent_type", rentType ?? "");

                $("#sumbit_btn")
                    .text(_l("web.home.processing_please_wait"))
                    .prop("disabled", true);
                $(".backUserInfo").prop("disabled", true);
                $.ajax({
                    url: "/create/payments",
                    method: "POST",
                    data: finalFormData,
                    dataType: "json",
                    contentType: false,
                    processData: false,
                    cache: false,
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                })

                    .done((response, statusText, xhr) => {
                        if (response.code === 200 && response.cod) {
                            showToast("success", response.message);
                            window.location.href = response.redirect_url; // Redirect to success page
                        }

                        if (response.paypal_url) {
                            showToast("success", response.message);
                            window.location.href = response.paypal_url;
                        }

                        if (response.stripurl) {
                            showToast("success", response.message);
                            window.location.href = response.stripurl;
                        }
                    })
                    .fail((error) => {
                        $("#serviceLoader").hide();
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid");
                        $(".add_btn").removeAttr("disabled");
                        $(".add_btn").html(_l("web.common.submit"));

                        $("#sumbit_btn")
                            .text(_l("web.home.pay_and_place_reservation"))
                            .prop("disabled", false);
                        $(".backUserInfo").prop("disabled", false);

                        if (error.status === 422) {
                            if (error.responseJSON.errors) {
                                // Laravel field-level validation errors
                                $.each(
                                    error.responseJSON.errors,
                                    function (key, val) {
                                        $("#" + key).addClass("is-invalid");
                                        $("#" + key + "_error").text(val[0]);
                                    }
                                );
                            } else if (error.responseJSON.message) {
                                // Custom error message (like wallet balance)
                                showToast("error", error.responseJSON.message);
                            }
                        } else {
                            showToast(
                                "error",
                                error.responseJSON.message ||
                                    _l("web.home.something_went_wrong")
                            );
                        }
                    });
            }
        });
    });

    //----------------------------------------------------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------------------------------------------------

    $(document).ready(function () {
        function loadStates(
            countryId,
            selectedStateId = null,
            selectedCityId = null
        ) {
            let $stateDropdown = $("#state_id");
            $stateDropdown
                .prop("disabled", true)
                .html("<option>Loading...</option>");

            $.ajax({
                url: "/get-states/" + countryId,
                type: "GET",
                success: function (response) {
                    $stateDropdown
                        .empty()
                        .append('<option value="">Select State</option>');

                    if (response.length > 0) {
                        $.each(response, function (key, state) {
                            $stateDropdown.append(
                                `<option value="${state.id}" ${
                                    selectedStateId == state.id
                                        ? "selected"
                                        : ""
                                }>${state.name}</option>`
                            );
                        });

                        // Only load cities if this is from edit mode
                        if (selectedStateId && selectedCityId) {
                            loadCities(selectedStateId, selectedCityId);
                        }
                    }

                    $stateDropdown.prop("disabled", false);
                },
                error: function () {
                    alert("Failed to fetch states. Please try again.");
                    $stateDropdown
                        .html('<option value="">Select State</option>')
                        .prop("disabled", false);
                },
            });
        }

        function loadCities(stateId, selectedCityId = null) {
            let $cityDropdown = $("#city_id");
            $cityDropdown
                .prop("disabled", true)
                .html("<option>Loading...</option>");

            $.ajax({
                url: "/get-cities/" + stateId,
                type: "GET",
                success: function (response) {
                    $cityDropdown
                        .empty()
                        .append('<option value="">Select City</option>');

                    if (response.length > 0) {
                        $.each(response, function (key, city) {
                            $cityDropdown.append(
                                `<option value="${city.id}" ${
                                    selectedCityId == city.id ? "selected" : ""
                                }>${city.name}</option>`
                            );
                        });
                    }

                    $cityDropdown.prop("disabled", false);
                },
                error: function () {
                    alert("Failed to fetch cities. Please try again.");
                    $cityDropdown
                        .html('<option value="">Select City</option>')
                        .prop("disabled", false);
                },
            });
        }

        const selectedCountryId = $("#country_id").val();
        const selectedStateId = $("#selected_state_id").val();
        const selectedCityId = $("#selected_city_id").val();

        if (selectedCountryId && selectedStateId) {
            loadStates(selectedCountryId, selectedStateId, selectedCityId);
        }

        // On country change
        $("#country_id").on("change", function () {
            let countryId = $(this).val();
            $("#city_id").html('<option value="">Select City</option>'); // Reset city
            if (countryId) {
                $stateDropdown
                    .prop("disabled", true)
                    .html("<option>Loading...</option>");
                $cityDropdown.html(
                    `<option value="">${_l("web.home.select_city")}</option>`
                ); // Reset city dropdown

                $.ajax({
                    url: "/get-states/" + countryId,
                    type: "GET",
                    success: function (response) {
                        $stateDropdown
                            .empty()
                            .append(
                                `<option value="">${_l(
                                    "web.home.select_state"
                                )}</option>`
                            );

                        if (response.length > 0) {
                            $.each(response, function (key, state) {
                                $stateDropdown.append(
                                    '<option value="' +
                                        state.id +
                                        '">' +
                                        state.name +
                                        "</option>"
                                );
                            });
                        }

                        $stateDropdown.prop("disabled", false);
                    },
                    error: function () {
                        alert("Failed to fetch states. Please try again.");
                        $stateDropdown
                            .html(
                                `<option value="">${_l(
                                    "web.home.select_state"
                                )}</option>`
                            )
                            .prop("disabled", false);
                    },
                });

                loadStates(countryId);
            } else {
                $("#state_id").html(
                    `<option value="">${_l("web.home.select_state")}</option>`
                );
            }
        });

        // On state change
        $("#state_id").on("change", function () {
            let stateId = $(this).val();
            if (stateId) {
                $cityDropdown
                    .prop("disabled", true)
                    .html(`<option>${_l("web.home.loading")}</option>`);

                $.ajax({
                    url: "/get-cities/" + stateId,
                    type: "GET",
                    success: function (response) {
                        $cityDropdown
                            .empty()
                            .append(
                                `<option value="">${_l(
                                    "web.home.select_city"
                                )}</option>`
                            );

                        if (response.length > 0) {
                            $.each(response, function (key, city) {
                                $cityDropdown.append(
                                    '<option value="' +
                                        city.id +
                                        '">' +
                                        city.name +
                                        "</option>"
                                );
                            });
                        }

                        $cityDropdown.prop("disabled", false);
                    },
                    error: function () {
                        alert("Failed to fetch cities. Please try again.");
                        $cityDropdown
                            .html(
                                `<option value="">${_l(
                                    "web.home.select_city"
                                )}</option>`
                            )
                            .prop("disabled", false);
                    },
                });
                loadCities(stateId);
            } else {
                $("#city_id").html(
                    `<option value="">${_l("web.home.select_city")}</option>`
                );
            }
        });
    });

    $(document).ready(function () {
        $(".Number").on("input", function () {
            this.value = this.value.replace(/[^0-9]/g, ""); // Remove any non-numeric characters
        });
    });

    $(document).ready(function () {
        // Initialize Select2
        $("#delivery_location, #delivery_return_location").select2();
        $("#pickup_location, #pickup_return_location").select2();

        $("#delivery_remeber").change(function () {
            if ($(this).is(":checked")) {
                let locationValue = $("#delivery_location").val();
                $("#delivery_return_location")
                    .val(locationValue)
                    .trigger("change"); // Update Select2 correctly
            } else {
                $("#delivery_return_location").val("").trigger("change"); // Clear Select2 selection
            }
        });

        // Listen for Select2 change event
        $("#delivery_location").on("change", function () {
            if ($("#delivery_remeber").is(":checked")) {
                $("#delivery_return_location")
                    .val($(this).val())
                    .trigger("change");
            }
        });

        $("#pickup_remeber").change(function () {
            if ($(this).is(":checked")) {
                let locationValue = $("#pickup_location").val();
                $("#pickup_return_location")
                    .val(locationValue)
                    .trigger("change"); // Update Select2 correctly
            } else {
                $("#pickup_return_location").val("").trigger("change"); // Clear Select2 selection
            }
        });

        // Listen for Select2 change event
        $("#pickup_location").on("change", function () {
            if ($("#pickup_remeber").is(":checked")) {
                $("#pickup_return_location")
                    .val($(this).val())
                    .trigger("change");
            }
        });
    });
})();

$(document).ready(function () {
    $("#driver_file").on("change", function (event) {
        const files = event.target.files;
        const imagePreview = $(".imagePreview");
        imagePreview.html(""); // Clear previous preview

        if (files && files[0]) {
            const file = files[0];
            if (!file.type.match("image.*")) {
                $("#driver_file_error").text(
                    "Please upload a valid image file."
                );
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                const img = $("<img>", {
                    src: e.target.result,
                    class: "img-thumbnail",
                    width: 150,
                });
                imagePreview.append(img);
                $("#driver_file_error").text(""); // Clear error
            };
            reader.readAsDataURL(file);
        }
    });
});

$(document).ready(function () {
    // Hide all descriptions by default
    $(".more-adon-info").hide();

    // Toggle description and icon on click
    $(".adon-info-btn").on("click", function () {
        const $button = $(this);
        const $listItem = $button.closest("li");
        const $description = $listItem.find(".more-adon-info");
        const $icon = $button.find(".arrow-icon");

        $description.slideToggle(200); // toggle the description

        // Toggle icon class
        if ($icon.hasClass("bx-chevron-down")) {
            $icon.removeClass("bx-chevron-down").addClass("bx-chevron-up");
        } else {
            $icon.removeClass("bx-chevron-up").addClass("bx-chevron-down");
        }
    });
});
