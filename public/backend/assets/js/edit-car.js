(async () => {
    "use strict";
    await loadTranslationFile("admin", "rentals, common");
    $(document).ready(function () {
        getSeasonalInfo();
        getTrraifInfo();
        getDocumentsInfo();
        getFaqInfo();
        getDamageInfo();
        getInsuranceInfo();

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
    });

    function getDamageInfo() {
        let vehicleId = $("#vehicle_id").val();

        $.ajax({
            url: "/admin/get-damage-info",
            type: "GET",
            data: { vehicle_id: vehicleId },
            success: function (response) {
                if (response.success && response.data.length > 0) {
                    $("#car_damage_append").html("");
                    response.data.forEach((damage) => {
                        adddamage(damage);
                    });
                } else {
                    // showToast("error", "No damage data found.");
                }
            },
            error: function (xhr, status, error) {},
        });
    }

    let DamageCounter = 0; // Ensure global unique IDs

    function adddamage(damage) {
        let uniqueID = `damage_${crypto.randomUUID()}`;

        let currentDate = new Date(
            damage.created_at || Date.now()
        ).toLocaleDateString("en-US", {
            day: "2-digit",
            month: "short",
            year: "numeric",
        });

        let imageUrl = damage.image;
        let newDamage = `
            <div id="${uniqueID}" class="bg-white p-20 br-5 border mb-2">
                <input type="hidden" name="damage_id[]" value="${uniqueID}">
                <input type="hidden" name="damage_image[]" value="${imageUrl}">
                <div class="row align-items-center row-gap-3">
                    <div class="col-xxl-8 col-md-7">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h6 class="fs-14 fw-medium">${damage.damage_type}</h6>
                            <input type="hidden" name="damage_loaction[]" value="${damage.damage_type}">
                            <span class="badge bg-pink-transparent badge-sm">${damage.damage_loaction}</span>
                            <input type="hidden" name="damage_location[]" value="${damage.damage_loaction}">
                        </div>
                        <p class="fs-13">${damage.description}</p>
                        <input type="hidden" name="damage_description[]" value="${damage.description}">
                    </div>
                    <div class="col-xxl-4 col-md-5">
                        <div class="d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                            <p class="mb-0">Added on : ${currentDate}</p>
                            <div class="icon-list d-flex align-items-center">
                                <a href="#" class="edit-damage me-2" data-id="${damage.id}" data-bs-toggle="modal" data-bs-target="#add-damage">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <a href="#" class="trash-damage" data-id="${uniqueID}" data-bs-toggle="modal" data-bs-target="#delete_damage">
                                    <i class="ti ti-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>                                                            
            </div>
        `;

        $("#car_damage_append").append(newDamage);
        updateDamageCount();
    }

    function updateDamageCount() {
        let totalDamages = $("#car_damage_append > div").length;
        $("#damage_count").text(totalDamages.toString().padStart(2, "0"));
    }

    function getInsuranceInfo() {
        let vehicleId = $("#vehicle_id").val();

        $.ajax({
            url: "/admin/get-insurance-info",
            type: "GET",
            data: { vehicle_id: vehicleId },
            success: function (response) {
                if (response.success) {
                    $("#insurance_car_append").html(""); // Clear previous entries
                    response.data.forEach((insurances) => {
                        addinsurances(insurances);
                    });
                } else {
                    showToast("error", "No insurance data found.");
                }
            },
            error: function (xhr, status, error) {},
        });
    }

    function addinsurances(insurances) {
        const appendContainer = document.getElementById("insurance_car_append");

        const uniqueId = `insurance_${Date.now()}_${Math.floor(
            Math.random() * 1000
        )}`;

        // Create a new div element
        const newInsuranceDiv = document.createElement("div");
        newInsuranceDiv.setAttribute(
            "class",
            "d-flex align-items-center justify-content-between bg-white border br-5 gap-3 flex-wrap p-20 mb-2"
        );
        newInsuranceDiv.setAttribute("data-id", uniqueId);

        newInsuranceDiv.innerHTML = `
        <div>
            <h6 class="fs-14 fw-semibold d-inline-flex align-items-center mb-1">${
                insurances.insurance_name
            }</h6>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <p class="fs-13 fw-medium border-end pe-2 mb-0">${_l(
                    "admin.rentals.insurance_price"
                )} : 
                    <span class="text-gray-9 priceIn" data-id="${uniqueId}">$${
            insurances.price
        }</span>
                </p>
                <input type="hidden" name="insurance_id_one[]" id="insurance_id_one_${uniqueId}" value="${
            insurances.insurances_id
        }">
                <input type="hidden" name="insurance_price_one[]" id="insurance_price_one_${uniqueId}" value="${
            insurances.price
        }">
                <p class="fs-13 fw-medium mb-0">${_l(
                    "admin.rentals.insurance_benefits"
                )} : <span class="text-gray-9">${insurances.benefits}</span></p>
                <p class="fs-13 fw-medium mb-0">${_l(
                    "admin.rentals.insurance_price_type"
                )} : 
                    <span class="text-gray-9 priceTypeIn" data-id="${uniqueId}">${
            insurances.value
        }</span>
                </p>
                <input type="hidden" name="insurance_price_type_one[]" id="insurance_price_type_one_${uniqueId}" value="${
            insurances.value
        }">
            </div>
        </div>
        <div class="d-flex align-items-center icon-list">
            <a href="#" class="edit-icon me-2" data-bs-toggle="modal" data-bs-target="#edit_insurance" 
                data-id="${uniqueId}" data-price="${
            insurances.price
        }" data-price-type="${insurances.value}">
                <i class="ti ti-edit"></i>
            </a>
            <a href="#" class="trash-icon" data-bs-toggle="modal" data-bs-target="#delete_insurance">
                <i class="ti ti-trash"></i>
            </a>
        </div>
    `;

        appendContainer.appendChild(newInsuranceDiv);
    }

    function getSeasonalInfo() {
        let vehicleId = $("#vehicle_id").val();

        $.ajax({
            url: "/admin/get-seasonal-info",
            type: "GET",
            data: { vehicle_id: vehicleId },
            success: function (response) {
                if (response.success) {
                    $("#seasonal_append").html("");
                    response.data.forEach((season) => {
                        addSeasonalPricing(season);
                    });
                } else {
                }
            },
            error: function (xhr, status, error) {},
        });
    }

    function addSeasonalPricing(season) {
        let uniqueId = "season_" + season.id;
        let newSeasonalPricing = `
        <div id="${uniqueId}" class="d-flex align-items-center justify-content-between flex-wrap bg-white gap-3 border br-5 p-20 mb-1">
            <div class="flex-grow-1">
                <input type="hidden" name="seasonal_id[]" value="${season.id}">
                <h6 class="fs-14 fw-semibold mb-1">${season.seasonal_title}</h6>
                <input type="hidden" name="seasonal_title[]" value="${
                    season.seasonal_title
                }">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <p class="fs-13 fw-medium border-end pe-2 mb-0 start-date">
                    ${_l(
                        "admin.rentals.start_date"
                    )} : <span class="text-gray-9">${
            season.seasonal_start_date
        }</span>
                        <input type="hidden" name="seasonal_start_date[]" value="${
                            season.seasonal_start_date
                        }">
                    </p>
                    <p class="fs-13 fw-medium border-end pe-2 mb-0 end-date">
                    ${_l(
                        "admin.rentals.end_date"
                    )} : <span class="text-gray-9">${
            season.seasonal_end_date
        }</span>
                        <input type="hidden" name="seasonal_end_date[]" value="${
                            season.seasonal_end_date
                        }">
                    </p>
                    <p class="fs-13 fw-medium border-end pe-2 mb-0 daily-price">
                    ${_l(
                        "admin.rentals.seasonal_daily_price"
                    )} : <span class="text-gray-9">$${parseFloat(
            season.seasonal_daily_rate
        ).toFixed(0)}</span>
                        <input type="hidden" name="seasonal_daily_rate[]" value="${
                            season.seasonal_daily_rate
                        }">
                    </p>
                    <p class="fs-13 fw-medium border-end pe-2 mb-0 weekly-price">
                    ${_l(
                        "admin.rentals.seasonal_weekly_price"
                    )}  : <span class="text-gray-9">$${parseFloat(
            season.seasonal_weekly_rate
        ).toFixed(0)}</span>
                        <input type="hidden" name="seasonal_weekly_rate[]" value="${
                            season.seasonal_weekly_rate
                        }">
                    </p>
                    <p class="fs-13 fw-medium border-end pe-2 mb-0 monthly-price">
                    ${_l(
                        "admin.rentals.seasonal_monthly_price"
                    )} : <span class="text-gray-9">$${parseFloat(
            season.seasonal_monthly_rate
        ).toFixed(0)}</span>
                        <input type="hidden" name="seasonal_monthly_rate[]" value="${
                            season.seasonal_monthly_rate
                        }">
                    </p>
                    <p class="fs-13 fw-medium pe-2 mb-0 late-fee">
                    ${_l(
                        "admin.rentals.seasonal_late_fee"
                    )} : <span class="text-gray-9">$${parseFloat(
            season.seasonal_late_fee
        ).toFixed(0)}</span>
                        <input type="hidden" name="seasonal_late_fee[]" value="${
                            season.seasonal_late_fee
                        }">
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 icon-list">
                <a href="#" class="edit-icon d-flex align-items-center justify-content-center me-2" 
                   data-id="${uniqueId}" data-bs-toggle="modal" data-bs-target="#add_price">
                    <i class="ti ti-edit"></i>
                </a>
                <a href="#" class="trash-icon d-flex align-items-center justify-content-center"
                   data-id="${uniqueId}" data-bs-toggle="modal" data-bs-target="#delete_price">
                    <i class="ti ti-trash"></i>
                </a>
            </div>
        </div>`;

        $("#seasonal_append").append(newSeasonalPricing);
    }

    function getTrraifInfo() {
        let vehicleId = $("#vehicle_id").val();

        $.ajax({
            url: "/admin/get-tarrif-info",
            type: "GET",
            data: { vehicle_id: vehicleId },
            success: function (response) {
                if (response.success) {
                    $("#tariff_append").html("");
                    response.data.forEach((tarrif) => {
                        addTarrifPricing(tarrif);
                    });
                } else {
                    showToast("error", "No tarrif data found.");
                }
            },
            error: function (xhr, status, error) {},
        });
    }

    function addTarrifPricing(tarrif) {
        let uniqueId = "tariff_" + new Date().getTime();

        let newTariff = `
        <div id="${uniqueId}" class="d-flex align-items-center justify-content-between flex-wrap bg-white gap-3 border br-5 p-20 mb-1">
            <div>
                <input type="hidden" name="tariff_id[]" value="${tarrif.id}">
                <h6 class="fs-14 fw-semibold mb-1">${tarrif.tariff_title}</h6>
                <input type="hidden" name="tariff_title[]" value="${
                    tarrif.tariff_title
                }">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <p class="fs-13 fw-medium border-end pe-2 mb-0 daily-price">
                    ${_l(
                        "admin.rentals.day_price"
                    )} : <span class="text-gray-9">$${
            tarrif.tariff_daily_price
        }</span>
                        <input type="hidden" name="tariff_daily_price[]" value="${
                            tarrif.tariff_daily_price
                        }">
                    </p>
                    <p class="fs-13 fw-medium border-end pe-2 mb-0 from-days">
                    ${_l(
                        "admin.rentals.from_days"
                    )} : <span class="text-gray-9">${
            tarrif.tariff_from_days
        }</span>
                        <input type="hidden" name="tariff_from_days[]" value="${
                            tarrif.tariff_from_days
                        }">
                    </p>
                    <p class="fs-13 fw-medium border-end pe-2 mb-0 to-days">
                         ${_l(
                             "admin.rentals.to_days"
                         )} : <span class="text-gray-9">${
            tarrif.tariff_to_days
        }</span>
                        <input type="hidden" name="tariff_to_days[]" value="${
                            tarrif.tariff_to_days
                        }">
                    </p>
                    <p class="fs-13 fw-medium border-end pe-2 mb-0 base-km">
                    ${_l(
                        "admin.rentals.base_km"
                    )} : <span class="text-gray-9">${
            tarrif.tariff_base_km
        }</span>
                        <input type="hidden" name="tariff_base_km[]" value="${
                            tarrif.tariff_base_km
                        }">
                    </p>
                    <p class="fs-13 fw-medium pe-2 mb-0 extra-price">
                    ${_l(
                        "admin.rentals.extra_price"
                    )} : <span class="text-gray-9">$${
            tarrif.tariff_extra_price
        }</span>
                        <input type="hidden" name="tariff_extra_price[]" value="${
                            tarrif.tariff_extra_price
                        }">
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

    function getFaqInfo() {
        let vehicleId = $("#vehicle_id").val();

        $.ajax({
            url: "/admin/get-faq-info",
            type: "GET",
            data: { vehicle_id: vehicleId },
            success: function (response) {
                if (response.success) {
                    $(".car_faq_append").html("");
                    response.data.forEach((faq) => {
                        addFaq(faq);
                    });
                } else {
                    showToast("error", "No faq data found.");
                }
            },
            error: function (xhr, status, error) {},
        });
    }

    let faqCounter = 0; // Global counter to ensure unique IDs

    function addFaq(faq) {
        let uniqueID = "faq_" + faqCounter++; // Increment counter for each FAQ

        let faqItem = `
    <div class="accordion-item" id="faq_item_${uniqueID}">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button"
                data-bs-toggle="collapse" data-bs-target="#${uniqueID}"
                aria-expanded="false" aria-controls="${uniqueID}">
                <span class="faq-icon"><i class="ti ti-grip-vertical"></i></span> ${
                    faq.question
                }
            </button>
            <input type="hidden" name="faq_id[]" value="${
                faq.id ?? ""
            }" id="${uniqueID}_id">
            <input type="hidden" name="faq_question[]" value="${
                faq.question
            }" id="${uniqueID}_question">
        </h2>
        <div class="faq-actions text-end py-2 px-3">
            <i class="ti ti-edit edit-faq cursor-pointer" data-id="${uniqueID}"></i>
            <i class="ti ti-trash delete-faq cursor-pointer" data-id="${uniqueID}"></i>
        </div>
        <div id="${uniqueID}" class="accordion-collapse collapse" data-bs-parent="#faqaccordion">
            <div class="accordion-body">
                <p class="fs-13" id="${uniqueID}_text">${faq.answer}</p>
                <input type="hidden" name="faq_answer[]" value="${
                    faq.answer
                }" id="${uniqueID}_answer">
            </div>
        </div>
    </div>
    `;

        $(".car_faq_append").append(faqItem);
        updateFaqCount();
    }

    function updateFaqCount() {
        const count = $(".car_faq_append .accordion-item").length;
        $("#faq_count_display").text(count); // Assuming you have an element with this ID
    }

    function getDocumentsInfo() {
        let vehicleId = $("#vehicle_id").val(); // Get vehicle ID from input field

        $.ajax({
            url: "/admin/get-documents-info", // API route for fetching documents
            type: "GET",
            data: { vehicle_id: vehicleId },
            success: function (response) {
                if (response.success) {
                    // Clear existing content before appending new data
                    $("#car_images_append").html("");
                    $("#car_doc_append").html("");
                    $("#car_policy_append").html("");

                    // Process each category separately
                    response.data.vehicle_images.forEach((image) => {
                        addVehicleImage(image);
                    });

                    response.data.vehicle_docs.forEach((doc) => {
                        addVehicleDocument(doc);
                    });

                    response.data.vehicle_policies.forEach((policy) => {
                        addVehiclePolicy(policy);
                    });
                } else {
                    showToast("error", "No documents found.");
                }
            },
            error: function (xhr, status, error) {},
        });
    }

    function addVehicleDocument(docPath) {
        let fileListContainer = $("#car_doc_append");

        // Extract file name and extension
        let fileName = docPath.split("/").pop();

        // Simulate file size (since backend doesn’t provide it)
        let randomFileSize = Math.floor(Math.random() * (50 * 1024 * 1024)); // Random size up to 50MB
        let fileSizeText =
            randomFileSize < 1024 * 1024
                ? (randomFileSize / 1024).toFixed(2) + " KB"
                : (randomFileSize / (1024 * 1024)).toFixed(2) + " MB";

        // Calculate progress percentage
        let progressPercentage = Math.min(
            (randomFileSize / (50 * 1024 * 1024)) * 100,
            100
        ).toFixed(2);

        let fileItem = $(`
        <div class="d-flex align-items-center justify-content-between bg-white border br-5 gap-3 flex-wrap p-20 mb-2 file-item" data-file="${fileName}">
            <div class="d-flex align-items-center">
                <span><img src="/backend/assets/img/icons/pdf-icon.svg" alt="File Icon" width="30"></span>
                <div class="ms-2">
                    <h6 class="fs-14 fw-medium">Douments</h6>
                    <p class="fs-13">${fileSizeText} MB</p>
                </div>
            </div>
            <div class="progress-wrap w-50">
                <div class="progress progress-sm" role="progressbar">
                    <div class="progress-bar bg-success" style="width: ${progressPercentage}%;"></div>
                </div>
                <p class="fs-12 text-muted mt-1">${fileSizeText} of 50MB</p>
            </div>
            <div class="icon-list">
                <a href="javascript:void(0);" class="trash-icon doc-delete-file"><i class="ti ti-trash"></i></a>
            </div>
        </div>
    `);

        // Append to container
        fileListContainer.append(fileItem);

        // Add delete functionality
        fileItem.find(".doc-delete-file").on("click", function () {
            $(this).closest(".file-item").remove();
        });
    }

    function addVehiclePolicy(policy) {
        let fileListContainer = $("#car_policy_append");

        // Extract file name and extension
        let fileName = policy.split("/").pop();

        // Simulate file size (since backend doesn’t provide it)
        let randomFileSize = Math.floor(Math.random() * (50 * 1024 * 1024)); // Random size up to 50MB
        let fileSizeText =
            randomFileSize < 1024 * 1024
                ? (randomFileSize / 1024).toFixed(2) + " KB"
                : (randomFileSize / (1024 * 1024)).toFixed(2) + " MB";

        // Calculate progress percentage
        let progressPercentage = Math.min(
            (randomFileSize / (50 * 1024 * 1024)) * 100,
            100
        ).toFixed(2);

        let fileItem = $(`
        <div class="d-flex align-items-center justify-content-between bg-white border br-5 gap-3 flex-wrap p-20 mb-2 file-item" data-file="${fileName}">
            <div class="d-flex align-items-center">
                <span><img src="/backend/assets/img/icons/pdf-icon.svg" alt="File Icon" width="30"></span>
                <div class="ms-2">
                    <h6 class="fs-14 fw-medium">Douments</h6>
                    <p class="fs-13">${fileSizeText} MB</p>
                </div>
            </div>
            <div class="progress-wrap w-50">
                <div class="progress progress-sm" role="progressbar">
                    <div class="progress-bar bg-success" style="width: ${progressPercentage}%;"></div>
                </div>
                <p class="fs-12 text-muted mt-1">${fileSizeText} of 50MB</p>
            </div>
            <div class="icon-list">
                <a href="javascript:void(0);" class="trash-icon policy-delete-file"><i class="ti ti-trash"></i></a>
            </div>
        </div>
    `);

        // Append to container
        fileListContainer.append(fileItem);

        // Add delete functionality
        fileItem.find(".doc-delete-file").on("click", function () {
            $(this).closest(".file-item").remove();
        });
    }

    function addVehicleImage(imagePath) {
        let fileListContainer = $("#car_images_append");

        let imageItem = $(`
        <div class="uploaded-img" data-file="${imagePath}">
            <img src="${imagePath}" alt="Vehicle Image">
            <a href="javascript:void(0);" class="trash-icon fs-12 delete-image"><i class="ti ti-trash"></i></a>
        </div>
    `);

        // Append the image item to the container
        fileListContainer.append(imageItem);

        // Add delete functionality
        imageItem.find(".delete-image").on("click", function () {
            $(this).closest(".uploaded-img").remove();
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        let select = document.getElementById("sort_by");

        // Set "Latest" as default if no option is selected
        select.value = localStorage.getItem("sort_by") || "latest";

        function updateSelectText() {
            let selectedOption = select.options[select.selectedIndex];
            select.options[0].text = "Select : " + selectedOption.text;
        }

        // Update text on page load
        updateSelectText();

        select.addEventListener("change", function () {
            localStorage.setItem("sort_by", this.value);
            updateSelectText();
        });
    });

    $(document).ready(function () {
        $("#carBasicInfoForm").validate({
            rules: {
                vehicle_image: {
                    required: false,
                },
                title: {
                    required: true,
                    minlength: 3,
                    maxlength: 50,
                },
                perma_link: {
                    required: false,
                    url: true,
                },
                vehicle_type_id: {
                    required: true,
                },
                vehicle_brand_id: {
                    required: true,
                },
                vehicle_model_id: {
                    required: true,
                },
                vehicle_category_id: {
                    required: true,
                },
                plate_number: {
                    required: false,
                },
                vin_number: {
                    required: false,
                },
                main_location_id: {
                    required: true,
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
                    required: true,
                },
                vehicle_year: {
                    required: true,
                },
                vehicle_passenger: {
                    required: true,
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
                    $("#vehicle_image_error_container").html(error); // Append error to a separate div
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

        // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        // (Features & Amenities Validation and scripts)
        // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
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

        // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        // (Pricing & Tariff Validation and scripts)
        // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
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

            // Check if at least one price type is selected (checkboxes)
            let hasPriceType = priceTypes.some((priceType) => {
                return $(`[name="${priceType}"]`).is(":checked");
            });

            // Check if at least one price value is entered (input fields)
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

                // Update text labels
                editElement.find("h6").text(seasonName);
                editElement.find(".start-date span").text(startDate);
                editElement.find(".end-date span").text(endDate);
                editElement.find(".daily-price span").text(`$${dailyRate}`);
                editElement.find(".weekly-price span").text(`$${weeklyRate}`);
                editElement.find(".monthly-price span").text(`$${monthlyRate}`);
                editElement.find(".late-fee span").text(`$${lateFee}`);

                // Update hidden input values
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

                // Reset form labels and buttons
                $("#seas_title").text("Create Seasonal Pricing");
                $("#price_btn").text("Create New");
                editingId = null;
            } else {
                let uniqueId = "season_" + new Date().getTime();
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
                            )} : <span class="text-gray-9">$${dailyRate}</span>
                                <input type="hidden" name="seasonal_daily_rate[]" value="${dailyRate}">
                            </p>
                            <p class="fs-13 fw-medium border-end pe-2 mb-0 weekly-price">
                            ${_l(
                                "admin.rentals.seasonal_weekly_price"
                            )}  : <span class="text-gray-9">$${weeklyRate}</span>
                                <input type="hidden" name="seasonal_weekly_rate[]" value="${weeklyRate}">
                            </p>
                            <p class="fs-13 fw-medium border-end pe-2 mb-0 monthly-price">
                            ${_l(
                                "admin.rentals.seasonal_monthly_price"
                            )} : <span class="text-gray-9">$${monthlyRate}</span>
                                <input type="hidden" name="seasonal_monthly_rate[]" value="${monthlyRate}">
                            </p>
                            <p class="fs-13 fw-medium pe-2 mb-0 late-fee">
                            ${_l(
                                "admin.rentals.seasonal_late_fee"
                            )} : <span class="text-gray-9">$${lateFee}</span>
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
            $("#add_price input").val(""); // Clear input fields
        });

        $(document).on("click", ".edit-icon", function () {
            editingId = $(this).data("id");
            let editElement = $("#" + editingId);

            $("#s_name").val(editElement.find("h6").text());
            $("#s_strdate").val(editElement.find(".start-date span").text());
            $("#s_enddate").val(editElement.find(".end-date span").text());
            $("#s_drate").val(
                editElement.find(".daily-price span").text().replace("$", "")
            );
            $("#s_wrate").val(
                editElement.find(".weekly-price span").text().replace("$", "")
            );
            $("#s_mrate").val(
                editElement.find(".monthly-price span").text().replace("$", "")
            );
            $("#s_lrate").val(
                editElement.find(".late-fee span").text().replace("$", "")
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
            let tariffName = $("#t_name").val();
            let dailyPrice = $("#t_price").val();
            let fromDays = $("#t_fromday").val();
            let toDays = $("#t_today").val();
            let baseKilometers = $("#t_base").val();
            let extraPrice = $("#t_extra").val();
            let isUnlimited = $("#unlimited1").prop("checked")
                ? "Unlimited"
                : baseKilometers;

            // Validation
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

                // Update text labels
                editElement.find("h6").text(tariffName);
                editElement.find(".daily-price span").text(`$${dailyPrice}`);
                editElement.find(".from-days span").text(fromDays);
                editElement.find(".to-days span").text(toDays);
                editElement.find(".base-km span").text(isUnlimited);
                editElement.find(".extra-price span").text(`$${extraPrice}`);

                // Update hidden input values
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

                // Reset form labels and buttons
                $("#tarrif_title").text("Add New Tariff");
                $("#tarrif_btn").text("Create Tariff");
                editingTariffId = null;
            } else {
                let uniqueId = "tariff_" + new Date().getTime();

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
                        )} : <span class="text-gray-9">$${dailyPrice}</span>
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
                        )} : <span class="text-gray-9">$${extraPrice}</span>
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

        // Edit Tariff
        $(document).on("click", ".edit-tariff", function () {
            editingTariffId = $(this).data("id");
            let editElement = $("#" + editingTariffId);

            $("#t_name").val(editElement.find("h6").text());
            $("#t_price").val(
                editElement.find(".daily-price span").text().replace("$", "")
            );
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

            $("#t_extra").val(
                editElement.find(".extra-price span").text().replace("$", "")
            );

            $("#tarrif_title").text("Edit Tariff");
            $("#tarrif_btn").text("Update");
        });

        // Delete Tariff
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

        // Handle "Unlimited" Checkbox
        $("#unlimited1").on("change", function () {
            if ($(this).prop("checked")) {
                $("#t_base").val("").prop("disabled", true);
            } else {
                $("#t_base").prop("disabled", false);
            }
        });

        // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        // (Car Documents validation and scripts)
        // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
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

        // (Documents validation and scripts)
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
            let iconPath = ''; // 🛠️ Declare it here first
            if (fileExtension === "doc" || fileExtension === "docx") {
                iconPath = "/backend/assets/img/icons/pdf-icon.svg";
            }   else if (fileExtension === "txt") {
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

        // (policy validation and scripts)
        let policySelectedFiles = new Map();
        const policyAllowedExtensions = ["pdf", "doc", "docx", "txt"];

        $("#policy_document").on("change", function (event) {
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

            // Ensure policy documents are correctly assigned to the right input field
            $("#policy_document")[0].files = dataTransfer.files;
        }

        function policyGetFileTypeIcon(fileName) {
            let fileExtension = fileName.split(".").pop().toLowerCase();
            let iconPath = ''; // 🛠️ Declare it here first
            if (fileExtension === "doc" || fileExtension === "docx") {
                iconPath = "/backend/assets/img/icons/pdf-icon.svg";
            }  else if (fileExtension === "txt") {
                iconPath = "/backend/assets/img/icons/txt.svg";
            } else if (fileExtension === "pdf") {
                iconPath = "/backend/assets/img/icons/pdf-icon.svg";
            }

            return iconPath;
        }

        $(document).on("click", ".policy-delete-file", function () {
            let fileItem = $(this).closest(".file-item");
            let fileName = fileItem.data("file");

            // Confirm deletion
            if (!confirm("Are you sure you want to delete this policy file?"))
                return;

            // If the file is newly uploaded (not yet in the database)
            if (policySelectedFiles.has(fileName)) {
                policySelectedFiles.delete(fileName); // Remove from map
                fileItem.remove(); // Remove from UI
                policyUpdateFileInput(); // Update input field
                return;
            }

            // If it's an existing file, send an AJAX request to delete it from the database
            $.ajax({
                url: "/admin/vehicle/policy/delete",
                type: "POST",
                data: {
                    vehicle_id: $("#vehicle_id").val(), // Hidden input field with vehicle_id
                    file_path: fileName, // Send file path
                    _token: $('meta[name="csrf-token"]').attr("content"), // CSRF token
                },
                success: function (response) {
                    if (response.success) {
                        showToast(
                            "success",
                            "Policy file deleted successfully."
                        );
                        fileItem.remove(); // Remove from UI
                    } else {
                        showToast("error", response.message);
                    }
                },
                error: function () {
                    showToast("error", "Failed to delete policy file.");
                },
            });
        });

        let selectedImages = new Map();
        const allowedImageExtensions = ["jpg", "jpeg", "png", "gif", "webp"];
        const maxFileSize = 50 * 1024 * 1024;
        
        $("#car_images").on("change", function (event) {
            let files = event.target.files;
            let imageListContainer = $("#car_images_append");
            let validFiles = [];
            let remainingChecks = files.length;
        
            for (let i = 0; i < files.length; i++) {
                let file = files[i];
                let ext = file.name.split(".").pop().toLowerCase();
        
                // Invalid file type
                if (!allowedImageExtensions.includes(ext)) {
                    showToast("error", `File "${file.name}" is not a valid image.`);
                    remainingChecks--;
                    continue;
                }
        
                // File size too large
                if (file.size > maxFileSize) {
                    showToast("error", `File "${file.name}" exceeds the 50MB size limit.`);
                    remainingChecks--;
                    continue;
                }
        
                // Duplicate file
                if (selectedImages.has(file.name)) {
                    showToast("error", `File "${file.name}" is already selected.`);
                    remainingChecks--;
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
                        showToast("error", `Image "${file.name}" must be exactly 690x420 pixels.`);
                        URL.revokeObjectURL(imageUrl);
                    }
        
                    remainingChecks--;
                    if (remainingChecks === 0) updateImageInput(validFiles);
                };
        
                img.onerror = function () {
                    showToast("error", `Could not load image "${file.name}".`);
                    URL.revokeObjectURL(imageUrl);
                    remainingChecks--;
                    if (remainingChecks === 0) updateImageInput(validFiles);
                };
            }
        });
        

        function updateImageInput(
            validFiles = Array.from(selectedImages.values())
        ) {
            let dt = new DataTransfer();
            validFiles.forEach((file) => dt.items.add(file));
            $("#car_images")[0].files = dt.files;
        }

        $(document).on("click", ".delete-image", function () {
            let imageItem = $(this).closest(".uploaded-img");
            let fileName = imageItem.data("file");

            if (!confirm("Are you sure you want to delete this image?")) return;

            // Newly added (not in DB yet)
            if (selectedImages.has(fileName)) {
                selectedImages.delete(fileName);
                imageItem.remove();
                updateImageInput();
                return;
            }

            // Existing DB image
            $.ajax({
                url: "/admin/vehicle/image/delete",
                type: "POST",
                data: {
                    vehicle_id: $("#vehicle_id").val(),
                    image_path: fileName,
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    if (response.success) {
                        imageItem.remove();
                    }
                },
            });
        });

        $("#car_video").on("input", function () {
            let videoUrl = $(this).val().trim();
            let videoContainer = $("#car_video_append");

            // Regular expression to validate YouTube URLs
            let youtubeRegex =
                /^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/;

            // If input is empty, remove the appended video
            if (videoUrl === "") {
                videoContainer.html("");
                return;
            }

            // Check if the entered URL matches the YouTube format
            if (youtubeRegex.test(videoUrl)) {
                let videoItem = `
                    <img src="/assets/img/car/car-lg-01.jpg" alt="img">
                    <a href="${videoUrl}" target="_blank" data-fancybox="" class="play-icon">
                        <i class="ti ti-player-play-filled"></i>
                    </a>
            `;

                videoContainer.html(videoItem); // Append or replace the video
            } else {
                videoContainer.html(""); // Remove invalid input if it doesn’t match the format
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
                // showToast("error", "Please upload valid documents before proceeding.");
            }
        });

        // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        // (Damage validation and scripts)
        // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

        let editingDamageId = null;
        let deletingDamageId = null;

        // Image Preview on File Selection
        $("#dam_image").on("change", function (event) {
            let file = event.target.files[0];
            if (file) {
                let imageUrl = URL.createObjectURL(file);
                $("#image_preview").attr("src", imageUrl).removeClass("d-none");
            } else {
                $("#image_preview").attr("src", "").addClass("d-none");
            }
        });

        // Add or Edit Damage Entry
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

            if (!damageName || !damageType) {
                showToast("error", "Please fill in all required fields.");
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
                let uniqueId = "damage_" + new Date().getTime();
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

            // Reset modal fields and hide modal
            $("#add-damage").modal("hide");
            showToast("success", "Damage added successfully!");
        });

        // Update Damage Count
        function updateDamageCount() {
            let totalDamages = $("#car_damage_append > div").length;
            $("#damage_count").text(totalDamages.toString().padStart(2, "0"));
        }

        // Open Add Damage Modal and Reset Fields
        $(document).on("click", "#damage_car", function (e) {
            e.preventDefault();
            $("#damage_title").text("Add Damage");
            $("#damage_btn").text("Create New");

            // Clear fields
            $("#add-damage input, #add-damage textarea").val("");
            $("#add-damage select").prop("selectedIndex", 0).trigger("change");
            $("#image_preview").attr("src", "").addClass("d-none");
        });

        // Edit Damage
        $(document).on("click", ".edit-damage", function () {
            let damageId = $(this).data("id"); // Get the damage ID

            // Make AJAX request to fetch damage details using the damage ID
            $.ajax({
                url: "/admin/get-damage-details", // Adjust this URL according to your route
                type: "GET",
                data: { id: damageId }, // Send the damage ID as part of the payload
                success: function (response) {
                    if (response.success) {
                        let damage = response.data;

                        // If the data was returned from the server, populate the modal fields
                        populateDamageForm(damage);
                    } else {
                        // If no data found, fallback to using the existing hidden input values
                        let editElement = $("#" + damageId);

                        let damageName = editElement
                            .find("input[name='damage_location[]']")
                            .val();
                        let damageDesc = editElement
                            .find("input[name='damage_description[]']")
                            .val();
                        let imgSrc = editElement
                            .find("input[name='damage_image[]']")
                            .val();

                        // Populate the modal fields with the values from the hidden inputs
                        populateDamageForm({
                            damage_location: damageName,
                            description: damageDesc,
                            image: imgSrc,
                        });
                    }
                },
                error: function (xhr, status, error) {
                    showToast(
                        "error",
                        "Something went wrong while fetching damage details."
                    );
                },
            });
        });

        function populateDamageForm(damage) {
            $("#dam_dis").val(damage.description || ""); // Description field
            $("#damage_title").text("Edit Damage");
            $("#damage_btn").text("Update");

            $("#dam_name")
                .val(damage.damage_loaction || "")
                .trigger("change");

            $("#dam_type")
                .val(damage.damage_type || "")
                .trigger("change");

            if (damage.image) {
                $("#image_preview")
                    .attr("src", "/" + damage.image)
                    .removeClass("d-none");
            } else {
                $("#image_preview").attr("src", "").addClass("d-none");
            }

            // Reset file input so user can select a new image
            $("#dam_image").val("");
        }

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

        let editingFAQ = null; // Track the currently editing FAQ

        // Create / Update FAQ
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

                // Update button text properly inside the accordion
                $(`#faq_item_${uniqueID} .accordion-button`).html(
                    `<span><i class="ti ti-angle-down"></i></span> ${question}`
                );

                showToast("success", "FAQ updated successfully!");
                editingFAQ = null; // Reset after editing
            } else {
                // Add new FAQ
                let uniqueID = "faq_" + faqCounter++; // Unique ID for each FAQ
                let faqItem = `
            <div class="accordion-item" id="faq_item_${uniqueID}">
                <h2 class="accordion-header d-flex align-items-center justify-content-between">
                    <button class="accordion-button collapsed" type="button"
                        data-bs-toggle="collapse" data-bs-target="#${uniqueID}"
                        aria-expanded="false" aria-controls="${uniqueID}">
                        <span class="faq-icon"><i class="ti ti-grip-vertical"></i></span> ${question}
                    </button>
                    <input type="hidden" name="faq_id[]" value="${uniqueID}" id="${uniqueID}_id">
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

                $("input[name='extra_service[]']:checked").each(function () {
                    let serviceId = $(this).val(); // Get checked service ID

                    // Find the exact index of the selected service in service_id[]
                    let index = $("input[name='service_id[]']").index(
                        $(
                            "input[name='service_id[]'][value='" +
                                serviceId +
                                "']"
                        )
                    );

                    if (index !== -1) {
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
                    }
                });

                // Append the filtered extra services to FormData
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

                let damagePayload = [];

                $("input[name='damage_image[]']").each(function (index) {
                    let image = $(this).val()?.trim() || "";

                    let nameField = $("input[name='damage_name[]']").eq(index);
                    let name = nameField.length
                        ? nameField.val()?.trim() || ""
                        : "";

                    let locationField = $("input[name='damage_location[]']").eq(
                        index
                    );
                    let location = locationField.length
                        ? locationField.val()?.trim() || ""
                        : "";

                    let descriptionField = $(
                        "input[name='damage_description[]']"
                    ).eq(index);
                    let description = descriptionField.length
                        ? descriptionField.val()?.trim() || ""
                        : "";

                    let damageIdField = $("input[name='damage_id[]']").eq(
                        index
                    );
                    let damageId = damageIdField.length
                        ? damageIdField.val()?.trim() || ""
                        : "";

                    if (image && name && location && description) {
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
                $("#seoFinalBtn").text("Please Wait...").prop("disabled", true);

                $.ajax({
                    url: "/admin/update/vehicle",
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
                    beforeSend: function () {
                        $(".add_btn").attr("disabled", true);
                        $(".add_btn").html(
                            '<div class="spinner-border text-light" role="status"></div>'
                        );
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
                            .text("Update & Exit")
                            .prop("disabled", false);

                        if (error.status == 422) {
                            $.each(error.responseJSON, function (key, val) {
                                $("#" + key).addClass("is-invalid");
                                $("#" + key + "_error").text(val[0]);
                            });
                            $("#seoFinalBtn")
                                .text("Update & Exit")
                                .prop("disabled", false);
                        } else {
                            toastr(error.responseJSON.message, "bg-danger");
                            $("#seoFinalBtn")
                                .text("Update & Exit")
                                .prop("disabled", false);
                        }
                    });
            }
        });
    });
})();

let editingDamageID = null; // Track the item being edited
function editDamage(damageID) {
    let damageItem = $("#" + damageID);

    let damageType = damageItem.find("input[name='damage_name[]']").val();
    let damageLocation = damageItem
        .find("input[name='damage_location[]']")
        .val();
    let damageDescription = damageItem
        .find("input[name='damage_description[]']")
        .val();

    // Populate modal fields with existing values
    $("#dam_type").val(damageType);
    $("#dam_name").val(damageLocation);
    $("#dam_dis").val(damageDescription);

    // Change modal title and button text
    $("#damage_title").text("Edit Damage");
    $("#damage_btn").text("Update").attr("data-editing", "true");

    // Store the ID of the item being edited
    editingDamageID = damageID;
}

function editVechileList(vehicleSlug) {
    $.ajax({
        url: "/admin/check-vehicle",
        type: "GET",
        data: { vehicle_slug: vehicleSlug },
        success: function (response) {
            if (response.exists === "yes") {
                window.location.href = `/admin/edit-vehicle/${vehicleSlug}`;
            } else {
                showToast("error", "Vehicle not found.");
            }
        },
        error: function (xhr, status, error) {},
    });
}

document
    .getElementById("service_save_btn")
    .addEventListener("click", function () {
        // Get all table rows from the modal
        let tableRows = document.querySelectorAll(".custom-table1 tbody tr");

        tableRows.forEach((row) => {
            let serviceName = row.querySelector("#extra_name").innerText.trim();
            let extraValue = row.querySelector("#extra_value").value;
            let extraPrice = row.querySelector("#extra_price").value;

            // Find the matching service card in the main list
            let serviceCards = document.querySelectorAll(".extra-service-card");

            serviceCards.forEach((card) => {
                let cardName = card
                    .querySelector("#service_name")
                    .innerText.trim();

                if (cardName === serviceName) {
                    // Update the selected value
                    card.querySelector("#set_value").innerText =
                        extraValue === "per_day"
                            ? _l("admin.rentals.per_day")
                            : _l("admin.rentals.one_time");
                    card.querySelector("#service_value").value = extraValue;

                    // Update the price
                    card.querySelector(
                        "#set_price"
                    ).innerText = `$${extraPrice}`;
                    card.querySelector("#service_price").value = extraPrice;
                }
            });
        });

        // Close the modal
        $("#edit_price").modal("hide");
    });

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".delivery-add").forEach(function (container) {
        const plusIcon = container.querySelector(".plus-active");
        const checkIcon = container.querySelector(".check-active");
        const checkbox = container.querySelector("#insurance_checked");

        container.addEventListener("click", function (event) {
            event.preventDefault();
            if (checkbox.checked) {
                checkbox.checked = false;
                checkIcon.style.display = "none";
                plusIcon.style.display = "inline";
            } else {
                checkbox.checked = true;
                checkIcon.style.display = "inline";
                plusIcon.style.display = "none";
            }
        });
    });

    document.getElementById("in_btn").addEventListener("click", function () {
        const selectedInsurances = document.querySelectorAll(
            "#set_value .delivery-add input[type='checkbox']:checked"
        );
        const appendContainer = document.getElementById("insurance_car_append");

        // Clear previously appended elements
        appendContainer.innerHTML = "";

        selectedInsurances.forEach((checkbox) => {
            const container = checkbox.closest("#inCont");
            const insuranceId = container.querySelector("#insurance_id").value;
            const insuranceName =
                container.querySelector("#insurance_name").value;
            const insurancePrice =
                container.querySelector("#insurance_price").value;
            const insuranceCount =
                container.querySelector("#insurance_count").value;
            const insurancePriceType = container.querySelector(
                "#insurance_price_type"
            ).value;

            // Generate a unique ID for this insurance entry
            const uniqueId = `insurance_${Date.now()}_${Math.floor(
                Math.random() * 1000
            )}`;

            const newInsuranceDiv = document.createElement("div");
            newInsuranceDiv.className =
                "d-flex align-items-center justify-content-between flex-wrap bg-white gap-3 border br-5 p-20 mb-3";
            newInsuranceDiv.setAttribute("data-id", uniqueId);
            newInsuranceDiv.innerHTML = `
                    <div>
                        <h6 class="fs-14 fw-semibold d-inline-flex align-items-center mb-1">${insuranceName}</h6>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <p class="fs-13 fw-medium border-end pe-2 mb-0">${_l(
                                "admin.rentals.insurance_price"
                            )} : <span class="text-gray-9 priceIn" data-id="${uniqueId}">$${insurancePrice}</span></p>
                            <input type="hidden" name="insurance_id_one[]" id="insurance_id_one_${uniqueId}" value="${insuranceId}">
                            <input type="hidden" name="insurance_price_one[]" id="insurance_price_one_${uniqueId}" value="${insurancePrice}">
                            <p class="fs-13 fw-medium mb-0">${_l(
                                "admin.rentals.insurance_benefits"
                            )} : <span class="text-gray-9">${insuranceCount}</span></p>
                            <p class="fs-13 fw-medium mb-0">${_l(
                                "admin.rentals.insurance_price_type"
                            )} : <span class="text-gray-9 priceTypeIn" data-id="${uniqueId}">${insurancePriceType}</span></p>
                            <input type="hidden" name="insurance_price_type_one[]" id="insurance_price_type_one_${uniqueId}" value="${insurancePriceType}">
                        </div>
                    </div>
                    <div class="d-flex align-items-center icon-list">
                        <a href="#" class="edit-icon me-2" data-bs-toggle="modal" data-bs-target="#edit_insurance" 
                        data-id="${uniqueId}" data-price="${insurancePrice}" data-price-type="${insurancePriceType}"><i class="ti ti-edit"></i></a>
                        <a href="#" class="trash-icon" data-bs-toggle="modal" data-bs-target="#delete_insurance"><i class="ti ti-trash"></i></a>
                    </div>
                `;
            appendContainer.appendChild(newInsuranceDiv);
        });
        $("#select_insurance").modal("hide");
    });

    document.addEventListener("click", function (event) {
        if (event.target.closest(".edit-icon")) {
            const editButton = event.target.closest(".edit-icon");
            const uniqueId = editButton.getAttribute("data-id");
            const price = editButton.getAttribute("data-price");
            const priceType = editButton.getAttribute("data-price-type");

            document.getElementById("price").value = price;
            document
                .getElementById("edit_insurance")
                .setAttribute("data-id", uniqueId);

            document
                .querySelectorAll("input[name='Radio']")
                .forEach((radio) => {
                    if (
                        radio.nextElementSibling.innerText.trim() === priceType
                    ) {
                        radio.checked = true;
                    }
                });
        }
    });

    document
        .getElementById("save_update")
        .addEventListener("click", function () {
            const updatedPrice = document.getElementById("price").value;
            const updatedPriceType = document
                .querySelector("input[name='Radio']:checked")
                .nextElementSibling.innerText.trim();

            // Get the unique ID from the modal
            const uniqueId = document
                .getElementById("edit_insurance")
                .getAttribute("data-id");

            // Update only the selected entry
            document.querySelector(
                `.priceIn[data-id='${uniqueId}']`
            ).innerText = `$${updatedPrice}`;
            document.getElementById(`insurance_price_one_${uniqueId}`).value =
                updatedPrice;

            document.querySelector(
                `.priceTypeIn[data-id='${uniqueId}']`
            ).innerText = updatedPriceType;
            document.getElementById(
                `insurance_price_type_one_${uniqueId}`
            ).value = updatedPriceType;

            // Close the modal
            $("#edit_insurance").modal("hide");
        });
});

$(document).ready(function () {
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
});

document.addEventListener("click", function (event) {
    // Delete functionality
    if (event.target.closest(".trash-icon")) {
        event.preventDefault();
        const deleteButton = event.target.closest(".trash-icon");
        const container = deleteButton.closest("div[data-id]"); // Find the insurance container
        const uniqueId = container.getAttribute("data-id");

        // Remove from the appended list
        container.remove();

        // Uncheck the corresponding checkbox in the modal
        document
            .querySelectorAll("#set_value .delivery-add input[type='checkbox']")
            .forEach((checkbox) => {
                const parentContainer = checkbox.closest("#inCont");
                const insuranceId =
                    parentContainer.querySelector("#insurance_id").value;
                if (
                    document.getElementById(`insurance_id_one_${uniqueId}`)
                        ?.value === insuranceId
                ) {
                    checkbox.checked = false;
                    const plusIcon =
                        parentContainer.querySelector(".plus-active");
                    const checkIcon =
                        parentContainer.querySelector(".check-active");
                    checkIcon.style.display = "none";
                    plusIcon.style.display = "inline";
                }
            });
    }
});

$(document).ready(function () {
    function toggleKilometerFields() {
        if ($("#Baseunlimited").is(":checked")) {
            $("#basic_kilometer").prop("disabled", true).val("");
            $("#extra_kilometer").prop("disabled", true).val("");
        } else {
            $("#basic_kilometer").prop("disabled", false);
            $("#extra_kilometer").prop("disabled", false);
        }
    }

    // Run function on page load to handle default state
    toggleKilometerFields();

    // Bind change event to checkbox
    $("#Baseunlimited").change(function () {
        toggleKilometerFields();
    });
});

$(document).ready(function () {
    $("#delImg").on("click", function () {
        // Clear the file input field
        $("#vehicle_image").val("");

        // Remove the selected image (hide or reset to a default)
        $(".frames img").attr("src", "").hide(); // Hides the image after removal
    });
});

// $(document).on("click", ".change-language", function () {
//     var languageCode = $(this).data("language_code");

//     $.ajax({
//         url: "/admin/flag-change-language",
//         type: "POST",
//         data: { language_code: languageCode },
//         headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
//         success: function (response) {
//             if (response.status === "success") {
//                 location.reload();
//             }
//         }
//     });
// });

document.addEventListener("DOMContentLoaded", function () {
    const checkboxes = document.querySelectorAll(".price-checkbox");

    checkboxes.forEach((checkbox) => {
        checkbox.addEventListener("change", function () {
            let priceInput = document.getElementById(this.name + "_price");

            if (this.checked) {
                priceInput.removeAttribute("disabled"); // Enable the input
            } else {
                priceInput.setAttribute("disabled", "false"); // Disable the input
                priceInput.value = ""; // Clear the input value
            }
        });
    });

    document.querySelectorAll(".priceLimit").forEach((input) => {
        input.addEventListener("input", function () {
            this.value = this.value.replace(/\D/g, "").slice(0, 5);
        });
    });

    const titleInput = document.getElementById("title");
    const permalinkInput = document.getElementById("perma_link");
    const previewLink = document.querySelector(".link-info");

    titleInput.addEventListener("input", function () {
        let slug = titleInput.value
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, "") // Remove special characters
            .replace(/\s+/g, "-") // Replace spaces with dashes
            .replace(/-+/g, "-"); // Remove multiple dashes

        let baseUrl = "https://www.example.com/cars/";
        let fullUrl = baseUrl + slug;

        permalinkInput.value = fullUrl;
        previewLink.href = fullUrl;
        previewLink.textContent = fullUrl;
    });
});

$(document).ready(function () {
    $("#languageSelector").on("change", function () {
        var langId = $(this).val();

        // Get slug from current URL
        var pathSegments = window.location.pathname.split("/");
        var slug = pathSegments[pathSegments.length - 1];

        if (langId && slug) {
            window.location.href =
                "/admin/edit-vehicle/" + slug + "?language_id=" + langId;
        }
    });
});
