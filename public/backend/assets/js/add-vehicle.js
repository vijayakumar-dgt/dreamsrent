(async () => {
    "use strict";

    await loadTranslationFile("admin", "rentals, common");

    const permissions = await loadUserPermissions();

    $(document).ready(function () {
        let currency = $("#currency").val();

        const titleInput = document.getElementById("title");
        const permalinkInput = document.getElementById("perma_link");
        const previewLink = document.querySelector(".link-info");

        if (titleInput && permalinkInput && previewLink) {
            titleInput.addEventListener("input", function () {
                let slug = titleInput.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, "")
                    .replace(/\s+/g, "-")
                    .replace(/-+/g, "-");

                let baseUrl = "https://www.example.com/cars/";
                let fullUrl = baseUrl + slug;

                permalinkInput.value = fullUrl;
                previewLink.href = fullUrl;
                previewLink.textContent = fullUrl;
            });
        }

        const checkboxes = document.querySelectorAll(".price-checkbox");
        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener("change", function () {
                let priceInput = document.getElementById(this.name + "_price");

                if (priceInput) {
                    if (this.checked) {
                        priceInput.removeAttribute("disabled"); // Enable the input
                    } else {
                        priceInput.setAttribute("disabled", "true"); // Disable the input
                        priceInput.value = ""; // Clear the input value
                    }
                }
            });
        });

        document.querySelectorAll(".priceLimit").forEach((input) => {
            input.addEventListener("input", function () {
                this.value = this.value.replace(/\D/g, "").slice(0, 5);
            });
        });

        $(".summernote").summernote({
            height: 200,
            placeholder: _l("admin.rentals.summer_des"),
            toolbar: [
                ["style", ["bold", "italic", "underline", "clear"]],
                ["para", ["ul", "ol", "paragraph"]],
                ["insert", ["link", "picture", "video"]],
                ["view", ["fullscreen", "codeview", "help"]],
            ],
        });

        $("#selectall_feature").on("change", function () {
            $(".singleCheckbox").prop("checked", $(this).prop("checked"));
        });

        $(".singleCheckbox").on("change", function () {
            if (
                $(".singleCheckbox:checked").length ===
                $(".singleCheckbox").length
            ) {
                $("#selectall_feature").prop("checked", true);
            } else {
                $("#selectall_feature").prop("checked", false);
            }
        });

        $("#carBasicInfoForm").validate({
            rules: {
                vehicle_image: {
                    required: false,
                },
                title: {
                    required: false,
                    minlength: 3,
                    maxlength: 50,
                },
                perma_link: {
                    required: false,
                    url: false,
                },
                vehicle_type_id: {
                    required: false,
                },
                vehicle_brand_id: {
                    required: false,
                },
                vehicle_model_id: {
                    required: false,
                },
                vehicle_category_id: {
                    required: false,
                },
                plate_number: {
                    required: false,
                },
                vin_number: {
                    required: false,
                },
                main_location_id: {
                    required: false,
                },
                other_location: {
                    required: false,
                },
                vehicle_fuel_id: {
                    required: false,
                },
                odometer: {
                    required: false,
                },
                vehicle_color_id: {
                    required: false,
                },
                vehicle_year: {
                    required: false,
                },
                vehicle_passenger: {
                    required: false,
                },
            },
            messages: {
                vehicle_image: {
                    required: _l("admin.rentals.vehicle_image_required"),
                },
                title: {
                    required: _l("admin.rentals.title_required"),
                    minlength: _l("admin.rentals.title_minlength"),
                    maxlength: _l("admin.rentals.title_maxlength"),
                },
                perma_link: {
                    url: _l("admin.rentals.permalink_invalid"),
                },
                vehicle_type_id: {
                    required: _l("admin.rentals.vehicle_type_required"),
                },
                vehicle_brand_id: {
                    required: _l("admin.rentals.vehicle_brand_required"),
                },
                vehicle_model_id: {
                    required: _l("admin.rentals.vehicle_model_required"),
                },
                vehicle_category_id: {
                    required: _l("admin.rentals.vehicle_category_required"),
                },
                main_location_id: {
                    required: _l("admin.rentals.main_location_required"),
                },
                vehicle_color_id: {
                    required: _l("admin.rentals.vehicle_color_required"),
                },
                vehicle_year: {
                    required: _l("admin.rentals.vehicle_year_required"),
                },
                vehicle_passenger: {
                    required: _l("admin.rentals.vehicle_passenger_required"),
                },
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                if (element.hasClass("select2-hidden-accessible")) {
                    var errorId = element.attr("id") + "_error";
                    $("#" + errorId).text(error.text());
                } else if (element.attr("name") === "vehicle_image") {
                    $("#vehicle_image_error_container").html(error);
                } else {
                    error.addClass("text-danger");
                    element.closest(".mb-3").append(error);
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
        });

        $(".select").on("change", function () {
            $(this).valid();
        });

        $("#vehicle_image").on("change", function () {
            var file = this.files[0];
            if (file) {
                var img = new Image();
                img.src = URL.createObjectURL(file);
                img.onload = function () {
                    if (this.width !== 690 || this.height !== 420) {
                        $("#vehicle_image_error_container").html(
                            '<span class="text-danger">The image must be 690px × 420px.</span>'
                        );
                        $("#vehicle_image").val("");
                    } else {
                        $("#vehicle_image_error_container").html("");
                    }
                };
            }
        });

        $("#featAmenNext").on("click", function (event) {
            event.preventDefault();

            let carBasicInfoFormDate = $("#carBasicInfoForm").serializeArray();

            if ($("#carBasicInfoForm").valid()) {
                let formDataCollection = {};
                carBasicInfoFormDate.forEach(function (item) {
                    formDataCollection[item.name] = item.value;
                });

                $("#first-field").hide();
                $("#second-field").show();
                $("#firstBar").removeClass("active").addClass("activated");
                $("#secondBar").addClass("active");
            }
        });

        $("#priceTariffNext").on("click", function (event) {
            event.preventDefault();

            let carBasicInfoFormData = $("#featuresForm").serializeArray();
            let formDataCollection = {};

            carBasicInfoFormData.forEach(function (item) {
                if (formDataCollection[item.name]) {
                    if (!Array.isArray(formDataCollection[item.name])) {
                        formDataCollection[item.name] = [
                            formDataCollection[item.name],
                        ];
                    }
                    formDataCollection[item.name].push(item.value);
                } else {
                    formDataCollection[item.name] = item.value;
                }
            });

            if ($("#featuresForm").valid()) {
                $("#second-field").hide();
                $("#third-field").show();
                $("#secondBar").removeClass("active").addClass("activated");
                $("#thirdBar").addClass("active");
            }
        });

        $("#priceTariffForm").validate({
            rules: {
                daily: {
                    required: false,
                },
                weekly: {
                    required: false,
                },
                montly: {
                    required: false,
                },
                yearly: {
                    required: false,
                },
                daily_price: {
                    required: true,
                },
                weekly_price: {
                    required: true,
                },
                montly_price: {
                    required: true,
                },
                yearly_price: {
                    required: true,
                },
                unlimited: {
                    required: false,
                },
                basic_kilometer: {
                    required: true,
                },
                extra_kilometer: {
                    required: true,
                },
            },
            messages: {
                daily_price: {
                    required: _l("admin.rentals.daily_price_required"),
                },
                weekly_price: {
                    required: _l("admin.rentals.weekly_price_required"),
                },
                montly_price: {
                    required: _l("admin.rentals.monthly_price_required"),
                },
                yearly_price: {
                    required: _l("admin.rentals.yearly_price_required"),
                },
                basic_kilometer: {
                    required: _l("admin.rentals.basic_kilometer_required"),
                },
                extra_kilometer: {
                    required: _l("admin.rentals.extra_kilometer_required"),
                },
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("text-danger");
                element.closest(".mb-3").append(error);
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
            },
        });

        $("#extraServiceNext").on("click", function (event) {
            event.preventDefault();

            let priceTypes = ["daily", "weekly", "montly", "yearly"];
            let priceValues = [
                "daily_price",
                "weekly_price",
                "montly_price",
                "yearly_price",
            ];

            let hasPriceType = priceTypes.some((priceType) => {
                return $(`[name="${priceType}"]`).is(":checked");
            });

            let hasPriceValue = priceValues.some((priceValue) => {
                return $(`[name="${priceValue}"]`).val().trim() !== "";
            });

            if (!hasPriceType || !hasPriceValue) {
                showToast(
                    "error",
                    "Please select at least one price type and enter a corresponding price."
                );
                return;
            }

            let carBasicInfoFormDate = $("#priceTariffForm").serializeArray();

            if ($("#priceTariffForm").valid()) {
                let formDataCollection = {};
                carBasicInfoFormDate.forEach(function (item) {
                    formDataCollection[item.name] = item.value;
                });

                $("#third-field").hide();
                $("#forth-field").show();
                $("#thirdBar").removeClass("active").addClass("activated");
                $("#forthBar").addClass("active");
            }
        });

        let editingId = null;
        let deletingId = null;

        $("#price_btn").on("click", function () {
            $(".noDataS").html(""); // Clear previous entries
            let seasonName = $("#s_name").val();
            let startDate = $("#s_strdate").val();
            let endDate = $("#s_enddate").val();
            let dailyRate = $("#s_drate").val();
            let weeklyRate = $("#s_wrate").val();
            let monthlyRate = $("#s_mrate").val();
            let lateFee = $("#s_lrate").val();

            if (
                !seasonName ||
                !startDate ||
                !endDate ||
                !dailyRate ||
                !weeklyRate ||
                !monthlyRate ||
                !lateFee
            ) {
                showToast("error", "Please fill in all required fields.");
                return;
            }

            if (editingId) {
                let editElement = $("#" + editingId);

                editElement.find("h6").text(seasonName);
                editElement.find(".start-date span").text(startDate);
                editElement.find(".end-date span").text(endDate);
                editElement
                    .find(".daily-price span")
                    .text(`${currency}${dailyRate}`);
                editElement
                    .find(".weekly-price span")
                    .text(`${currency}${weeklyRate}`);
                editElement
                    .find(".monthly-price span")
                    .text(`${currency}${monthlyRate}`);
                editElement
                    .find(".late-fee span")
                    .text(`${currency}${lateFee}`);

                editElement
                    .find("input[name='seasonal_title[]']")
                    .val(seasonName);
                editElement
                    .find("input[name='seasonal_start_date[]']")
                    .val(startDate);
                editElement
                    .find("input[name='seasonal_end_date[]']")
                    .val(endDate);
                editElement
                    .find("input[name='seasonal_daily_rate[]']")
                    .val(dailyRate);
                editElement
                    .find("input[name='seasonal_weekly_rate[]']")
                    .val(weeklyRate);
                editElement
                    .find("input[name='seasonal_monthly_rate[]']")
                    .val(monthlyRate);
                editElement
                    .find("input[name='seasonal_late_fee[]']")
                    .val(lateFee);

                $("#seas_title").text("Create Seasonal Pricing");
                $("#price_btn").text("Create New");
                editingId = null;
            } else {
                let uniqueId = `season_${crypto.randomUUID()}`;
                let newSeasonalPricing = `
<div id="${uniqueId}" class="d-flex align-items-center justify-content-between flex-wrap bg-white gap-3 border br-5 p-20 mb-1">
    <div>
        <input type="hidden" name="seasonal_id[]" value="">
        <h6 class="fs-14 fw-semibold mb-1">${seasonName}</h6>
        <input type="hidden" name="seasonal_title[]" value="${seasonName}">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <p class="fs-13 fw-medium border-end pe-2 mb-0 start-date">
                ${_l(
                    "admin.rentals.start_date"
                )} : <span class="text-gray-9">${startDate}</span>
                <input type="hidden" name="seasonal_start_date[]" value="${startDate}">
            </p>
            <p class="fs-13 fw-medium border-end pe-2 mb-0 end-date">
                ${_l(
                    "admin.rentals.end_date"
                )} : <span class="text-gray-9">${endDate}</span>
                <input type="hidden" name="seasonal_end_date[]" value="${endDate}">
            </p>
            <p class="fs-13 fw-medium border-end pe-2 mb-0 daily-price">
                ${_l(
                    "admin.rentals.seasonal_daily_price"
                )} : <span class="text-gray-9">${currency}${dailyRate}</span>
                <input type="hidden" name="seasonal_daily_rate[]" value="${dailyRate}">
            </p>
            <p class="fs-13 fw-medium border-end pe-2 mb-0 weekly-price">
                ${_l(
                    "admin.rentals.seasonal_weekly_price"
                )} : <span class="text-gray-9">${currency}${weeklyRate}</span>
                <input type="hidden" name="seasonal_weekly_rate[]" value="${weeklyRate}">
            </p>
            <p class="fs-13 fw-medium border-end pe-2 mb-0 monthly-price">
                ${_l(
                    "admin.rentals.seasonal_monthly_price"
                )} : <span class="text-gray-9">${currency}${monthlyRate}</span>
                <input type="hidden" name="seasonal_monthly_rate[]" value="${monthlyRate}">
            </p>
            <p class="fs-13 fw-medium pe-2 mb-0 late-fee">
                ${_l(
                    "admin.rentals.seasonal_late_fee"
                )} : <span class="text-gray-9">${currency}${lateFee}</span>
                <input type="hidden" name="seasonal_late_fee[]" value="${lateFee}">
            </p>
        </div>
    </div>
    <div class="d-flex align-items-center icon-list">
        <a href="#" class="edit-icon me-2" data-id="${uniqueId}" data-bs-toggle="modal" data-bs-target="#add_price">
            <i class="ti ti-edit"></i>
        </a>
        <a href="#" class="trash-icon" data-id="${uniqueId}" data-bs-toggle="modal" data-bs-target="#delete_price">
            <i class="ti ti-trash"></i>
        </a>
    </div>
</div>`;
                $("#seasonal_append").append(newSeasonalPricing);
            }

            $("#add_price").modal("hide");
            $("#add_price input").val("");
        });

        $(document).on("click", ".edit-icon", function () {
            editingId = $(this).data("id");
            let editElement = $("#" + editingId);

            $("#s_name").val(editElement.find("h6").text());
            $("#s_strdate").val(editElement.find(".start-date span").text());
            $("#s_enddate").val(editElement.find(".end-date span").text());
            $("#s_drate").val(
                editElement
                    .find(".daily-price span")
                    .text()
                    .replace(currency, "")
            );
            $("#s_wrate").val(
                editElement
                    .find(".weekly-price span")
                    .text()
                    .replace(currency, "")
            );
            $("#s_mrate").val(
                editElement
                    .find(".monthly-price span")
                    .text()
                    .replace(currency, "")
            );
            $("#s_lrate").val(
                editElement.find(".late-fee span").text().replace(currency, "")
            );

            $("#seas_title").text("Edit Seasonal Pricing");
            $("#price_btn").text("Update");
        });

        $(document).on("click", ".trash-icon", function () {
            deletingId = $(this).data("id");
        });

        $("#delete_price .btn-primary").on("click", function () {
            if (deletingId) {
                $("#" + deletingId).remove();
                deletingId = null;
            }
            $("#delete_price").modal("hide");
        });

        let editingTariffId = null;
        let deletingTariffId = null;

        $("#tarrif_btn").on("click", function () {
            $(".noDataT").html(""); // Clear previous entries
            let tariffName = $("#t_name").val();
            let dailyPrice = $("#t_price").val();
            let fromDays = $("#t_fromday").val();
            let toDays = $("#t_today").val();
            let baseKilometers = $("#t_base").val();
            let extraPrice = $("#t_extra").val();
            let isUnlimited = $("#unlimited1").prop("checked")
                ? "Unlimited"
                : baseKilometers;

            if (
                !tariffName ||
                !dailyPrice ||
                !fromDays ||
                !toDays ||
                (!isUnlimited && !baseKilometers) ||
                !extraPrice
            ) {
                showToast("error", "Please fill in all required fields.");
                return;
            }

            if (editingTariffId) {
                let editElement = $("#" + editingTariffId);

                editElement.find("h6").text(tariffName);
                editElement
                    .find(".daily-price span")
                    .text(`${currency}${dailyPrice}`);
                editElement
                    .find(".extra-price span")
                    .text(`${currency}${extraPrice}`);
                editElement.find(".from-days span").text(fromDays);
                editElement.find(".to-days span").text(toDays);
                editElement.find(".base-km span").text(isUnlimited);

                editElement
                    .find("input[name='tariff_title[]']")
                    .val(tariffName);
                editElement
                    .find("input[name='tariff_daily_price[]']")
                    .val(dailyPrice);
                editElement
                    .find("input[name='tariff_from_days[]']")
                    .val(fromDays);
                editElement.find("input[name='tariff_to_days[]']").val(toDays);
                editElement
                    .find("input[name='tariff_base_km[]']")
                    .val(isUnlimited);
                editElement
                    .find("input[name='tariff_extra_price[]']")
                    .val(extraPrice);

                $("#tarrif_title").text("Add New Tariff");
                $("#tarrif_btn").text("Create Tariff");
                editingTariffId = null;
            } else {
                let uniqueId = `tariff_${crypto.randomUUID()}`;

                let newTariff = `
            <div id="${uniqueId}" class="d-flex align-items-center justify-content-between flex-wrap bg-white gap-3 border br-5 p-20 mb-1">
                <div>
                    <input type="hidden" name="tariff_id[]" value="">
                    <h6 class="fs-14 fw-semibold mb-1">${tariffName}</h6>
                    <input type="hidden" name="tariff_title[]" value="${tariffName}">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <p class="fs-13 fw-medium border-end pe-2 mb-0 daily-price">
                            ${_l(
                                "admin.rentals.day_price"
                            )} : <span class="text-gray-9">${currency}${dailyPrice}</span>
                            <input type="hidden" name="tariff_daily_price[]" value="${dailyPrice}">
                        </p>
                        <p class="fs-13 fw-medium border-end pe-2 mb-0 from-days">
                            ${_l(
                                "admin.rentals.from_days"
                            )} : <span class="text-gray-9">${fromDays}</span>
                            <input type="hidden" name="tariff_from_days[]" value="${fromDays}">
                        </p>
                        <p class="fs-13 fw-medium border-end pe-2 mb-0 to-days">
                            ${_l(
                                "admin.rentals.to_days"
                            )} : <span class="text-gray-9">${toDays}</span>
                            <input type="hidden" name="tariff_to_days[]" value="${toDays}">
                        </p>
                        <p class="fs-13 fw-medium border-end pe-2 mb-0 base-km">
                            ${_l(
                                "admin.rentals.base_km"
                            )} : <span class="text-gray-9">${isUnlimited}</span>
                            <input type="hidden" name="tariff_base_km[]" value="${isUnlimited}">
                        </p>
                        <p class="fs-13 fw-medium pe-2 mb-0 extra-price">
                            ${_l(
                                "admin.rentals.extra_price"
                            )} : <span class="text-gray-9">${currency}${extraPrice}</span>
                            <input type="hidden" name="tariff_extra_price[]" value="${extraPrice}">
                        </p>
                    </div>
                </div>
                <div class="d-flex align-items-center icon-list">
                    <a href="#" class="edit-tariff me-2" data-id="${uniqueId}" data-bs-toggle="modal" data-bs-target="#add-tarrif">
                        <i class="ti ti-edit"></i>
                    </a>
                    <a href="#" class="trash-tariff" data-id="${uniqueId}" data-bs-toggle="modal" data-bs-target="#delete_tarrif">
                        <i class="ti ti-trash"></i>
                    </a>
                </div>
            </div>`;

                $("#tariff_append").append(newTariff);
            }

            $("#add-tarrif").modal("hide");
            $("#add-tarrif input").val("");
            $("#unlimited1").prop("checked", false);
            $("#t_base").prop("disabled", false);
        });

        $(document).on("click", ".edit-tariff", function () {
            editingTariffId = $(this).data("id");
            let editElement = $("#" + editingTariffId);

            $("#t_name").val(editElement.find("h6").text());
            $("#t_price").val(editElement.find(".daily-price span").text().replace(currency, ""));
            $("#t_fromday").val(editElement.find(".from-days span").text());
            $("#t_today").val(editElement.find(".to-days span").text());

            let baseKmValue = editElement.find(".base-km span").text();
            if (baseKmValue === "Unlimited") {
                $("#unlimited1").prop("checked", true);
                $("#t_base").val("").prop("disabled", true);
            } else {
                $("#unlimited1").prop("checked", false);
                $("#t_base").val(baseKmValue).prop("disabled", false);
            }

            $("#t_extra").val(editElement.find(".extra-price span").text().replace(currency, ""));

            $("#tarrif_title").text("Edit Tariff");
            $("#tarrif_btn").text("Update");
        });

        $(document).on("click", ".trash-tariff", function () {
            deletingTariffId = $(this).data("id");
        });

        $("#delete_tarrif .btn-primary").on("click", function () {
            if (deletingTariffId) {
                $("#" + deletingTariffId).fadeOut(300, function () {
                    $(this).remove();
                });
                deletingTariffId = null;
            }
            $("#delete_tarrif").modal("hide");
        });

        $("#unlimited1").on("change", function () {
            if ($(this).prop("checked")) {
                $("#t_base").val("").prop("disabled", true);
            } else {
                $("#t_base").prop("disabled", false);
            }
        });

        $("#carDocumentForm").validate({
            rules: {
                "car_document[]": {
                    required: false,
                    extension: "pdf|txt|doc|docx",
                },
                "policy_document[]": {
                    required: false,
                    extension: "pdf|txt|doc|docx",
                },
                "car_images[]": {
                    required: false,
                    extension: "jpg|jpeg|png",
                },
                car_video: {
                    required: false,
                    url: true,
                },
            },
            messages: {
                "car_document[]": {
                    required: _l("admin.rentals.document_required"),
                    extension: _l("admin.rentals.document_extension_invalid"),
                },
                "policy_document[]": {
                    required: _l("admin.rentals.policy_required"),
                    extension: _l("admin.rentals.policy_extension_invalid"),
                },
                "car_images[]": {
                    required: _l("admin.rentals.image_required"),
                    extension: _l("admin.rentals.image_extension_invalid"),
                },
                car_video: {
                    required: _l("admin.rentals.video_required"),
                    url: _l("admin.rentals.video_url_invalid"),
                },
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("text-danger");
                element.closest(".mb-3").append(error);
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
            },
        });

        let docSelectedFiles = new Map();
        const docAllowedExtensions = ["pdf", "doc", "docx", "txt"];

        $("#car_document").on("change", function (event) {
            let files = event.target.files;
            let maxFileSize = 50 * 1024 * 1024;
            let fileListContainer = $("#car_doc_append");

            for (let i = 0; i < files.length; i++) {
                let file = files[i];
                let fileExtension = file.name.split(".").pop().toLowerCase();

                if (!docAllowedExtensions.includes(fileExtension)) {
                    showToast(
                        "error",
                        `Only PDF, DOC, and TXT files are allowed.`
                    );
                    continue;
                }

                if (file.size > maxFileSize) {
                    showToast("error", `File exceeds the 50MB limit.`);
                    continue;
                }

                if (docSelectedFiles.has(file.name)) {
                    showToast("error", `File is already added.`);
                    continue;
                }

                docSelectedFiles.set(file.name, file);

                let fileSizeInKB = (file.size / 1024).toFixed(2);
                let fileSizeInMB = (file.size / (1024 * 1024)).toFixed(2);
                let fileSizeText =
                    file.size < 1024 * 1024
                        ? `${fileSizeInKB} KB`
                        : `${fileSizeInMB} MB`;
                let fileTypeIcon = docGetFileTypeIcon(file.name);
                let progressPercent = ((file.size / maxFileSize) * 100).toFixed(
                    2
                );

                let truncatedFileName =
                    file.name.length > 10
                        ? file.name.substring(0, 10) + "..."
                        : file.name;
                let fileItem = $(`
                <div class="d-flex align-items-center justify-content-between bg-white border br-5 gap-3 flex-wrap p-20 mb-2 file-item" data-file="${file.name}">
                    <div class="d-flex align-items-center">
                        <span><img src="${fileTypeIcon}" alt="File Icon"></span>
                        <div class="ms-2">
                            <h6 class="fs-14 fw-medium" title="${file.name}">${truncatedFileName}</h6>
                            <p class="fs-13">${fileSizeInKB} KB</p>
                        </div>
                    </div>
                    <div class="progress-wrap">
                        <div class="progress progress-sm" role="progressbar">
                            <div class="progress-bar bg-success" style="width: ${progressPercent}%"></div>
                        </div>
                        <p class="fs-12 text-muted mt-1">${fileSizeText} of 50MB</p>
                    </div>
                    <div class="icon-list">
                        <a href="javascript:void(0);" class="trash-icon doc-delete-file"><i class="ti ti-trash"></i></a>
                    </div>
                </div>
            `);

                fileListContainer.append(fileItem);
            }

            docUpdateFileInput();
        });

        function docUpdateFileInput() {
            let dataTransfer = new DataTransfer();

            docSelectedFiles.forEach((file) => {
                dataTransfer.items.add(file);
            });

            $("#car_document")[0].files = dataTransfer.files;
        }

        function docGetFileTypeIcon(fileName) {
            let fileExtension = fileName.split(".").pop().toLowerCase();
            let iconPath = ""; // 🛠️ Declare it here first
            if (fileExtension === "doc" || fileExtension === "docx") {
                iconPath = "/backend/assets/img/icons/pdf-icon.svg";
            } else if (fileExtension === "txt") {
                iconPath = "/backend/assets/img/icons/txt.svg";
            } else if (fileExtension === "pdf") {
                iconPath = "/backend/assets/img/icons/pdf-icon.svg";
            }

            return iconPath;
        }

        $(document).on("click", ".doc-delete-file", function () {
            let fileItem = $(this).closest(".file-item");
            let fileName = fileItem.data("file");

            docSelectedFiles.delete(fileName);
            fileItem.remove();

            docUpdateFileInput();
        });

        let policySelectedFiles = new Map();
        const policyAllowedExtensions = ["pdf", "doc", "docx", "txt"];

        $(document).on("change", "#policy_document", function (event) {
            let files = event.target.files;
            let maxFileSize = 50 * 1024 * 1024;
            let fileListContainer = $("#car_policy_append");

            for (let i = 0; i < files.length; i++) {
                let file = files[i];
                let fileExtension = file.name.split(".").pop().toLowerCase();

                if (!policyAllowedExtensions.includes(fileExtension)) {
                    showToast(
                        "error",
                        `Only PDF, DOC, and TXT files are allowed.`
                    );
                    continue;
                }

                if (file.size > maxFileSize) {
                    showToast("error", `File exceeds the 50MB limit.`);
                    continue;
                }

                if (policySelectedFiles.has(file.name)) {
                    showToast("error", `File is already added.`);
                    continue;
                }

                policySelectedFiles.set(file.name, file);

                let fileSizeInKB = (file.size / 1024).toFixed(2);
                let fileSizeInMB = (file.size / (1024 * 1024)).toFixed(2);
                let fileSizeText =
                    file.size < 1024 * 1024
                        ? `${fileSizeInKB} KB`
                        : `${fileSizeInMB} MB`;
                let fileTypeIcon = policyGetFileTypeIcon(file.name);
                let progressPercent = ((file.size / maxFileSize) * 100).toFixed(
                    2
                );

                let truncatedFileName =
                    file.name.length > 10
                        ? file.name.substring(0, 10) + "..."
                        : file.name;
                let fileItem = $(`
                <div class="d-flex align-items-center justify-content-between bg-white border br-5 gap-3 flex-wrap p-20 mb-2 file-item" data-file="${file.name}">
                    <div class="d-flex align-items-center">
                        <span><img src="${fileTypeIcon}" alt="File Icon"></span>
                        <div class="ms-2">
                            <h6 class="fs-14 fw-medium" title="${file.name}">${truncatedFileName}</h6>
                            <p class="fs-13">${fileSizeInKB} KB</p>
                        </div>
                    </div>
                    <div class="progress-wrap">
                        <div class="progress progress-sm" role="progressbar">
                            <div class="progress-bar bg-success" style="width: ${progressPercent}%"></div>
                        </div>
                        <p class="fs-12 text-muted mt-1">${fileSizeText} of 50MB</p>
                    </div>
                    <div class="icon-list">
                        <a href="javascript:void(0);" class="trash-icon policy-delete-file"><i class="ti ti-trash"></i></a>
                    </div>
                </div>
            `);

                fileListContainer.append(fileItem);
            }

            policyUpdateFileInput();
        });

        function policyUpdateFileInput() {
            let dataTransfer = new DataTransfer();

            policySelectedFiles.forEach((file) => {
                dataTransfer.items.add(file);
            });

            $("#policy_document")[0].files = dataTransfer.files;
        }

        function policyGetFileTypeIcon(fileName) {
            let fileExtension = fileName.split(".").pop().toLowerCase();
            let iconPath = ""; // 🛠️ Declare it here first
            if (fileExtension === "doc" || fileExtension === "docx") {
                iconPath = "/backend/assets/img/icons/pdf-icon.svg"; // 📝 maybe a Word icon instead?
            } else if (fileExtension === "pdf") {
                iconPath = "/backend/assets/img/icons/pdf-icon.svg";
            } else if (fileExtension === "txt") {
                iconPath = "/backend/assets/img/icons/txt.svg";
            } else {
                iconPath = "/backend/assets/img/icons/default-file-icon.svg"; // ⚙️ default for unknown files
            }

            return iconPath;
        }

        $(document).on("click", ".policy-delete-file", function () {
            let fileItem = $(this).closest(".file-item");
            let fileName = fileItem.data("file");

            policySelectedFiles.delete(fileName);
            fileItem.remove();

            policyUpdateFileInput();
        });

        let selectedImages = new Map();
        const allowedImageExtensions = ["jpg", "jpeg", "png"];

        $("#car_images").on("change", function (event) {
            let files = event.target.files;
            let maxFileSize = 50 * 1024 * 1024;
            let imageListContainer = $("#car_images_append");
            let validFiles = [];
            let pending = files.length;

            for (let i = 0; i < files.length; i++) {
                let file = files[i];
                let ext = file.name.split(".").pop().toLowerCase();

                if (!allowedImageExtensions.includes(ext)) {
                    showToast(
                        "error",
                        `Only image files (${allowedImageExtensions.join(
                            ", "
                        )}) are allowed.`
                    );
                    pending--;
                    continue;
                }

                if (file.size > maxFileSize) {
                    showToast(
                        "error",
                        `File "${file.name}" exceeds the 50MB size limit.`
                    );
                    pending--;
                    continue;
                }

                if (selectedImages.has(file.name)) {
                    showToast(
                        "error",
                        `Image "${file.name}" is already selected.`
                    );
                    pending--;
                    continue;
                }

                let imageUrl = URL.createObjectURL(file);
                let img = new Image();
                img.src = imageUrl;

                img.onload = function () {
                    if (this.width === 690 && this.height === 420) {
                        selectedImages.set(file.name, file);
                        validFiles.push(file);

                        imageListContainer.append(`
                            <div class="uploaded-img" data-file="${file.name}">
                                <img src="${imageUrl}" alt="img">
                                <a href="javascript:void(0);" class="trash-icon fs-12 delete-image"><i class="ti ti-trash"></i></a>
                            </div>
                        `);
                    } else {
                        showToast(
                            "error",
                            `Image "${file.name}" must be 690x420 pixels.`
                        );
                        URL.revokeObjectURL(imageUrl);
                    }

                    pending--;
                    if (pending === 0) updateImageInput(validFiles);
                };

                img.onerror = function () {
                    showToast("error", `Failed to load "${file.name}".`);
                    URL.revokeObjectURL(imageUrl);
                    pending--;
                    if (pending === 0) updateImageInput(validFiles);
                };
            }
        });

        function updateImageInput(validFiles) {
            let dt = new DataTransfer();
            validFiles.forEach((file) => dt.items.add(file));
            $("#car_images")[0].files = dt.files;
        }

        $(document).on("click", ".delete-image", function () {
            let item = $(this).closest(".uploaded-img");
            let fileName = item.data("file");

            selectedImages.delete(fileName);
            item.remove();

            let updatedFiles = Array.from(selectedImages.values());
            updateImageInput(updatedFiles);
        });

        $("#car_video").on("input", function () {
            let videoUrl = $(this).val().trim();
            let videoContainer = $("#car_video_append");

            let youtubeRegex =
                /^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/;

            if (videoUrl === "") {
                videoContainer.html("");
                return;
            }

            if (youtubeRegex.test(videoUrl)) {
                let videoItem = `
                    <img src="/backend/assets/img/car/car-lg-01.jpg" alt="img">
                    <a href="${videoUrl}" target="_blank" data-fancybox="" class="play-icon">
                        <i class="ti ti-player-play-filled"></i>
                    </a>
            `;

                videoContainer.html(videoItem);
            } else {
                videoContainer.html("");
            }
        });

        $("#carDamageNext").on("click", function (event) {
            event.preventDefault();

            let carBasicInfoFormData = $("#carDocumentForm").serializeArray();

            if ($("#carDocumentForm").valid()) {
                let formDataCollection = {};
                carBasicInfoFormData.forEach(function (item) {
                    formDataCollection[item.name] = item.value;
                });

                $("#fifth-field").hide();
                $("#sixth-field").show();
                $("#fifthBar").removeClass("active").addClass("activated");
                $("#sixthBar").addClass("active");
            } else {
            }
        });

        let editingDamageId = null;
        let deletingDamageId = null;

        $("#dam_image").on("change", function (event) {
            let file = event.target.files[0];

            if (file) {
                let imageUrl = URL.createObjectURL(file);
                $("#image_preview").attr("src", imageUrl).removeClass("d-none");
            } else {
                $("#image_preview").attr("src", "").addClass("d-none");
            }
        });

        $("#damage_btn").on("click", function () {
            let damageImage = $("#dam_image")[0].files[0];
            let damageName = $("#dam_name").val();
            let damageType = $("#dam_type").find("option:selected").text();
            let damageDesc = $("#dam_dis").val();
            let currentDate = new Date().toLocaleDateString("en-US", {
                day: "2-digit",
                month: "short",
                year: "numeric",
            });

            if (!damageName) {
                showToast("error", "Please enter a damage name.");
                return;
            }

            if (!damageType || damageType === "Select Type") {
                showToast("error", "Please select a damage type.");
                return;
            }

            if (editingDamageId) {
                let editElement = $("#" + editingDamageId);
                let prevImageSrc = editElement
                    .find("input[name='damage_image[]']")
                    .val();

                if (damageImage) {
                    let reader = new FileReader();
                    reader.onload = function (e) {
                        editElement
                            .find(".damage-image")
                            .attr("src", e.target.result);
                        editElement
                            .find("input[name='damage_image[]']")
                            .val(e.target.result);
                    };
                    reader.readAsDataURL(damageImage);
                } else {
                    editElement
                        .find("input[name='damage_image[]']")
                        .val(prevImageSrc);
                }

                // Update values in hidden inputs
                editElement.find("input[name='damage_name[]']").val(damageType);
                editElement
                    .find("input[name='damage_location[]']")
                    .val(damageName);
                editElement
                    .find("input[name='damage_description[]']")
                    .val(damageDesc);

                // Update displayed text
                editElement.find("h6").text(damageType);
                editElement.find(".badge").text(damageName);
                editElement.find("p.fs-13").text(damageDesc);

                // Reset form fields
                $("#damage_title").text("Add New Damage");
                $("#damage_btn").text("Create New");
                editingDamageId = null;
                showToast("success", "Damage updated successfully!");
            } else {
                let uniqueId = `damage_${crypto.randomUUID()}`;
                let reader = new FileReader();

                reader.onload = function (e) {
                    let imageUrl = e.target.result;

                    let newDamage = `
                <div id="${uniqueId}" class="bg-white p-20 br-5 border mb-2">
                 <input type="hidden" name="damage_id[]" value="${uniqueId}">
                        <input type="hidden" name="damage_image[]" value="${imageUrl}">
                    <div class="row align-items-center row-gap-3">
                        <div class="col-xxl-8 col-md-7">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h6 class="fs-14 fw-medium">${damageType}</h6>
                                <input type="hidden" name="damage_name[]" value="${damageType}">
                                <span class="badge bg-pink-transparent badge-sm">${damageName}</span>
                                <input type="hidden" name="damage_location[]" value="${damageName}">
                            </div>
                            <p class="fs-13">${damageDesc}</p>
                            <input type="hidden" name="damage_description[]" value="${damageDesc}">
                        </div>
                        <div class="col-xxl-4 col-md-5">
                            <div class="d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                                <p class="mb-0">Added on : ${currentDate}</p>
                                <div class="icon-list d-flex align-items-center">
                                    <a href="#" class="edit-damage me-2" data-id="${uniqueId}" data-bs-toggle="modal" data-bs-target="#add-damage">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <a href="#" class="trash-damage" data-id="${uniqueId}" data-bs-toggle="modal" data-bs-target="#delete_damage">
                                        <i class="ti ti-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>                                                            
                </div>`;

                    $("#car_damage_append").append(newDamage);
                    updateDamageCount();
                };

                reader.readAsDataURL(damageImage);
            }

            $("#add-damage").modal("hide");
            showToast("success", "Damage added successfully!");
        });

        function updateDamageCount() {
            let totalDamages = $("#car_damage_append > div").length;
            $("#damage_count").text(totalDamages.toString().padStart(2, "0"));
        }

        $(document).on("click", "#damage_car", function (e) {
            e.preventDefault();
            $("#damage_title").text("Add Damage");
            $("#damage_btn").text("Create New");

            $("#add-damage input, #add-damage textarea").val("");

            $("#add-damage select").prop("selectedIndex", 0).trigger("change");
            $("#image_preview").attr("src", "").addClass("d-none");
        });

        // Edit Damage
        $(document).on("click", ".edit-damage", function () {
            editingDamageId = $(this).data("id");
            let editElement = $("#" + editingDamageId);

            // Get values from hidden inputs
            let damageId = editElement.find("input[name='damage_id[]']").val();
            let damageName = editElement
                .find("input[name='damage_name[]']")
                .val();
            let damageLocation = editElement
                .find("input[name='damage_location[]']")
                .val();
            let damageDesc = editElement
                .find("input[name='damage_description[]']")
                .val();
            let imgSrc = editElement.find("input[name='damage_image[]']").val();

            // Populate form fields
            $("#dam_name").val(damageLocation);
            $("#dam_dis").val(damageDesc);
            $("#damage_title").text("Edit Damage");
            $("#damage_btn").text("Update");

            // Handle Image Preview
            if (imgSrc) {
                $("#image_preview").attr("src", imgSrc).removeClass("d-none");
            } else {
                $("#image_preview").attr("src", "").addClass("d-none");
            }

            // Reset file input so user can select a new image
            $("#dam_image").val("");
        });

        // Delete Damage
        $(document).on("click", ".trash-damage", function () {
            deletingDamageId = $(this).data("id");
        });

        $("#dete-damage").on("click", function () {
            if (deletingDamageId) {
                $("#" + deletingDamageId).fadeOut(300, function () {
                    $(this).remove();
                    updateDamageCount();
                });
                deletingDamageId = null;
            }
            showToast("success", "Damage deleted successfully!");
            $("#delete_damage").modal("hide");
        });

        // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        // (FAQ validation and scripts)
        // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

        let faqCounter = 0; // Counter to create unique IDs
        let editingFAQ = null; // Track the currently editing FAQ

        $("#faq_btn").on("click", function (e) {
            e.preventDefault();

            let question = $("#f_q").val().trim();
            let answer = $("#f_a").val().trim();

            if (!question || !answer) {
                showToast("error", "Please enter both Question and Answer.");
                return;
            }

            if (editingFAQ) {
                // Update existing FAQ
                let uniqueID = editingFAQ;
                $(`#${uniqueID}_question`).val(question);
                $(`#${uniqueID}_answer`).val(answer);
                $(`#${uniqueID}_text`).text(answer);
                $(`#${uniqueID} button`).html(
                    `<span><i class="ti ti-angle-down"></i></span> ${question}`
                );

                showToast("success", "FAQ updated successfully!");
                editingFAQ = null; // Reset after editing
            } else {
                // Add new FAQ
                let uniqueID = `faq_${crypto.randomUUID()}`;
                let faqItem = `
            <div class="accordion-item" id="faq_item_${uniqueID}">
                <h2 class="accordion-header d-flex align-items-center justify-content-between">
                    <button class="accordion-button collapsed" type="button"
                        data-bs-toggle="collapse" data-bs-target="#${uniqueID}"
                        aria-expanded="false" aria-controls="${uniqueID}">
                        <span class="faq-icon"><i class="ti ti ti-grip-vertical"></i></span> ${question}
                    </button>
                    <input type="hidden" name="faq_id[]" value="" id="${uniqueID}_id">
                    <input type="hidden" name="faq_question[]" value="${question}" id="${uniqueID}_question">
                </h2>
                <div class="faq-actions text-end py-2 px-3">
                    <i class="ti ti-edit edit-faq cursor-pointer" data-id="${uniqueID}"></i>
                    <i class="ti ti-trash delete-faq cursor-pointer" data-id="${uniqueID}"></i>
                </div>
                <div id="${uniqueID}" class="accordion-collapse collapse" data-bs-parent="#faqaccordion">
                    <div class="accordion-body">
                        <p class="fs-13" id="${uniqueID}_text">${answer}</p>
                        <input type="hidden" name="faq_answer[]" value="${answer}" id="${uniqueID}_answer">
                    </div>
                </div>
            </div>
            `;

                $(".car_faq_append").append(faqItem);
                showToast("success", "FAQ added successfully!");
                updateFaqCount();
            }

            // Close modal and reset form
            $("#add-faq").modal("hide");
            $("#faq_title").text("Create FAQ");
            $("#faq_btn").text("Create New");
            $("#f_q").val("");
            $("#f_a").val("");
        });

        function updateFaqCount() {
            let totalDamages = $(".car_faq_append > div").length;
            $("#faq_count").text(totalDamages.toString().padStart(2, "0"));
        }

        // Edit FAQ
        $(document).on("click", ".edit-faq", function () {
            let faqID = $(this).data("id");

            // Get existing values
            let question = $(`#${faqID}_question`).val();
            let answer = $(`#${faqID}_answer`).val();

            // Populate modal with existing values
            $("#f_q").val(question);
            $("#f_a").val(answer);
            $("#faq_title").text("Edit FAQ");
            $("#faq_btn").text("Update");

            editingFAQ = faqID; // Store the current editing ID

            $("#add-faq").modal("show");
        });

        // Delete FAQ
        $(document).on("click", ".delete-faq", function () {
            let faqID = $(this).data("id");

            // Show confirmation modal
            $("#delete_faq").modal("show");

            $("#dete-faq")
                .off("click")
                .on("click", function () {
                    $(`#faq_item_${faqID}`).remove();
                    showToast("success", "FAQ deleted successfully!");
                    $("#delete_faq").modal("hide");
                    updateFaqCount();
                });
        });

        // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        // (SEO validation and scripts)
        //
        // - > Storing all the data
        // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

        $("#carSeoForm").validate({
            rules: {
                seo_title: {
                    required: false,
                    maxlength: 255,
                },
                seo_key: {
                    required: false,
                    maxlength: 255,
                },
                seo_description: {
                    required: false,
                    maxlength: 255,
                },
            },
            messages: {
                seo_title: {
                    required: _l("admin.rentals.seo_title_required"),
                    maxlength: _l("admin.rentals.seo_title_maxlength"),
                },
                seo_key: {
                    required: _l("admin.rentals.seo_key_required"),
                    maxlength: _l("admin.rentals.seo_key_maxlength"),
                },
                seo_description: {
                    required: _l("admin.rentals.seo_description_required"),
                    maxlength: _l("admin.rentals.seo_description_maxlength"),
                },
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("invalid-feedback");
                element.closest(".mb-3").append(error);
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
            },
        });

        $("#seoFinalBtn").on("click", function (event) {
            event.preventDefault();

            let carBasicInfoData = $("#carBasicInfoForm").serializeArray();
            let featuresData = $("#featuresForm").serializeArray();
            let priceTariffData = $("#priceTariffForm").serializeArray();
            let extraServiceData = $("#extraServiceForm").serializeArray();
            let carDocumentData = $("#carDocumentForm").serializeArray();
            let carDamageData = $("#carDamageForm").serializeArray();
            let carFaqData = $("#carFaqForm").serializeArray();
            let carSeoData = $("#carSeoForm").serializeArray();

            if ($("#carSeoForm").valid()) {
                let finalFormData = new FormData();

                finalFormData.append(
                    "_token",
                    $('meta[name="csrf-token"]').attr("content")
                );

                [
                    ...carBasicInfoData,
                    ...featuresData,
                    ...priceTariffData,
                    ...extraServiceData,
                    ...carDocumentData,
                    ...carDamageData,
                    ...carFaqData,
                    ...carSeoData,
                ].forEach(function (item) {
                    finalFormData.append(item.name, item.value);
                });

                let featureIds = [];

                $("input[name='feature_id[]']:checked").each(function () {
                    featureIds.push(parseInt($(this).val()));
                });

                featureIds.sort((a, b) => a - b);

                finalFormData.append("feature_id", JSON.stringify(featureIds));

                let otherLocationIds = [];

                $("select[name='other_location_id[]'] option:selected").each(
                    function () {
                        let value = parseInt($(this).val());
                        if (!isNaN(value)) {
                            otherLocationIds.push(value);
                        }
                    }
                );

                finalFormData.append(
                    "other_location_id",
                    JSON.stringify(otherLocationIds)
                );

                let carDocFiles = $("#car_document")[0].files;
                if (carDocFiles.length > 0) {
                    for (let i = 0; i < carDocFiles.length; i++) {
                        finalFormData.append("car_document[]", carDocFiles[i]);
                    }
                }

                let carPolicayFiles = $("#policy_document")[0].files;
                if (carPolicayFiles.length > 0) {
                    for (let i = 0; i < carPolicayFiles.length; i++) {
                        finalFormData.append(
                            "policy_document[]",
                            carPolicayFiles[i]
                        );
                    }
                }

                let carImageFiles = $("#car_images")[0].files;
                if (carImageFiles.length > 0) {
                    for (let i = 0; i < carImageFiles.length; i++) {
                        finalFormData.append("car_images[]", carImageFiles[i]);
                    }
                }

                let tariffPayload = [];

                $("input[name='tariff_id[]']").each(function (index) {
                    let tariffId = $(this).val().trim(); // Get value & trim spaces
                    tariffId = tariffId === "" ? null : parseInt(tariffId); // Convert empty to null, else integer

                    let tariffTitle = $("input[name='tariff_title[]']")
                        .eq(index)
                        .val()
                        .trim();
                    let dailyPrice = parseFloat(
                        $("input[name='tariff_daily_price[]']").eq(index).val()
                    );
                    let fromDays = parseInt(
                        $("input[name='tariff_from_days[]']").eq(index).val()
                    );
                    let toDays = parseInt(
                        $("input[name='tariff_to_days[]']").eq(index).val()
                    );
                    let baseKm = parseInt(
                        $("input[name='tariff_base_km[]']").eq(index).val()
                    );
                    let extraPrice = parseFloat(
                        $("input[name='tariff_extra_price[]']").eq(index).val()
                    );

                    if (
                        tariffTitle !== "" &&
                        !isNaN(dailyPrice) &&
                        !isNaN(fromDays) &&
                        !isNaN(toDays) &&
                        !isNaN(baseKm) &&
                        !isNaN(extraPrice)
                    ) {
                        tariffPayload.push({
                            id: tariffId,
                            title: tariffTitle,
                            daily_price: dailyPrice,
                            from_days: fromDays,
                            to_days: toDays,
                            base_km: baseKm,
                            extra_price: extraPrice,
                        });
                    }
                });

                finalFormData.append("tariff", JSON.stringify(tariffPayload));

                let seasonalPayload = [];

                $("input[name='seasonal_id[]']").each(function (index) {
                    let seasonalId = $(this).val().trim();
                    seasonalId =
                        seasonalId === "" ? null : parseInt(seasonalId);

                    let seasonalTitle = $("input[name='seasonal_title[]']")
                        .eq(index)
                        .val()
                        .trim();
                    let startDate = $("input[name='seasonal_start_date[]']")
                        .eq(index)
                        .val();
                    let endDate = $("input[name='seasonal_end_date[]']")
                        .eq(index)
                        .val();
                    let dailyRate = parseFloat(
                        $("input[name='seasonal_daily_rate[]']").eq(index).val()
                    );
                    let weeklyRate = parseFloat(
                        $("input[name='seasonal_weekly_rate[]']")
                            .eq(index)
                            .val()
                    );
                    let monthlyRate = parseFloat(
                        $("input[name='seasonal_monthly_rate[]']")
                            .eq(index)
                            .val()
                    );
                    let lateFee = parseFloat(
                        $("input[name='seasonal_late_fee[]']").eq(index).val()
                    );

                    if (
                        seasonalTitle !== "" &&
                        startDate !== "" &&
                        endDate !== "" &&
                        !isNaN(dailyRate) &&
                        !isNaN(weeklyRate) &&
                        !isNaN(monthlyRate) &&
                        !isNaN(lateFee)
                    ) {
                        seasonalPayload.push({
                            id: seasonalId,
                            title: seasonalTitle,
                            start_date: startDate,
                            end_date: endDate,
                            daily_rate: dailyRate,
                            weekly_rate: weeklyRate,
                            monthly_rate: monthlyRate,
                            late_fee: lateFee,
                        });
                    }
                });

                finalFormData.append(
                    "seasonal",
                    JSON.stringify(seasonalPayload)
                );

                let extraServicePayload = [];

                $("input[name='extra_service[]']:checked").each(function (
                    index
                ) {
                    let serviceId = $(this).val(); // Get service ID
                    let serviceValue = $("input[name='service_value[]']")
                        .eq(index)
                        .val();
                    let servicePrice = $("input[name='service_price[]']")
                        .eq(index)
                        .val();

                    extraServicePayload.push({
                        service_id: parseInt(serviceId),
                        value: serviceValue,
                        price: parseFloat(servicePrice),
                    });
                });

                finalFormData.append(
                    "extra_services",
                    JSON.stringify(extraServicePayload)
                );

                let faqPayload = [];

                $("input[name='faq_question[]']").each(function (index) {
                    let question = $(this).val().trim();
                    let answer = $("input[name='faq_answer[]']")
                        .eq(index)
                        .val()
                        .trim();
                    let faqId = $("input[name='faq_id[]']")
                        .eq(index)
                        .val()
                        .trim(); // Get FAQ ID

                    if (question !== "" && answer !== "") {
                        faqPayload.push({
                            id: faqId === "" ? null : parseInt(faqId), // Set ID to null if empty
                            question: question,
                            answer: answer,
                        });
                    }
                });

                finalFormData.append("vehicle_faq", JSON.stringify(faqPayload));

                let damagePayload = [];

                $("input[name='damage_image[]']").each(function (index) {
                    let image = $(this).val().trim();
                    let name = $("input[name='damage_name[]']")
                        .eq(index)
                        .val()
                        .trim();
                    let location = $("input[name='damage_location[]']")
                        .eq(index)
                        .val()
                        .trim();
                    let description = $("input[name='damage_description[]']")
                        .eq(index)
                        .val()
                        .trim();
                    let damageId = $("input[name='damage_id[]']")
                        .eq(index)
                        .val()
                        .trim(); // Get Damage ID

                    if (
                        image !== "" &&
                        name !== "" &&
                        location !== "" &&
                        description !== ""
                    ) {
                        damagePayload.push({
                            image: image,
                            name: name,
                            location: location,
                            description: description,
                        });
                    }
                });

                finalFormData.append(
                    "vehicle_damage",
                    JSON.stringify(damagePayload)
                );

                let vehicleImage = $("#vehicle_image")[0].files[0];

                if (vehicleImage) {
                    finalFormData.append("vehicle_image", vehicleImage);
                }

                let insurancePayload = [];

                $("input[name='insurance_id_one[]']").each(function (index) {
                    let id = parseInt($(this).val().trim()); // Convert to integer
                    let price = parseFloat(
                        $("input[name='insurance_price_one[]']")
                            .eq(index)
                            .val()
                            .trim()
                    ); // Convert to float
                    let type = $("input[name='insurance_price_type_one[]']")
                        .eq(index)
                        .val()
                        .trim(); // Get type

                    if (!isNaN(id) && !isNaN(price) && type !== "") {
                        insurancePayload.push({
                            id: id,
                            price: price,
                            type: type,
                        });
                    }
                });

                // Append the JSON string to FormData
                finalFormData.append(
                    "vehicle_insurance",
                    JSON.stringify(insurancePayload)
                );

                $("#seoFinalBtn").text("Please Wait...").prop("disabled", true);

                $.ajax({
                    url: "/admin/create/vehicle",
                    method: "POST",
                    data: finalFormData,
                    dataType: "json",
                    contentType: false,
                    processData: false,
                    cache: false,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                })
                    .done((response, statusText, xhr) => {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid");
                        $(".add_btn").removeAttr("disabled");
                        $(".add_btn").html("Submit");

                        if (response.code === 200) {
                            showToast("success", response.message);

                            $(".form-control").removeClass("is-valid");
                            $(".is-invalid").removeClass("is-invalid");
                            $(".invalid-feedback").remove();

                            setTimeout(() => {
                                window.location.href =
                                    window.location.origin +
                                    "/admin/vehiclelist";
                            });
                        }
                    })
                    .fail((error) => {
                        $("#serviceLoader").hide();
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid");
                        $(".add_btn").removeAttr("disabled");
                        $(".add_btn").html("submit");
                        $("#seoFinalBtn")
                            .text("Save & Exit")
                            .prop("disabled", false);

                        if (error.status == 422) {
                            $.each(error.responseJSON, function (key, val) {
                                $("#" + key).addClass("is-invalid");
                                $("#" + key + "_error").text(val[0]);
                            });
                            $("#seoFinalBtn")
                                .text("Save & Exit")
                                .prop("disabled", false);
                        } else {
                            toastr(error.responseJSON.message, "bg-danger");
                            $("#seoFinalBtn")
                                .text("Save & Exit")
                                .prop("disabled", false);
                            F;
                        }
                    });
            }
        });

        $(document).on(
            "click",
            "[data-bs-target='#status-modal']",
            function () {
                const vehicleId = $(this).data("id");
                const status = $(this).data("status");

                $("#status_vehicle_id").val(vehicleId);
                $("#vehicle_status").val(status).trigger("change"); // Important for Select2
            }
        );

        $("#vehicle_brand_id").on("change", function () {
            let brandId = $(this).val();
            let modelDropdown = $("#vehicle_model_id");

            modelDropdown.html('<option value="">Loading...</option>'); // Show loading text

            if (brandId) {
                $.ajax({
                    url: "/admin/get-model",
                    type: "GET",
                    data: { brand_id: brandId },
                    success: function (response) {
                        modelDropdown.html(
                            '<option value="">Select Model</option>'
                        ); // Reset dropdown

                        if (response.length > 0) {
                            $.each(response, function (key, model) {
                                modelDropdown.append(
                                    `<option value="${model.id}">${model.model_name}</option>`
                                );
                            });
                        } else {
                            modelDropdown.html(
                                '<option value="">No models found</option>'
                            );
                        }
                    },
                    error: function () {
                        modelDropdown.html(
                            '<option value="">Error loading models</option>'
                        );
                    },
                });
            } else {
                modelDropdown.html('<option value="">Select Model</option>'); // Reset if no brand is selected
            }
        });

        $(document).on("change", "#Baseunlimited", function () {
            if ($(this).is(":checked")) {
                $("#basic_kilometer").prop("disabled", true).val("");
                $("#extra_kilometer").prop("disabled", true).val("");
            } else {
                $("#basic_kilometer").prop("disabled", false);
                $("#extra_kilometer").prop("disabled", false);
            }
        });

        $("#delImg").on("click", function () {
            $("#vehicle_image").val("");

            $(".frames img").attr("src", "").hide(); // Hides the image after removal
        });

        $(document).on("click", ".change-language", function () {
            var languageCode = $(this).data("language_code");

            $.ajax({
                url: "/admin/flag-change-language",
                type: "POST",
                data: { language_code: languageCode },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    if (response.status === "success") {
                        location.reload();
                    }
                },
            });
        });

        const saveBtn = document.getElementById("service_save_btn");
        if (saveBtn) {
            saveBtn.addEventListener("click", function () {
                let tableRows = document.querySelectorAll(
                    ".custom-table1 tbody tr"
                );

                tableRows.forEach((row) => {
                    let serviceName = row
                        .querySelector("#extra_name")
                        .innerText.trim();
                    let extraValue = row.querySelector("#extra_value").value;
                    let extraPrice = row.querySelector("#extra_price").value;

                    let serviceCards = document.querySelectorAll(
                        ".extra-service-card"
                    );

                    serviceCards.forEach((card) => {
                        let cardName = card
                            .querySelector("#service_name")
                            .innerText.trim();

                        if (cardName === serviceName) {
                            card.querySelector("#set_value").innerText =
                                extraValue === "per_day"
                                    ? _l("admin.rentals.per_day")
                                    : _l("admin.rentals.one_time");
                            card.querySelector("#service_value").value =
                                extraValue;

                            card.querySelector(
                                "#set_price"
                            ).innerText = `${currency}${extraPrice}`;
                            card.querySelector("#service_price").value =
                                extraPrice;
                        }
                    });
                });

                $("#edit_price").modal("hide");
            });
        }

        $(".delivery-add").each(function () {
            const $container = $(this);
            const $plusIcon = $container.find(".plus-active");
            const $checkIcon = $container.find(".check-active");
            const $checkbox = $container.find("#insurance_checked");

            $container.on("click", function (event) {
                event.preventDefault();
                if ($checkbox.prop("checked")) {
                    $checkbox.prop("checked", false);
                    $checkIcon.addClass("d-none");
                    $plusIcon.removeClass("d-none");
                } else {
                    $checkbox.prop("checked", true);
                    $checkIcon.removeClass("d-none");
                    $plusIcon.addClass("d-none");
                }
            });
        });

        // Edit icon click handler
        $(document).on("click", ".edit-icon-in", function () {
            const $editButton = $(this);
            const uniqueId = $editButton.data("id");
            const price = $editButton.data("price");
            const priceType = $editButton.data("price-type").toLowerCase(); // Ensure lowercase match

            $("#price").val(price);
            $("#edit_insurance").attr("data-id", uniqueId);

            // Match the radio by value instead of label text
            $("input[name='Radio']").each(function () {
                if ($(this).val() === priceType) {
                    $(this).prop("checked", true);
                }
            });
        });

        // Add insurance button click
        const $inBtn = $("#in_btn");
        if ($inBtn.length) {
            $inBtn.on("click", function () {
                $(".noDataI").html(""); // Clear previous entries
                const $selectedInsurances = $(
                    "#set_value .delivery-add input[type='checkbox']:checked"
                );
                const $appendContainer = $("#insurance_car_append");

                $appendContainer.html("");

                $selectedInsurances.each(function () {
                    const $checkbox = $(this);
                    const $container = $checkbox.closest("#inCont");
                    const insuranceId = $container.find("#insurance_id").val();
                    const insuranceName = $container
                        .find("#insurance_name")
                        .val();
                    const insurancePrice = $container
                        .find("#insurance_price")
                        .val();
                    const insuranceCount = $container
                        .find("#insurance_count")
                        .val();
                    const insurancePriceType = $container
                        .find("#insurance_price_type")
                        .val();
                    const insurancePriceTypeId = $container
                        .find("#insurance_price_type_id")
                        .val();

                    const uniqueId = `insurance_${crypto.randomUUID()}`;
                    const displayPrice =
                        insurancePriceTypeId == 7
                            ? `${parseFloat(insurancePrice).toFixed(0)}%`
                            : `${currency}${parseFloat(insurancePrice).toFixed(2)}`;
                    const newInsuranceDiv = $(`
                    <div class="d-flex align-items-center justify-content-between flex-wrap bg-white gap-3 border br-5 p-20 mb-3" data-id="${uniqueId}">
                        <div>
                            <h6 class="fs-14 fw-semibold d-inline-flex align-items-center mb-1">${insuranceName}</h6>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <p class="fs-13 fw-medium border-end pe-2 mb-0">${_l(
                                    "admin.rentals.insurance_price"
                                )} : <span class="text-gray-9 priceIn" data-id="${uniqueId}">${displayPrice}</span></p>
                                <input type="hidden" name="insurance_id_one[]" id="insurance_id_one_${uniqueId}" value="${insuranceId}">
                                <input type="hidden" name="insurance_price_one[]" id="insurance_price_one_${uniqueId}" value="${insurancePrice}">
                                <p class="fs-13 fw-medium mb-0">${_l(
                                    "admin.rentals.insurance_benefits"
                                )} : <span class="text-gray-9">${insuranceCount}</span></p>
                                <p class="fs-13 fw-medium mb-0">${_l(
                                    "admin.rentals.insurance_price_type"
                                )} : <span class="text-gray-9 priceTypeIn" data-id="${uniqueId}">${insurancePriceType}</span></p>
                                <input type="hidden" name="insurance_price_type_one[]" id="insurance_price_type_one_${uniqueId}" value="${
                        insurancePriceTypeId == 7
                            ? "percentage"
                            : insurancePriceTypeId == 6
                            ? "fixed"
                            : "daily"
                    }">
                            </div>
                        </div>
                        <div class="d-flex align-items-center icon-list">
                            <a href="#" class="edit-icon-in me-2" data-bs-toggle="modal" data-bs-target="#edit_insurance" 
                                data-id="${uniqueId}" 
                                data-price="${insurancePrice}" 
                                data-price-type="${
                                    insurancePriceTypeId == 7
                                        ? "percentage"
                                        : insurancePriceTypeId == 6
                                        ? "fixed"
                                        : "daily"
                                }">
                                <i class="ti ti-edit"></i>
                            </a>
                            <a href="#" class="trash-icon" data-bs-toggle="modal" data-bs-target="#delete_insurance"><i class="ti ti-trash"></i></a>
                        </div>
                    </div>
                `);

                    $appendContainer.append(newInsuranceDiv);
                });

                $("#select_insurance").modal("hide");
            });
        }

        const $saveUpdateBtn = $("#save_update");

        if ($saveUpdateBtn.length) {
            $saveUpdateBtn.on("click", function () {
                const updatedPriceRaw = $("#price").val().trim();
                const $selectedRadio = $("input[name='Radio']:checked");
                const updatedPriceType = $selectedRadio.val();
                const uniqueId = $("#edit_insurance").data("id");

                if (updatedPriceRaw === "" || isNaN(updatedPriceRaw)) {
                    alert("Please enter a valid price.");
                    return;
                }

                let updatedPrice = parseFloat(updatedPriceRaw);

                let displayPrice = "";
                let priceTypeLabel =
                    updatedPriceType.charAt(0).toUpperCase() +
                    updatedPriceType.slice(1);

                if (updatedPriceType === "percentage") {
                    updatedPrice = updatedPrice.toFixed(0);
                    displayPrice = `${updatedPrice}%`;
                } else {
                    updatedPrice = updatedPrice.toFixed(2);
                    displayPrice = `${currency}${updatedPrice}`;
                }

                $(`.priceIn[data-id='${uniqueId}']`).text(displayPrice);
                $(`#insurance_price_one_${uniqueId}`).val(updatedPrice);
                $(`.priceTypeIn[data-id='${uniqueId}']`).text(priceTypeLabel);
                $(`#insurance_price_type_one_${uniqueId}`).val(
                    updatedPriceType
                );

                $("#edit_insurance").modal("hide");
            });
        }

        $(document).on("click", ".trash-icon", function (event) {
            event.preventDefault();
            const $deleteButton = $(this);
            const $container = $deleteButton.closest("div[data-id]");
            const uniqueId = $container.data("id");

            $container.remove();

            $("#set_value .delivery-add input[type='checkbox']").each(
                function () {
                    const $checkbox = $(this);
                    const $parentContainer = $checkbox.closest("#inCont");
                    const insuranceId = $parentContainer
                        .find("#insurance_id")
                        .val();

                    if (
                        $(`#insurance_id_one_${uniqueId}`).val() === insuranceId
                    ) {
                        $checkbox.prop("checked", false);
                        $parentContainer.find(".check-active").hide();
                        $parentContainer.find(".plus-active").show();
                    }
                }
            );
        });
    });

    let editingDamageID = null;
    function editDamage(damageID) {
        let item = $("#" + damageID);

        let damageType = item.find("input[name='damage_name[]']").val();
        let damageLocation = item.find("input[name='damage_location[]']").val();
        let damageDescription = item
            .find("input[name='damage_description[]']")
            .val();
        let damageImage = item.find("input[name='damage_image[]']").val();

        $("#dam_type").val(damageType);
        $("#dam_name").val(damageLocation);
        $("#dam_dis").val(damageDescription);

        if (damageImage) {
            $("#image_preview").attr("src", damageImage).removeClass("d-none");
        } else {
            $("#image_preview").addClass("d-none");
        }

        editingDamageID = damageID;
    }
})();
