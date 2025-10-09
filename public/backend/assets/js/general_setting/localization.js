(async () => {
    "use strict";
    await loadTranslationFile("admin", "general_settings,common");
    $(document).ready(function () {
        getLocalizationSettings();

        $("#timezone").select2({
            minimumInputLength: 3,
            ajax: {
                url: "/admin/settings/get-timezones",
                dataType: "json",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                data: function (params) {
                    return {
                        search: params.term,
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.data,
                    };
                },
            },
        });

        $("#localizationForm").validate({
            rules: {
                timezone: {
                    required: true,
                },
                week_start_day: {
                    required: true,
                },
                date_format: {
                    required: true,
                },
                time_format: {
                    required: true,
                },
                default_language: {
                    required: true,
                },
                currency: {
                    required: true,
                },
                currency_symbol: {
                    required: true,
                },
                currency_position: {
                    required: true,
                },
                decimal_seperator: {
                    required: true,
                },
                thousand_seperator: {
                    required: true,
                },
            },
            messages: {
                timezone: {
                    required: _l("admin.general_settings.enter_localization"),
                },
                week_start_day: {
                    required: _l("admin.general_settings.week_start_day"),
                },
                date_format: {
                    required: _l("admin.general_settings.date_format"),
                },
                time_format: {
                    required: _l("admin.general_settings.time_format"),
                },
                default_language: {
                    required: _l("admin.general_settings.default_laguage"),
                },
                currency: {
                    required: _l("admin.general_settings.currency_required"),
                },
                currency_symbol: {
                    required: _l("admin.general_settings.currency_symbol"),
                },
                currency_position: {
                    required: _l("admin.general_settings.currency_position"),
                },
                decimal_seperator: {
                    required: _l(
                        "admin.general_settings.decimal_seperator_required"
                    ),
                },
                thousand_seperator: {
                    required: _l(
                        "admin.general_settings.Thousand_seperator_required"
                    ),
                },
            },
            errorPlacement: function (error, element) {
                let errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
                let errorId = element.id + "_error";
                $("#" + errorId).text("");
            },
            onkeyup: function (element) {
                $(element).valid();
            },
            onchange: function (element) {
                $(element).valid();
            },
            submitHandler: function (form) {
                let _formData = new FormData(form);

                $("#localizationForm .submitbtn").attr("disabled", true);
                $.ajax({
                    type: "POST",
                    url: "/admin/settings/update-localization",
                    data: _formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $("#localizationForm .submitbtn").attr("disabled", true)
                            .html(`
                        <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l(
                            "admin.common.saving"
                        )}..
                    `);
                    },
                    complete: function () {
                        $("#localizationForm .submitbtn")
                            .attr("disabled", false)
                            .html(_l("admin.common.save_changes"));
                    },
                    success: function (resp) {
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                        } else {
                            showToast("error", resp.message);
                        }
                        getLocalizationSettings();
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

                        getLocalizationSettings();
                    },
                });
            },
        });

        $(document).on("change", "#currency", function () {
            let currency = $(this).val();
            let symbol = $(this).find(":selected").data("symbol");
            $("#currency_symbol").empty();
            $("#currency_position").empty();
            if (currency && symbol) {
                $("#currency_symbol").append(
                    `<option value="${currency}">${symbol}</option>`
                );
                $("#currency_position").append(
                    `<option value="before">${symbol}100</option><option value="after">100${symbol}</option>`
                );
            } else {
                $("#currency_symbol").append(
                    `<option value="">${_l(
                        "admin.general_settings.select"
                    )}</option>`
                );
            }
            $("#currency_symbol").trigger("change");
        });

        function getTimizone() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: "GET",
                    url: "/admin/settings/get-timezone",
                    success: function (resp) {
                        const timezoneSelect = $("#timezone");
                        timezoneSelect.empty();

                        timezoneSelect.append(
                            $("<option>")
                                .val("")
                                .text(_l("admin.general_settings.select"))
                        );

                        if (
                            resp.code === 200 &&
                            resp.data?.id &&
                            resp.data?.name
                        ) {
                            const safeId = DOMPurify.sanitize(
                                resp.data.id.toString()
                            );
                            const safeName = DOMPurify.sanitize(resp.data.name);

                            timezoneSelect.append(
                                $("<option>").val(safeId).text(safeName)
                            );
                        }
                        timezoneSelect.trigger("change");
                        resolve(resp);
                    },
                    error: function (error) {
                        resolve(error.responseJSON);
                    },
                });
            });
        }
        function getLocalizationSettings() {
            getTimizone().then((resp) => {
                $.ajax({
                    type: "POST",
                    url: "/admin/settings/list",
                    data: {
                        group_id: 5,
                        _token: $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function (resp) {
                        if (resp.code === 200) {
                            let data = resp.data;
                            $.each(data, function (k, val) {
                                switch (val.key) {
                                    case "timezone":
                                        $("#timezone")
                                            .val(val.value)
                                            .trigger("change");
                                        break;
                                    case "week_start_day":
                                        $("#week_start_day")
                                            .val(val.value)
                                            .trigger("change");
                                        break;
                                    case "date_format":
                                        $("#date_format")
                                            .val(val.value)
                                            .trigger("change");
                                        break;
                                    case "time_format":
                                        $("#time_format")
                                            .val(val.value)
                                            .trigger("change");
                                        break;
                                    case "default_language":
                                        $("#default_language")
                                            .val(val.value)
                                            .trigger("change");
                                        break;
                                    case "currency":
                                        $("#currency")
                                            .val(val.value)
                                            .trigger("change");
                                        break;
                                    case "currency_symbol":
                                        $("#currency_symbol")
                                            .val(val.value)
                                            .trigger("change");
                                        break;
                                    case "currency_position":
                                        $("#currency_position")
                                            .val(val.value)
                                            .trigger("change");
                                        break;
                                    case "decimal_seperator":
                                        $("#decimal_seperator")
                                            .val(val.value)
                                            .trigger("change");
                                        break;
                                    case "thousand_seperator":
                                        $("#thousand_seperator")
                                            .val(val.value)
                                            .trigger("change");
                                        break;
                                    case "currency_switcher":
                                        if (val.value == 1) {
                                            $("#currency_switcher").prop(
                                                "checked",
                                                true
                                            );
                                        } else {
                                            $("#currency_switcher").prop(
                                                "checked",
                                                false
                                            );
                                        }
                                        break;
                                    case "language_switcher":
                                        if (val.value == 1) {
                                            $("#language_switcher").prop(
                                                "checked",
                                                true
                                            );
                                        } else {
                                            $("#language_switcher").prop(
                                                "checked",
                                                false
                                            );
                                        }
                                        break;
                                }
                            });
                        }
                    },
                    error: function (error) {
                        console.log(error);
                    },
                    complete: function () {
                        $(".label-loader, .input-loader, .card-loader").hide();
                        $(".real-label, .real-input, .real-card").removeClass(
                            "d-none"
                        );
                    },
                });
            });
        }
    });
})();
