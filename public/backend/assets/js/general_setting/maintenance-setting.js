(async () => {
    "use strict";
    await loadTranslationFile('admin', 'general_settings,common');

    $(document).ready(function() {
        $("#maintenanceSettingsForm").validate({
            rules: {
                maintenance_image: {
                    required: false,
                    accept: "image/*"
                },
                maintenance_description: {
                    required: true,
                    minlength: 10
                },
                maintenance_status: {
                    required: false
                }
            },
            messages: {
                maintenance_image: {
                    required: _l('admin.general_settings.please_upload_image'),
                    accept: _l('admin.general_settings.favicon_image_resolution'),
                },
                maintenance_description: {
                    required:_l('admin.general_settings.enter_description'),
                    minlength:_l('admin.general_settings.description_characters'),
                }
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
                var errorId = $(element).attr("id") + "_error";
                $("#" + errorId).text("");
            },
            onkeyup: function (element) {
                $(element).valid();
            },
            onchange: function (element) {
                $(element).valid();
            },
            submitHandler: function (form) {
                let maintenanceData = new FormData(form);

                let maintenanceStatus = $("#maintenance_status").prop("checked") ? 1 : 0;
                maintenanceData.set("maintenance_status", maintenanceStatus);



                $.ajax({
                    type: "POST",
                    url: "/admin/settings/maintenance/update",
                    data: maintenanceData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {
                        $('.btn-primary').attr('disabled', true).html(`
                            <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}..
                        `);
                    },
                    complete: function () {
                        $('.btn-primary').attr('disabled', false).html(_l('admin.common.save_changes'));
                    },
                    success: function (resp) {
                        if (resp.code === 200) {
                            loadMaintenanceSettings();
                            showToast('success', resp.message);

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
                            showToast('error', error.responseJSON.message);
                        }


                    }
                });
            }
        });

        loadMaintenanceSettings();

        function loadMaintenanceSettings() {
            $.ajax({
                url: '/admin/settings/company/list',
                type: 'POST',
                data: { 'group_id': 4 },
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.code === 200) {
                        const settings = response.data;

                        settings.forEach(setting => {
                            const element = $('#' + setting.key);

                            if (setting.key === 'maintenance_image' && setting.value) {
                                const imageUrl = `/storage/${setting.value}`;
                                $('#profile_photo_preview').attr('src', imageUrl).show();
                            }

                            else if (setting.key === 'maintenance_description') {
                                $('#maintenance_description').summernote('code', setting.value);
                            }

                            else if (setting.key === 'maintenance_status') {
                                element.prop('checked', setting.value == 1);
                            }

                            else if (element.length) {
                                element.val(setting.value);
                            }
                        });
                    }
                },
                error: function(xhr) {
                    showToast('error', _l('admin.common.default_retrieve_error'));
                },
                complete: function() {
                    $(".label-loader, .input-loader").hide();
                    $('.real-label, .real-input').removeClass('d-none');
                }
            });
        }
    });


})();


function previewImage(event) {
    const reader = new FileReader();
    const preview = document.getElementById('profile_photo_preview');

    reader.onload = function () {
        preview.src = reader.result;
    }

    if (event.target.files.length > 0) {
        reader.readAsDataURL(event.target.files[0]);
    }
}

function removeImage() {
    const preview = document.getElementById('profile_photo_preview');
    const fileInput = document.getElementById('profile_photo');

    preview.src = '/assets/img/settings/company-logo-01.jpg';
    fileInput.value = '';
}
