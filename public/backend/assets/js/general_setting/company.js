document.addEventListener("DOMContentLoaded", function () {
    const userPhoneInput = document.querySelector("#company_phone");
    const intlPhoneInput = document.querySelector(
        "#international_phone_number"
    );

    if (userPhoneInput) {
        window.iti = intlTelInput(userPhoneInput, {
            utilsScript: "/backend/assets/plugins/intltelinput/js/utils.js",
            separateDialCode: true,
        });
    }

    document
        .querySelector("#companySettingForm")
        .addEventListener("submit", function (event) {
            event.preventDefault();
            if (window.iti) {
                const intlNumber = window.iti.getNumber();
                intlPhoneInput.value = intlNumber;
            }
        });
});

(async () => {
    await loadTranslationFile("admin", "general_settings,common");
    ("use strict");
    $(document).ready(function () {
        company_list();

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

        $("#companySettingForm").validate({
            rules: {
                profile_photo: {
                    accept: "image/*",
                },
                organization_name: {
                    required: true,
                },
                owner_name: {
                    required: true,
                },
                company_email: {
                    required: true,
                    email: true,
                },
                company_phone: {
                    required: true,
                    pattern: /^[0-9]+$/,
                },
                industry: {
                    required: true,
                },
                team_size: {
                    required: true,
                },
                country: {
                    required: true,
                },
                state: {
                    required: true,
                },
                city: {
                    required: true,
                },
            },
            messages: {
                organization_name: {
                    required: _l(
                        "admin.general_settings.organization_name_required"
                    ),
                },
                owner_name: {
                    required: _l("admin.general_settings.owner_name_required"),
                },
                company_email: {
                    required: _l("admin.general_settings.enter_company_email"),
                    email: _l("admin.general_settings.enter_valid_email"),
                },
                company_phone: {
                    required: _l("admin.general_settings.enter_company_number"),
                    pattern: _l("admin.general_settings.enter_valid_number"),
                },
                industry: {
                    required: _l("admin.general_settings.select_industry"),
                },
                team_size: {
                    required: _l("admin.general_settings.select_team_size"),
                },
                country: {
                    required: _l("admin.general_settings.select_country"),
                },
                state: {
                    required: _l("admin.general_settings.select_state"),
                },
                city: {
                    required: _l("admin.general_settings.select_city"),
                },
            },
            errorPlacement: function (error, element) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
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
            submitHandler: function (form) {
                let companyData = new FormData(form);
                companyData.set(
                    "company_phone",
                    $("#international_phone_number").val()
                );

                $.ajax({
                    type: "POST",
                    url: "/admin/settings/company/store",
                    data: companyData,
                    processData: false,
                    contentType: false,
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    beforeSend: function () {
                        $(".companysave").attr("disabled", true).html(`
                        <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l(
                            "admin.common.saving"
                        )}..
                    `);
                    },
                    complete: function () {
                        $(".companysave")
                            .attr("disabled", false)
                            .html(_l("admin.common.save_changes"));
                    },
                    success: function (resp) {
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            company_list();
                        }
                    },
                    error: function (error) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");

                        if (error.responseJSON.code === 422) {
                            $.each(
                                error.responseJSON.errors,
                                function (key, val) {
                                    $("#" + key).addClass("is-invalid");
                                    $("#" + key + "_error").text(val[0]);
                                }
                            );
                        } else {
                            showToast("error", error.responseJSON.message);
                        }
                    },
                });
            },
        });

        $(document).ready(function () {
            $("#ownerSettingForm").validate({
                rules: {
                    owner_id: {
                        required: true,
                    },
                },
                messages: {
                    owner_id: {
                        required: "Please select an owner",
                    },
                },
                errorPlacement: function (error, element) {
                    var errorId = element.attr("name") + "_error";
                    $("#" + errorId).text(error.text());
                },
                highlight: function (element) {
                    $(element).addClass("is-invalid").removeClass("is-valid");
                },
                unhighlight: function (element) {
                    $(element).removeClass("is-invalid").addClass("is-valid");
                    var errorId = element.name + "_error";
                    $("#" + errorId).text("");
                },
                submitHandler: function (form) {
                    let ownerData = new FormData(form);
                    $(".ownershipChange")
                        .text("Please Wait...")
                        .prop("disabled", true);

                    $.ajax({
                        type: "POST",
                        url: "/admin/settings/ownership/transfer",
                        data: ownerData,
                        processData: false,
                        contentType: false,
                        headers: {
                            Accept: "application/json",
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                        success: function (resp) {
                            if (resp.code === 200) {
                                showToast("success", resp.message);
                                $(".ownershipChange")
                                    .text("Update")
                                    .prop("disabled", false);
                            }
                        },
                        error: function (error) {
                            $(".error-text").text("");
                            $(".form-control").removeClass(
                                "is-invalid is-valid"
                            );

                            if (error.responseJSON.code === 422) {
                                $.each(
                                    error.responseJSON.errors,
                                    function (key, val) {
                                        $("[name='" + key + "']").addClass(
                                            "is-invalid"
                                        );
                                        $("#" + key + "_error").text(val[0]);
                                    }
                                );
                            } else {
                                showToast("error", error.responseJSON.message);
                            }

                            $(".ownershipChange")
                                .text("Update")
                                .prop("disabled", false);
                        },
                    });
                },
            });

            $(document).on("click", ".ownershipChange", function () {
                $("#ownerSettingForm").submit();
            });
        });
    });

    $("#company_phone").on("input", function () {
        $(this).val(
            $(this)
                .val()
                .replace(/[^0-9]/g, "")
        );
    });

    function company_list() {
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
                            console.log(error);
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
                    .text(_l("admin.general_settings.save_changes"))
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
                    if (img.width === 500 && img.height === 500) {
                        preview.attr("src", e.target.result).show(); // Update preview
                        $(".frames").removeClass("d-none"); // Show the image container
                    } else {
                        showToast(
                            "error",
                            _l("admin.general_settings.image_dimension")
                        );
                        $("#company_profile_photo").val(""); // Reset file input
                    }
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
                        '<option value="">Select Country</option>'
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
                        '<option value="">Select State</option>'
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
                    $("#city").append('<option value="">Select City</option>');
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
                            '<option value="">Select Country</option>'
                        );
                        $.each(data, function (key, value) {
                            if (value.id == id) {
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
                    console.log(error);
                    reject({
                        message: _l("admin.general_settings.retrive_error"),
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
                            '<option value="">Select State</option>'
                        );
                        $.each(data, function (key, value) {
                            if (value.id == id) {
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
                    console.log(error);
                    reject({
                        message: "Something went wrong",
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
                            '<option value="">Select City</option>'
                        );
                        $.each(data, function (key, value) {
                            if (value.id == id) {
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
                    console.log(error);
                    reject({
                        message: "Something went wrong",
                    });
                },
            });
        });
    }
})();
