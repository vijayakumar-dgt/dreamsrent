$(document).ready(function () {
    $(document).on("submit", "#config_form", async function (e) {
        e.preventDefault();

        toastr.clear(); // Clear previous notifications

        const configAppName = $("#config_app_name").val().trim();
        const submitBtn = $("#submit_btn");
        const csrfToken = $('meta[name="csrf-token"]').attr("content");

        // Input validation
        if (!configAppName) {
            toastr.warning("App Name is required");
            $("#config_app_name").focus();
            return;
        }

        // Show loading state
        submitBtn
            .html(
                'Saving... <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
            )
            .prop("disabled", true);

        try {
            const response = await $.ajax({
                url: '/setup/configuration-submit',
                method: "POST",
                data: {
                    config_app_name: configAppName,
                    _token: csrfToken,
                },
                dataType: "json",
            });

            if (response.success) {
                toastr.success(response.message);
                submitBtn.addClass("btn-success").html("Redirecting...");
                setTimeout(() => {
                    window.location.href = "/setup/complete";
                }, 1500);
            } else {
                toastr.error(response.message || "Something went wrong");
                submitBtn.prop("disabled", false).html("Save Config");
            }
        } catch (error) {
            console.error("AJAX error", error);
            submitBtn.prop("disabled", false).html("Save Config");

            if (error.responseJSON?.errors) {
                $.each(error.responseJSON.errors, function (key, messages) {
                    toastr.error(messages[0]); // Show first error per field
                });
            } else {
                toastr.error("Unexpected error. Please try again.");
            }
        }
    });
});
