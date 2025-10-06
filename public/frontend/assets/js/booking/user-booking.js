/* global $, loadTranslationFile,  document, showToast, FormData, window,  _l,  alert, FileReader*/

(async () => {
    "use strict";

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
        let basePrice = $("#total_price").val().replace(/,/g, "");

        let $extraChargesList = $(".extra-charges-list"); // Extra services list
        let $insuranceChargesList = $(".insurance-charges-list"); // Insurance list
        let $extraChargesTotal = $(".extra-services-total"); // Extra services total price
        let $insuranceChargesTotal = $(".insurance-total"); // Insurance total price
        let $totalPriceElement = $("#total_price"); // Hidden input for total price
        let $totalPriceExtra = $("#extra_price_total"); // Hidden input for total price
        let $totalPriceInsurance = $("#insurance_price_total"); // Hidden input for total price
        let $totalPriceSpan = $(".vehicle-total-price span"); // Display total price
        let $submitButton = $("#sumbit_btn"); // Display final price button

        let noServiceMessage = `<li class="no-service-message">${_l(
            "web.home.no_extra_service_added_to_this_vehicle"
        )}</li>`;
        let noInsuranceMessage = `<li class="no-insurance-message">${_l(
            "web.home.no_insurance_available"
        )}</li>`;

        let currencySymbol = $("#currency").val() || "$";

        function updateTotalPrice(includeTax = false) {
            const base = parseFloat(basePrice) || 0;
            const extra = parseFloat(totalExtraServicePrice) || 0;
            const insurance = parseFloat(totalInsurancePrice) || 0;

            let tax = 0;
            const $taxInput = $("#tax_val");

            if ($taxInput.length) {
                const rawTaxVal = $taxInput.val();
                // Only parse if it's a valid numeric string (no commas, etc.)
                if (!isNaN(rawTaxVal) && rawTaxVal.trim() !== "") {
                    tax = parseFloat(rawTaxVal);
                }
            }

            let finalTotal = base + extra + insurance;

            if (includeTax) {
                finalTotal += tax;
            }

            $totalPriceExtra.val(extra.toFixed(2));
            $totalPriceInsurance.val(insurance.toFixed(2));
            $totalPriceElement.val(finalTotal.toFixed(2));

            $totalPriceSpan.text(`${currencySymbol}${finalTotal.toFixed(2)}`);
            $submitButton.text(
                `${_l("web.home.pay")} ${currencySymbol}${finalTotal.toFixed(
                    2
                )} & ${_l("web.home.place_reservation")}`
            );
            $extraChargesTotal.text(`${currencySymbol}${extra.toFixed(2)}`);
            $insuranceChargesTotal.text(
                `${currencySymbol}${insurance.toFixed(2)}`
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
            $(this).addClass("d-none");
            parent.find(".remove-adon-btn").removeClass("d-none");

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
            $(this).addClass("d-none");
            parent.find(".add-addon-btn").removeClass("d-none");

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
        $(".booking-info-body").on("click", ".insurance-select", function (e) {
            e.preventDefault();
            e.stopPropagation();

            const $card = $(this);
            const insuranceId = $card
                .find("input[name='insurance_id[]']")
                .val();
            const insuranceName = $card.find("p.fs-14").text().trim();
            const insuranceType = $card
                .find("input[name='insurance_type[]']")
                .val();
            const insurancePriceRaw = parseFloat(
                $card.find("input[name='insurance_price[]']").val()
            );
            let insurancePrice = 0;
            const base = parseFloat(basePrice) || 0;

            if (insuranceType === "Percentage") {
                insurancePrice = (base * insurancePriceRaw) / 100;
            } else {
                insurancePrice = insurancePriceRaw;
            }

            const isActive = $card.hasClass("active");

            if (!isActive) {
                // Deselect all other cards
                $(".insurance-select")
                    .removeClass("active")
                    .find("input[name='add_insurance']")
                    .prop("checked", false);

                // Clear the insurance charges list and reset total
                $insuranceChargesList.empty();
                totalInsurancePrice = 0;

                // Select this card
                $card.addClass("active");
                $card.find("input[name='add_insurance']").prop("checked", true);

                const displayPrice =
                    insuranceType === "Percentage"
                        ? `${insurancePriceRaw}% (${currencySymbol}${insurancePrice.toFixed(
                              2
                          )})`
                        : `${currencySymbol}${insurancePrice.toFixed(2)}`;

                $insuranceChargesList.append(`
            <li data-insurance-id="${insuranceId}">
                <h6>${insuranceName}</h6>
                <h5>${displayPrice}</h5>
            </li>
        `);

                totalInsurancePrice += insurancePrice;
            } else {
                // Unselect if the same card is clicked again (optional - can be removed for strict radio behavior)
                $card.removeClass("active");
                $card
                    .find("input[name='add_insurance']")
                    .prop("checked", false);
                $insuranceChargesList
                    .find(`li[data-insurance-id="${insuranceId}"]`)
                    .remove();
                totalInsurancePrice -= insurancePrice;
            }

            checkEmptyCart();
            updateTotalPrice();
        });

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
                    min: 20,
                },
                driver_file: {
                    required: false,
                    extension: "jpg|jpeg|png",
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

        // Utility Functions
        function getNumericVal(selector) {
            let val = $(selector).val();
            return parseFloat(val.replace(/,/g, "")) || 0;
        }

        function setTotalPriceDisplay(total) {
            $(".vehicle-total-price span").text(total.toFixed(2));
            $("#total_price").val(total.toFixed(2));
        }

        function removeTaxFromTotal() {
            let currentTotal = getNumericVal("#total_price");
            let taxVal = getNumericVal("#tax_val");

            let newTotal = currentTotal - taxVal;
            if (newTotal < 0) newTotal = 0;
            setTotalPriceDisplay(newTotal);
        }

        document
            .getElementById("removeTax")
            .addEventListener("click", removeTaxFromTotal);

        $(".userInfoBtn").on("click", function (event) {
            event.preventDefault();

            let $form = $("#bookExtraDetailsForm");

            if ($form.valid()) {
                let carExtraInfoFormData = $form.serializeArray();
                let formDataCollection = {};
                carExtraInfoFormData.forEach(function (item) {
                    formDataCollection[item.name] = item.value;
                });
                // Add tax once after form is valid
                updateTotalPrice(true);

                // UI transitions
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
                    minlength: 10,
                    maxlength: 15,
                },
                terms: {
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
                    minlength: _l("web.home.min_length_10"),
                    maxlength: _l("web.home.max_length_15"),
                },
                email: {
                    required: _l("web.home.email_required"),
                    email: _l("web.home.valid_email"),
                },
                phone_number: {
                    required: _l("web.home.mobile_number_required"),
                },
                terms: {
                    required: _l("web.home.terms_required"),
                },
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("text-danger");

                if (element.attr("type") === "checkbox") {
                    element.closest(".custom_check").append(error);
                } else if (element.attr("type") === "file") {
                    $("#driver_file_error").html(error);
                } else if (element.hasClass("select2-hidden-accessible") || $("#" + element.attr("id") + "_error").length) {
                    const errorId = element.attr("id") + "_error";
                    $("#" + errorId).text(error.text());
                } else {
                    element.after(error);
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
                let errorId = element.id + "_error";
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

            if (!$("#bookPaymentForm").valid()) return;

            const finalFormData = prepareFinalFormData();

            disableSubmitButton();

            sendPaymentRequest(finalFormData);
        });

        // Prepare FormData with all sections, extras, insurance, and prices
        function prepareFinalFormData() {
            const finalFormData = new FormData();
            finalFormData.append("_token", $("meta[name='csrf-token']").attr("content"));

            const sections = [
                "#bookLocationForm",
                "#bookExtraDetailsForm",
                "#bookUserInfoForm",
                "#bookPaymentForm"
            ];

            sections.forEach(formSelector => {
                $(formSelector).serializeArray().forEach(item => {
                    finalFormData.append(item.name, item.value);
                });
            });

            finalFormData.append("extra_services", JSON.stringify(getSelectedExtras()));
            finalFormData.append("insurance", JSON.stringify(getSelectedInsurance()));

            // Append numeric fields
            const fields = [
                "extra_price_total",
                "insurance_price_total",
                "driver_price_total",
                "vehicle_price",
                "vehicle_price_total",
                "total_price",
                "tax_val"
            ];

            fields.forEach(fieldId => {
                finalFormData.append(fieldId, parseFloat($(`#${fieldId}`).val()) || 0);
            });

            const rentType = $("input[name='rent_type']:checked").val();
            finalFormData.append("rent_type", rentType ?? "");

            return finalFormData;
        }

        // Collect selected extras
        function getSelectedExtras() {
            const extras = [];
            $("input[name='add_extra']:checked").each(function () {
                const parent = $(this).closest("li");
                extras.push({
                    id: parent.data("service-id"),
                    price: parseFloat(parent.find(".adon-price").text().replace("$", "")),
                    value: parent.find("input[name='extra_type[]']").val(),
                });
            });
            return extras;
        }

        // Collect selected insurance
        function getSelectedInsurance() {
            const insurance = [];
            $("input[name='add_insurance']:checked").each(function () {
                const parent = $(this).closest(".insurance-select");
                const insuranceId = parent.find("input[name='insurance_id[]']").val();
                insurance.push({
                    id: insuranceId,
                    price: parseFloat(parent.find("h6").text().replace("$", "")),
                    value: insuranceId,
                });
            });
            return insurance;
        }

        // Disable submit button and navigation
        function disableSubmitButton() {
            $("#sumbit_btn")
                .text(_l("web.home.processing_please_wait"))
                .prop("disabled", true);
            $(".backUserInfo").prop("disabled", true);
        }

        // Send AJAX request
        function sendPaymentRequest(finalFormData) {
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
                    "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content"),
                },
            })
                .done(handlePaymentSuccess)
                .fail(handlePaymentError);
        }

        // Handle successful payment response
        function handlePaymentSuccess(response) {
            if (response.code === 200 && response.cod) {
                showToast("success", response.message);
                window.location.href = response.redirect_url;
                return;
            }
            if (response.paypal_url) {
                showToast("success", response.message);
                window.location.href = response.paypal_url;
                return;
            }
            if (response.stripurl) {
                showToast("success", response.message);
                window.location.href = response.stripurl;
            }
        }

        // Handle failed payment response
        function handlePaymentError(error) {
            $("#serviceLoader").hide();
            $(".error-text").text("");
            $(".form-control").removeClass("is-invalid");
            $(".add_btn").removeAttr("disabled").html(_l("web.common.submit"));

            $("#sumbit_btn")
                .text(_l("web.home.pay_and_place_reservation"))
                .prop("disabled", false);
            $(".backUserInfo").prop("disabled", false);

            if (error.status === 422 && error.responseJSON.errors) {
                // Laravel validation errors
                $.each(error.responseJSON.errors, (key, val) => {
                    $(`#${key}`).addClass("is-invalid");
                    $(`#${key}_error`).text(val[0]);
                });
            } else {
                showToast(
                    "error",
                    error.responseJSON?.message || _l("web.home.something_went_wrong")
                );
            }
        }
    });

    let $stateDropdown = $("#state_id");
    let $cityDropdown = $("#city_id");

    function loadStates(
        countryId,
        selectedStateId = null,
        selectedCityId = null
    ) {
        $stateDropdown
            .prop("disabled", true)
            .html("<option>Loading...</option>");

        $.ajax({
            url: "/get-states/" + countryId,
            type: "GET",
            success: function (response) {
                $stateDropdown
                    .empty()
                    .append("<option value=\"\">Select State</option>");

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
                    .html("<option value=\"\">Select State</option>")
                    .prop("disabled", false);
            },
        });
    }

    function loadCities(stateId, selectedCityId = null) {
        $cityDropdown
            .prop("disabled", true)
            .html("<option>Loading...</option>");

        $.ajax({
            url: "/get-cities/" + stateId,
            type: "GET",
            success: function (response) {
                $cityDropdown
                    .empty()
                    .append("<option value=\"\">Select City</option>");

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
                    .html("<option value=\"\">Select City</option>")
                    .prop("disabled", false);
            },
        });
    }

    $(document).ready(function () {

        const selectedCountryId = $("#country_id").val();
        const selectedStateId = $("#selected_state_id").val();
        const selectedCityId = $("#selected_city_id").val();

        if (selectedCountryId && selectedStateId) {
            loadStates(selectedCountryId, selectedStateId, selectedCityId);
        }

        // On country change
        $("#country_id").on("change", function () {
            const countryId = $(this).val();
            resetCityDropdown();

            if (!countryId) {
                resetStateDropdown();
                return;
            }

            loadStatesForCountry(countryId);
        });

        // Resets state dropdown
        function resetStateDropdown() {
            $("#state_id").html(`<option value="">${_l("web.home.select_state")}</option>`);
        }

        // Load states via AJAX
        function loadStatesForCountry(countryId) {
            $stateDropdown.prop("disabled", true).html("<option>Loading...</option>");
            resetCityDropdown();

            $.ajax({
                url: "/get-states/" + countryId,
                type: "GET",
                success: handleStateSuccess,
                error: handleStateError,
            });

            loadStates(countryId); // Existing function call if needed
        }

        // Handle AJAX success
        function handleStateSuccess(response) {
            $stateDropdown.empty().append(`<option value="">${_l("web.home.select_state")}</option>`);

            if (response.length > 0) {
                response.forEach(state => {
                    $stateDropdown.append(`<option value="${state.id}">${state.name}</option>`);
                });
            }

            $stateDropdown.prop("disabled", false);
        }

        // Handle AJAX error
        function handleStateError() {
            alert("Failed to fetch states. Please try again.");
            $stateDropdown.html(`<option value="">${_l("web.home.select_state")}</option>`).prop("disabled", false);
        }

        // On state change
        $("#state_id").on("change", function () {
            const stateId = $(this).val();
            resetCityDropdown();

            if (!stateId) return;

            loadCitiesForState(stateId);
        });

        // Resets city dropdown
        function resetCityDropdown() {
            $cityDropdown.html(`<option value="">${_l("web.home.select_city")}</option>`);
        }

        // Load cities via AJAX
        function loadCitiesForState(stateId) {
            $cityDropdown.prop("disabled", true).html(`<option>${_l("web.home.loading")}</option>`);

            $.ajax({
                url: "/get-cities/" + stateId,
                type: "GET",
                success: handleCitySuccess,
                error: handleCityError,
            });

            loadCities(stateId); // Existing function call if needed
        }

        // Handle AJAX success
        function handleCitySuccess(response) {
            $cityDropdown.empty().append(`<option value="">${_l("web.home.select_city")}</option>`);

            if (response.length > 0) {
                response.forEach(city => {
                    $cityDropdown.append(`<option value="${city.id}">${city.name}</option>`);
                });
            }

            $cityDropdown.prop("disabled", false);
        }

        // Handle AJAX error
        function handleCityError() {
            alert("Failed to fetch cities. Please try again.");
            $cityDropdown.html(`<option value="">${_l("web.home.select_city")}</option>`).prop("disabled", false);
        }

        $(".Number").on("input", function () {
            this.value = this.value.replace(/\D/g, ""); // Remove any non-numeric characters
        });

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

        // Driver file upload
        $("#driver_file").on("change", function (event) {
            const file = event.target.files?.[0]; // Optional chaining
            const imagePreview = $(".imagePreview");
            imagePreview.html(""); // Clear previous preview

            if (!file) return;

            if (!file.type?.match("image.*")) { // Optional chaining
                $("#driver_file_error").text("Please upload a valid image file.");
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
        });

        $(".more-adon-info").hide();

        $(".adon-info-btn").on("click", function () {
            const $button = $(this);
            const $listItem = $button.closest("li");
            const $description = $listItem.find(".more-adon-info");
            const $icon = $button.find(".arrow-icon");

            $description.slideToggle(200);

            if ($icon.hasClass("bx-chevron-down")) {
                $icon.removeClass("bx-chevron-down").addClass("bx-chevron-up");
            } else {
                $icon.removeClass("bx-chevron-up").addClass("bx-chevron-down");
            }
        });

        $(".show-benefits-link").on("click", function (e) {
            e.preventDefault();
            const insuranceId = $(this).data("insurance-id");

            showBenefitLoading();
            fetchInsuranceBenefits(insuranceId);
        });

        // Show loading spinner in benefit list
        function showBenefitLoading() {
            $("#benefit-list").html(`
                <div class="d-flex justify-content-center py-3">
                    <div class="spinner-border text-warning" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `);
        }

        // Fetch insurance benefits via AJAX
        function fetchInsuranceBenefits(insuranceId) {
            $.ajax({
                type: "POST",
                url: "/get/benefits",
                data: {
                    id: insuranceId,
                    _token: $("meta[name=\"csrf-token\"]").attr("content"),
                },
                success: handleBenefitSuccess,
            });
        }

        // Handle AJAX success
        function handleBenefitSuccess(response) {
            const $list = $("#benefit-list");
            $list.empty();

            if (response.length > 0) {
                response.forEach((item, index) => {
                    const $li = $("<li class=\"mb-2\"></li>").text(`${index + 1}. ${item.benefit}`);
                    $list.append($li);
                });
            } else {
                $list.append($("<li></li>").text("No benefits available."));
            }

            $("#show_benifit").modal("show");
        }
    });
})();
