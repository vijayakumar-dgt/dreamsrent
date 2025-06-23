/* global location, loadTranslationFile, jQuery, setTimeout, document, showToast */
(($) => {
    "use strict";

    (async () => {
        await loadTranslationFile("web", "home, common");

        let isInitialLoad = true;

        $(document).ready(() => {
            $('.custom-select2').select2();
            fetchPreference();
        });

        $(document).on('change', '#language_id', function () {
            if (isInitialLoad) return;
            updatePreference({ language_id: $(this).val() });
        });

        $(document).on('change', '#region_id', function () {
            if (isInitialLoad) return;
            updatePreference({ region_id: $(this).val() });
        });

        const fetchPreference = async () => {
            try {
                const resp = await $.ajax({
                    type: "POST",
                    url: "/user/get-preferences",
                    dataType: "json",
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                if (resp.code === 200) {
                    $("#language_id").val(resp.data.language_id).trigger('change');
                    $("#region_id").val(resp.data.region_id).trigger('change');
                    isInitialLoad = false;
                }
            } catch (error) {
                showToast("error", `Error fetching preferences: ${error.message}`);
            }
        };

        const updatePreference = async (formData = {}) => {
            try {
                const resp = await $.ajax({
                    type: "POST",
                    url: "/user/preference/update",
                    data: formData,
                    dataType: "json",
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $(".error-text").text("");
                $(".form-control").removeClass("is-invalid is-valid");

                if (resp.code === 200) {
                    showToast('success', resp.message);
                    setTimeout(() => {
                        location.reload();
                    },3000);
                }
            } catch (error) {
                $(".error-text").text("");
                $(".form-control").removeClass("is-invalid is-valid");

                if (error.responseJSON?.code === 422) {
                    Object.entries(error.responseJSON.errors).forEach(([key, val]) => {
                        $(`#${key}`).addClass("is-invalid");
                        $(`#${key}_error`).text(val[0]);
                    });
                } else {
                    showToast('error', error.responseJSON?.message || "An error occurred");
                }
            }
        };

    })();
})(jQuery);
