(async () => {
    "use strict";
    await loadTranslationFile('admin', 'general_settings,common');

    $(document).ready(function() {
        $("#seosetupSettingForm").validate({
            rules: {
                metaImage: {
                    required: false,
                    accept: "image/*"
                },
                metaTitle: {
                    required: true,
                    minlength: 5
                },
                siteDescription: {
                    required: true,
                    minlength: 10
                },
                keywords: {
                    required: true
                },
                ogmetaTitle: {
                    required: true,
                    minlength: 5
                },
                ogsiteDescription: {
                    required: true,
                    minlength: 10
                },
                ogkeywords: {
                    required: true
                }
            },
            messages: {
                metaImage: {
                    required: _l('admin.general_settings.upload_meta_image'),
                    accept: _l('admin.general_settings.favicon_image_resolution'),
                },
                metaTitle: {
                    required: _l('admin.general_settings.favicon_image_resolution'),
                    minlength: _l('admin.general_settings.meta_title_characters'),
                },
                siteDescription: {
                    required:  _l('admin.general_settings.site_description'),
                    minlength: _l('admin.general_settings.description_characters'),
                },
                keywords: {
                    required: _l('admin.general_settings.enter_least_keyword'),
                },
                ogmetaTitle: {
                    required:  _l('admin.general_settings.enter_og_metatitle'),
                    minlength: _l('admin.general_settings.description_characters'),
                },
                ogsiteDescription: {
                    required: _l('admin.general_settings.enter_og_site_description'),
                    minlength:_l('admin.general_settings.og_description_characters'),
                },
                ogkeywords: {
                    required: _l('admin.general_settings.enter_one_og'),
                }
            },
            errorPlacement: function(error, element) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            },
            highlight: function(element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function(element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
                var errorId = element.id + "_error";
                $("#" + errorId).text("");
            },
            onkeyup: function(element) {
                $(element).valid();
            },
            onchange: function(element) {
                $(element).valid();
            },
            submitHandler: function(form) {
                let seoData = new FormData(form);

                $.ajax({
                    type: "POST",
                    url: "/admin/settings/seosetup/store",
                    data: seoData,
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
                    success: function(resp) {
                        if (resp.code === 200) {
                            loadSeoSettings();
                            showToast('success', resp.message);

                        }
                    },
                    error: function(error) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");

                        if (error.responseJSON.code === 422) {
                            $.each(error.responseJSON.errors, function(key, val) {
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

        loadSeoSettings();

        function loadSeoSettings() {
            $.ajax({
                url: '/admin/settings/company/list',
                type: 'POST',
                data: { 'group_id': 6 },
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.code === 200) {
                        const settings = response.data;

                        settings.forEach(setting => {
                            const element = $('#' + setting.key);

                            if (setting.key === 'metaImage' && setting.value) {
                                $('#profile_photo_preview').attr('src', setting.value).show();
                            }

                            else if (setting.key === 'siteDescription') {
                                $('#siteDescription').val(setting.value);
                            }
                            else if (setting.key === 'keywords') {
                                $('#keywords').tagsinput('removeAll');
                                const keywordsArray = setting.value.split(',');
                                keywordsArray.forEach(keyword => {
                                    $('#keywords').tagsinput('add', keyword.trim());
                                });
                            }


                            else if (setting.key === 'OGmetaTitle') {
                                $('#ogmetaTitle').val(setting.value);
                            }
                            else if (setting.key === 'OGsiteDescription') {
                                $('#ogsiteDescription').val(setting.value);
                            }
                            else if (setting.key === 'ogkeywords') {
                                $('#ogkeywords').tagsinput('removeAll');
                                const ogKeywordsArray = setting.value.split(',');
                                ogKeywordsArray.forEach(keyword => {
                                    $('#ogkeywords').tagsinput('add', keyword.trim());
                                });
                            }

                            else if (element.length) {
                                element.val(setting.value);
                            }
                        });

                        $('.real-label').removeClass('d-none');
                        $('.label-loader, .input-loader, .image-loader').addClass('d-none');
                    }
                },
                error: function(xhr) {
                    showToast('error', xhr.responseJSON.message);
                },
                complete: function() {
                    $(".label-loader, .input-loader, .card-loader").hide();
                    $('.real-label, .real-input, .real-card').removeClass('d-none');
                }
            });
        }
    });

})();




function previewImage(event) {
    const file = event.target.files[0];
    const reader = new FileReader();
    const preview = $("#profile_photo_preview");

    if (file) {
        if (file.size > 5 * 1024 * 1024) {
            showToast('error', _l('admin.general_settings.image_size_5mb'));
            $("#metaImage").val("");
            return;
        }

        reader.onload = function (e) {
            const img = new Image();
            img.src = e.target.result;

            img.onload = function () {
                if (img.width === 500 && img.height === 500) {
                    preview.attr("src", e.target.result).show();
                    $(".frames").removeClass("d-none");
                } else {
                    showToast('error', _l('admin.general_settings.image_dimension'));
                    $("#metaImage").val("");
                }
            };
        };

        reader.readAsDataURL(file);
    }
}

function removeImage() {
    const preview = document.getElementById('profile_photo_preview');
    const fileInput = document.getElementById('profile_photo');

    preview.src = '/backend/assets/img/settings/company-logo-01.jpg';
    fileInput.value = '';
}
