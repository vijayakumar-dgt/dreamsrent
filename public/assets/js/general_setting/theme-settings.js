(async () => {
    await loadTranslationFile('admin', 'general_settings,common');

$(document).ready(function() {
    loadThemeSettings();
});

function loadThemeSettings() {
    $.ajax({
        url: '/admin/settings/list',
        type: 'POST',
        data: { 'group_id': 16 },
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        
        success: function(response) {
            if (response.code === 200) {
                const settings = response.data;
                settings.forEach(setting => {
                    if (setting.value == 1) {
                        $('#theme_01').prop('checked', true);
                    } else if (setting.value == 2) {
                        $('#theme_02').prop('checked', true);
                    }
                });
            }
        },
        error: function(error) {
            if (error.responseJSON.code === 500) {
                showToast('error', error.responseJSON.message);
            } else {
                showToast('error', _l('admin.general_settings.retrive_error'));
            }
        },
        complete: function() {
            $(".label-loader, .input-loader, .card-loader").hide();
            $('.real-label, .real-input, .real-card').removeClass('d-none');
        }
    });
}

$(document).on('click', '.default_theme, .theme-img', function () {
    let themeId = $(this).data('id');
    $('#' + themeId).prop('checked', true);

    let theme_val = 1;

    if (themeId == 'theme_01') {
        theme_val = 1;
    } else if (themeId == 'theme_02') {
        theme_val = 2;
    }

    let formData = new FormData();
    formData.append('group_id', 16);
    formData.append('default_theme', theme_val);

    $.ajax({
        type: "POST",
        url: "/admin/settings/update-theme-settings",
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(resp) {
            if (resp.code === 200) {
                showToast(_l('admin.general_settings.success'), resp.message);
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
                showToast(_l('admin.general_settings.retrive_error'), error.responseJSON.message);

            }
        }
    });

});

})();