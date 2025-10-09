(async () => {
    "use strict";
    await loadTranslationFile("admin", "common, general_settings");
    let iti;

    $(document).ready(function () {
        initInternationalPhoneInput();
        profile_list();
        fetchCountries();

        $("#profile_photo").on("change", handleProfilePhotoChange);

        $("#country").on("change", function () {
            const id = $(this).val();
            if (id) {
                fetchStatesByCountry(id);
            } else {
                resetDropdown("#state");
                resetDropdown("#city");
            }
        });

        $("#state").on("change", function () {
            const id = $(this).val();
            if (id) {
                fetchCitiesByState(id);
            } else {
                resetDropdown("#city");
            }
        });

        $("#adminProfileForm").validate({
            rules: getAdminProfileRules(),
            messages: getAdminProfileMessages(),
            errorPlacement: placeError,
            highlight: highlightElement,
            unhighlight: unhighlightElement,
            onkeyup: validateOnInput,
            onchange: validateOnInput,
            submitHandler: function (form) {
                const adminProfileData = new FormData(form);
                adminProfileData.set(
                    "phone",
                    $("#international_phone_number").val()
                );

                $.ajax({
                    type: "POST",
                    url: "/admin/update_profile",
                    data: adminProfileData,
                    processData: false,
                    contentType: false,
                    beforeSend: () => toggleButton(true),
                    complete: () => toggleButton(false),
                    success: function (resp) {
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            profile_list();
                        }
                    },
                    error: handleFormError,
                });
            },
        });

        $("#admin_phone").on("input", function () {
            $(this).val(
                $(this)
                    .val()
                    .replace(/\D/g, "")
            );
        });
    });

    function handleProfilePhotoChange(event) {
        const file = event.target.files[0];
        const preview = $("#profile_photo_preview");

        if (!file) return;

        if (isFileTooLarge(file)) {
            showToast("error", _l("admin.general_settings.image_5mb"));
            $("#profile_photo").val("");
            return;
        }

        readFile(file, (dataURL) => displayPreview(dataURL, preview));
    }

    function isFileTooLarge(file) {
        return file.size > 5 * 1024 * 1024;
    }

    function readFile(file, callback) {
        const reader = new FileReader();
        reader.onload = (e) => callback(e.target.result);
        reader.readAsDataURL(file);
    }

    function displayPreview(dataURL, preview) {
        const img = new Image();
        img.onload = () => {
            preview.attr("src", dataURL).show();
            $(".frames").removeClass("d-none");
        };
        img.src = dataURL;
    }

    function resetDropdown(selector) {
        $(selector)
            .empty()
            .append(`<option value="">${_l("admin.common.select")}</option>`);
    }

    function placeError(error, element) {
        const errorId = element.attr("id") + "_error";
        $("#" + errorId).text(error.text());
    }

    function highlightElement(element) {
        $(element).addClass("is-invalid").removeClass("is-valid");
        $("#" + element.id)
            .siblings("span")
            .addClass("me-3");
    }

    function unhighlightElement(element) {
        $(element).removeClass("is-invalid").addClass("is-valid");
        const errorId = element.id + "_error";
        $("#" + errorId).text("");
        $("#" + element.id)
            .siblings("span")
            .addClass("me-3");
    }

    function validateOnInput(element) {
        $(element).valid();
    }

    function toggleButton(disable) {
        const button = $(".btn-primary");
        button
            .prop("disabled", disable)
            .html(
                disable
                    ? `<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l(
                          "admin.common.saving"
                      )}..`
                    : _l("admin.common.save_changes")
            );
    }

    function handleFormError(error) {
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
    }

    function getAdminProfileRules() {
        return {
            profile_photo: {
                required: false,
                accept: "image/*",
                extension: "jpg|jpeg|png|gif",
            },
            first_name: { required: true, maxlength: 30 },
            last_name: { required: true, maxlength: 30 },
            email: { required: true, email: true },
            admin_phone: { required: true, pattern: /^\d+$/, maxlength: 15 },
            address_line: { required: true, maxlength: 50 },
            postal_code: { required: true, pattern: /^[\da-zA-Z]+$/ },
            current_password: { required: false },
            new_password: { required: false, minlength: 6 },
            confirm_password: { required: false, equalTo: "#new_password" },
        };
    }

    function getAdminProfileMessages() {
        return {
            first_name: {
                required: _l("admin.general_settings.enter_first_name"),
                maxlength: _l("admin.general_settings.first_name_max"),
            },
            last_name: {
                required: _l("admin.general_settings.enter_last_name"),
                maxlength: _l("admin.general_settings.last_name_max"),
            },
            email: {
                required: _l("admin.general_settings.enter_email"),
                email: _l("admin.general_settings.email_invalid"),
            },
            admin_phone: {
                required: _l("admin.general_settings.enter_phone_number"),
                pattern: _l("admin.general_settings.phone_invalid"),
                maxlength: _l("admin.general_settings.phone_max"),
            },
            address_line: {
                required: _l("admin.general_settings.enter_address"),
                maxlength: _l("admin.general_settings.address_max"),
            },
            postal_code: {
                required: _l("admin.general_settings.postal_code_required"),
                pattern: _l("admin.general_settings.postal_invalid"),
            },
            new_password: {
                minlength: _l("admin.general_settings.password_min"),
            },
            confirm_password: {
                equalTo: _l("admin.general_settings.password_mismatch"),
            },
            profile_photo: {
                accept: _l("admin.general_settings.image_accept"),
                extension: _l("admin.general_settings.image_extension"),
                filesize: _l("admin.general_settings.image_size"),
            },
        };
    }

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
                    $("#city").append('<option value="">Select City</option>');
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
                    $("#city").append(
                        `<option value="">${_l("admin.common.select")}</option>`
                    );
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

    function fetchCountryAjax(id) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "GET",
                url: "/api/countries",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    if (response.code === 200) {
                        let data = response.data;
                        $("#country").empty();
                        $("#country").append(
                            `<option value="">${_l(
                                "admin.common.select"
                            )}</option>`
                        );
                        $.each(data, function (key, value) {
                            if (value.id === id) {
                                $("#country").append(
                                    '<option value="' +
                                        value.id +
                                        '" selected>' +
                                        value.name +
                                        "</option>"
                                );
                            } else {
                                $("#country").append(
                                    '<option value="' +
                                        value.id +
                                        '">' +
                                        value.name +
                                        "</option>"
                                );
                            }
                        });
                        resolve();
                    }
                },
                error: function (error) {
                    reject({
                        message: "Something went wrong",
                    });
                },
            });
        });
    }

    function fetchStateAjax(country_id, id) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "POST",
                url: "/api/states",
                data: { country_id: country_id },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                    accept: "application/json",
                },
                success: function (response) {
                    if (response.code === 200) {
                        let data = response.data;
                        $("#state").empty();
                        $("#state").append(
                            `<option value="">${_l(
                                "admin.common.select"
                            )}</option>`
                        );
                        $.each(data, function (key, value) {
                            if (value.id === id) {
                                $("#state").append(
                                    '<option value="' +
                                        value.id +
                                        '" selected>' +
                                        value.name +
                                        "</option>"
                                );
                            } else {
                                $("#state").append(
                                    '<option value="' +
                                        value.id +
                                        '">' +
                                        value.name +
                                        "</option>"
                                );
                            }
                        });
                        resolve();
                    }
                },
                error: function (error) {
                    reject({
                        message: _l("admin.general_settings.retrive_error"),
                    });
                },
            });
        });
    }

    function fetchCityAjax(state_id, id) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "POST",
                url: "/api/cities",
                data: { state_id: state_id },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                    accept: "application/json",
                },
                success: function (response) {
                    if (response.code === 200) {
                        let data = response.data;
                        $("#city").empty();
                        $("#city").append(
                            `<option value="">${_l(
                                "admin.common.select"
                            )}</option>`
                        );
                        $.each(data, function (key, value) {
                            if (value.id === id) {
                                $("#city").append(
                                    '<option value="' +
                                        value.id +
                                        '" selected>' +
                                        value.name +
                                        "</option>"
                                );
                            } else {
                                $("#city").append(
                                    '<option value="' +
                                        value.id +
                                        '">' +
                                        value.name +
                                        "</option>"
                                );
                            }
                        });
                        resolve();
                    }
                },
                error: function (error) {
                    reject({
                        message: _l("admin.general_settings.retrive_error"),
                    });
                },
            });
        });
    }

    function profile_list() {
        $.ajax({
            type: "GET",
            url: "/admin/profile/1",
            processData: false,
            contentType: false,
            success: function (resp) {
                if (resp.code === 200) {
                    const data = resp.data;

                    $("#id").val(data.id);
                    $("#email").val(data.email);
                    $("#first_name").val(data.first_name);
                    $("#last_name").val(data.last_name);
                    $("#address_line").val(data.address_line);
                    $("#postal_code").val(data.postal_code);

                    if (data.phone && iti) {
                        iti.setNumber(data.phone);

                        const countryData = iti.getSelectedCountryData();
                        const dialCode = countryData.dialCode;

                        const localNumber = data.phone
                            .replace("+" + dialCode, "")
                            .trim();
                        $(".admin_phone").val(localNumber);
                    }

                    fetchCountryAjax(data.country)
                        .then(() => fetchStateAjax(data.country, data.state))
                        .then(() => fetchCityAjax(data.state, data.city))
                        .then(() => {
                            if (
                                data.working_days &&
                                data.working_days.length > 0
                            ) {
                                $.each(
                                    data.working_days,
                                    function (index, value) {
                                        $("#" + value.day).prop(
                                            "checked",
                                            true
                                        );
                                        $("#" + value.day + "_start").val(
                                            value.start_time
                                        );
                                        $("#" + value.day + "_end").val(
                                            value.end_time
                                        );
                                    }
                                );
                            }
                        });

                    if (data.profile_photo) {
                        $("#profile_photo_preview").attr(
                            "src",
                            data.profile_photo
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

                $(".btn-primary").text("Save Changes").prop("disabled", false);
            },
            complete: function () {
                $(".label-loader, .input-loader, .card-loader").addClass(
                    "d-none"
                );
                $(".real-label, .real-input, .real-card").removeClass("d-none");
            },
        });
    }

    function initInternationalPhoneInput() {
        const userPhoneInput = document.querySelector(".admin_phone");
        const intlPhoneInput = document.querySelector(
            "#international_phone_number"
        );

        if (userPhoneInput) {
            iti = intlTelInput(userPhoneInput, {
                utilsScript:
                    window.location.origin +
                    "/backend/assets/plugins/intltelinput/js/utils.js",
                    separateDialCode: true,
                    placeholderNumberType: "",
                    autoPlaceholder: "off",
                    formatOnDisplay: false
            });

            userPhoneInput.classList.add("iti");
            userPhoneInput.parentElement.classList.add("intl-tel-input");

            document
                .querySelector("#adminProfileForm")
                .addEventListener("submit", function (event) {
                    event.preventDefault();

                    const intlNumber = iti.getNumber();
                    if (intlNumber) {
                        document.querySelector(
                            "#international_phone_number"
                        ).value = intlNumber;

                        intlPhoneInput.value = intlNumber;
                    }
                });
        }
    }
})();
