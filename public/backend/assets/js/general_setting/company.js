(async () => {
    "use strict";
    await loadTranslationFile("admin", "general_settings,common");

    $(document).ready(function () {
        companyList();
        initInternationalPhoneInput();
        fetchCountries();

        $("#country").on("change", function () {
            let id = $(this).val();
            if (id) {
                fetchStatesByCountry(id);
            } else {
                $("#state").empty();
                $("#state").append(
                    `<option value="">${_l("admin.common.select")}</option>`
                );
                $("#city").empty();
                $("#city").append(
                    `<option value="">${_l("admin.common.select")}</option>`
                );
            }
        });

        $("#state").on("change", function () {
            let id = $(this).val();
            if (id) {
                fetchCitiesByState(id);
            } else {
                $("#city").empty();
                $("#city").append(
                    `<option value="">${_l("admin.common.select")}</option>`
                );
            }
        });

        // Initialize
        initCompanyForm();
    });

    // Initialize company form validation
    function initCompanyForm() {
        $("#companySettingForm").validate({
            rules: getCompanyRules(),
            messages: getCompanyMessages(),
            errorPlacement: handleCompanyErrorPlacement,
            highlight: handleHighlight,
            unhighlight: handleUnhighlight,
            onkeyup: (el) => $(el).valid(),
            onchange: (el) => $(el).valid(),
            submitHandler: submitCompanyForm,
        });
    }

    // Validation rules
    function getCompanyRules() {
        return {
            profile_photo: { accept: "image/*" },
            organization_name: { required: true },
            owner_name: { required: true },
            company_email: { required: true, email: true },
            company_phone: { required: true, pattern: /^\d+$/ },
        };
    }

    // Validation messages
    function getCompanyMessages() {
        return {
            organization_name: { required: _l("admin.general_settings.organization_name_required") },
            owner_name: { required: _l("admin.general_settings.owner_name_required") },
            company_email: {
                required: _l("admin.general_settings.enter_company_email"),
                email: _l("admin.general_settings.enter_valid_email")
            },
            company_phone: {
                required: _l("admin.general_settings.enter_company_number"),
                pattern: _l("admin.general_settings.enter_valid_number")
            },
            industry: { required: _l("admin.general_settings.select_industry") },
            team_size: { required: _l("admin.general_settings.select_team_size") },
            country: { required: _l("admin.general_settings.select_country") },
            state: { required: _l("admin.general_settings.select_state") },
            city: { required: _l("admin.general_settings.select_city") },
        };
    }

    // Error placement
    function handleCompanyErrorPlacement(error, element) {
        const errorId = element.attr("id") + "_error";
        $("#" + errorId).text(error.text());
    }

    // Highlight invalid fields
    function handleHighlight(element) {
        $(element).addClass("is-invalid").removeClass("is-valid");
    }

    // Unhighlight valid fields
    function handleUnhighlight(element) {
        $(element).removeClass("is-invalid").addClass("is-valid");
        $("#" + element.id + "_error").text("");
    }

    // Submit form via AJAX
    function submitCompanyForm(form) {
        const formData = new FormData(form);
        formData.set("company_phone", $("#international_phone_number").val());
        const $btn = $(".companysave");

        setCompanySavingState($btn, true);

        $.ajax({
            type: "POST",
            url: "/admin/settings/company/store",
            data: formData,
            processData: false,
            contentType: false,
            headers: getCsrfHeaders(),
            success: (resp) => handleCompanySuccess(resp),
            error: (error) => handleCompanyError(error),
            complete: () => setCompanySavingState($btn, false),
        });
    }

    // CSRF headers
    function getCsrfHeaders() {
        return {
            Accept: "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        };
    }

    // Set button saving state
    function setCompanySavingState($btn, isSaving) {
        if (isSaving) {
            $btn.attr("disabled", true).html(`
                <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l("admin.common.saving")}..
            `);
        } else {
            $btn.attr("disabled", false).html(_l("admin.common.save_changes"));
        }
    }

    // AJAX success
    function handleCompanySuccess(resp) {
        if (resp.code === 200) {
            showToast("success", resp.message);
            companyList();
        }
    }

    // AJAX error
    function handleCompanyError(error) {
        $(".error-text").text("");
        $(".form-control").removeClass("is-invalid is-valid");

        if (error.responseJSON?.code === 422) {
            Object.entries(error.responseJSON.errors).forEach(([key, val]) => {
                $("#" + key).addClass("is-invalid");
                $("#" + key + "_error").text(val[0]);
            });
        } else {
            showToast("error", error.responseJSON?.message || "Something went wrong");
        }
    }

    $("#company_phone").on("input", function () {
        $(this).val(
                $(this)
                    .val()
                    .replace(/\D/g, "").slice(0, 15)
            );
    });

    function companyList() {
        $.ajax({
            type: "POST",
            url: "/admin/settings/company/list/new",
            data: { group_id: 1 },
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (resp) {
                if (resp.code === 200) {
                    const data = resp.data;

                    $("#organization_name").val(data.organization_name);
                    $("#owner_name").val(data.owner_name);
                    $("#company_email").val(data.company_email);
                    $("#industry").val(data.industry).trigger("change");
                    $("#team_size").val(data.team_size).trigger("change");
                    $("#company_address_line").val(data.company_address_line);
                    $("#company_postal_code").val(data.company_postal_code);

                    fetchCountryAjax(data.country)
                        .then(() => fetchStateAjax(data.country, data.state))
                        .then(() => fetchCityAjax(data.state, data.city))
                        .catch((error) => {
                            console.error(error);
                        });

                    if (window.iti && data.company_phone) {
                        window.iti.setNumber(data.company_phone);
                    }

                    if (data.company_profile_photo) {
                        $("#profile_photo_preview").attr(
                            "src",
                            data.company_profile_photo
                        );
                    }
                }
            },
            error: function (error) {
                $(".error-text").text("");
                $(".form-control").removeClass("is-invalid is-valid");

                if (error.responseJSON.code === 422) {
                    $.each(error.responseJSON.errors, function (key, val) {
                        $("#" + key).addClass("is-invalid");
                        $("#" + key + "_error").text(val[0]);
                    });
                } else {
                    showToast("error", error.responseJSON.message);
                }

                $(".btn-primary")
                    .text(_l("admin.common.save_changes"))
                    .prop("disabled", false);
            },
            complete: function () {
                $(".label-loader, .input-loader, .card-loader").hide();
                $(".real-label, .real-input, .real-card").removeClass("d-none");
            },
        });
    }

    $("#company_profile_photo").on("change", function (event) {
        const file = event.target.files[0];
        const reader = new FileReader();
        const preview = $("#profile_photo_preview");

        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                showToast("error", _l("admin.general_settings.image_5mb"));
                $(this).val("");
                return;
            }

            reader.onload = function (e) {
                const img = new Image();
                img.src = e.target.result;

                img.onload = function () {
                    preview.attr("src", e.target.result).show();
                    $(".frames").removeClass("d-none");
                };
            };

            reader.readAsDataURL(file);
        }
    });

    function fetchCountries() {
        $.ajax({
            type: "GET",
            url: "/api/countries",
            headers: {
                accept: "application/json",
            },
            success: function (response) {
                if (response.code === 200) {
                    let data = response.data;
                    $("#country").empty();
                    $("#country").append(
                        `<option value="">${_l("admin.common.select")}</option>`
                    );
                    $.each(data, function (key, value) {
                        $("#country").append(
                            '<option value="' +
                                value.id +
                                '">' +
                                value.name +
                                "</option>"
                        );
                    });
                }
            },
        });
    }

    function fetchStatesByCountry(country_id) {
        $.ajax({
            type: "POST",
            url: "/api/states",
            data: { country_id: country_id },
            headers: {
                accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.code === 200) {
                    let data = response.data;
                    $("#state").empty();
                    $("#state").append(
                        `<option value="">${_l("admin.common.select")}</option>`
                    );
                    $.each(data, function (key, value) {
                        $("#state").append(
                            '<option value="' +
                                value.id +
                                '">' +
                                value.name +
                                "</option>"
                        );
                    });

                    $("#city").empty();
                    $("#city").append(`<option value="">${_l("admin.common.select")}</option>`);
                }
            },
        });
    }

    function fetchCitiesByState(state_id) {
        $.ajax({
            type: "POST",
            url: "/api/cities",
            data: { state_id: state_id },
            headers: {
                accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.code === 200) {
                    let data = response.data;
                    $("#city").empty();
                    $("#city").append(`${_l("admin.common.select")}`);
                    $.each(data, function (key, value) {
                        $("#city").append(
                            '<option value="' +
                                value.id +
                                '">' +
                                value.name +
                                "</option>"
                        );
                    });
                }
            },
        });
    }

    // Generic AJAX dropdown fetcher
    function fetchDropdown({ url, data = {}, dropdownSelector, selectedId }) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: Object.keys(data).length ? "POST" : "GET",
                url,
                data,
                headers: getCsrfHeaders(),
                success: (response) => handleDropdownSuccess(response, dropdownSelector, selectedId, resolve),
                error: (error) => handleDropdownError(error, reject),
            });
        });
    }

    // Handle success response
    function handleDropdownSuccess(response, selector, selectedId, resolve) {
        if (response.code !== 200) return;

        populateDropdown(selector, response.data, selectedId);
        resolve();
    }

    // Handle error
    function handleDropdownError(error, reject) {
        console.error(error);
        reject({ message: _l("admin.general_settings.retrive_error") });
    }

    // Populate dropdown options
    function populateDropdown(selector, data, selectedId) {
        const $dropdown = $(selector);
        $dropdown.empty();
        $dropdown.append(`<option value="">${_l("admin.common.select")}</option>`);

        data.forEach((item) => {
            const selected = item.id == selectedId ? "selected" : "";
            $dropdown.append(`<option value="${item.id}" ${selected}>${item.name}</option>`);
        });
    }

    // Fetch country
    function fetchCountryAjax(id) {
        return fetchDropdown({ url: "/api/countries", dropdownSelector: "#country", selectedId: id });
    }

    // Fetch state
    function fetchStateAjax(country_id, id) {
        return fetchDropdown({ url: "/api/states", data: { country_id }, dropdownSelector: "#state", selectedId: id });
    }

    // Fetch city
    function fetchCityAjax(state_id, id) {
        return fetchDropdown({ url: "/api/cities", data: { state_id }, dropdownSelector: "#city", selectedId: id });
    }

    function initInternationalPhoneInput() {
        const userPhoneInput = document.querySelector("#company_phone");
        const intlPhoneInput = document.querySelector(
            "#international_phone_number"
        );

        if (userPhoneInput) {
            window.iti = intlTelInput(userPhoneInput, {
                utilsScript: "/backend/assets/plugins/intltelinput/js/utils.js",
                separateDialCode: true,
                placeholderNumberType: "",
                autoPlaceholder: "off",
                formatOnDisplay: false
            });
        }

        document.querySelector("#companySettingForm")
            .addEventListener("submit", function (event) {
                event.preventDefault();
                if (window.iti) {
                    const intlNumber = window.iti.getNumber();
                    intlPhoneInput.value = intlNumber;
                }
            });
    }
})();
