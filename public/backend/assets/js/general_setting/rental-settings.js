(async () => {
    "use strict";
    await loadTranslationFile('admin', 'general_settings,common');

    $(document).ready(function () {
        $("#rentalSettingForm").validate({
            rules: {
               
            },
            messages: {
               
            },
            errorPlacement: function (error, element) {
                $("#" + element.attr("id") + "Error").text(error.text()).removeClass("d-none");
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
                $("#" + element.id + "Error").text("").addClass("d-none");
            },
            submitHandler: function (form) {
                let rentalData = new FormData(form);


                $("#rentalSettingForm input[type='checkbox']").each(function () {
                    rentalData.set($(this).attr("name"), $(this).is(":checked") ? "1" : "0");
                });



                $.ajax({
                    type: "POST",
                    url: "/admin/settings/rental/update",
                    data: rentalData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    },
                    beforeSend: function () {
                        $('.submitBtn').attr('disabled', true).html(`
                            <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}..
                        `);
                    },
                    complete: function () {
                        $('.submitBtn').attr('disabled', false).html(_l('admin.common.save_changes'));
                    },
                    success: function (resp) {
                        if (resp.code === 200) {
                            loadRentalSettings();
                            showToast("success", resp.message);

                        }
                    },
                    error: function (error) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");

                        if (error.responseJSON.code === 422) {
                            $.each(error.responseJSON.errors, function (key, val) {
                                $("#" + key).addClass("is-invalid");
                                $("#" + key + "Error").text(val[0]).removeClass("d-none");
                            });
                        } else {
                            showToast("error", error.responseJSON.message);
                        }


                    }
                });
            }
        });

        loadRentalSettings();

        function loadRentalSettings() {
            $.ajax({
                url: '/admin/settings/company/list',
                type: "POST",
                data: { group_id: 20 },
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                success: function (response) {
                    if (response.code === 200) {
                        const settings = response.data;

                        settings.forEach(setting => {
                            const element = $("#" + setting.key);

                            if (element.length) {
                                if (element.is(":checkbox")) {
                                    element.prop("checked", setting.value == 1);
                                } else if (element.is("select")) {
                                    const optionExists = element.find(`option[value="${setting.value}"]`).length > 0;

                                    if (!optionExists) {
                                        element.append(`<option value="${setting.value}">${setting.value}</option>`);
                                    }

                                    element.val(setting.value).trigger("change");
                                } else {
                                    element.val(setting.value);
                                }
                            }
                        });
                    }
                },
                error: function (xhr) {
                    showToast('error', _l('admin.common.default_retrieve_error'));
                },
                complete: function () {
                    $(".label-loader, .input-loader").hide();
                    $(".real-label, .real-input").removeClass("d-none");
                }
            });
        }
    });


})();


