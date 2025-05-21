(function () {
    "use strict";

    // Toggle switches (optional if using custom UI toggles)
    $("#reset_database").bootstrapToggle({
        on: "Yes",
        off: "No",
        onstyle: "danger",
        offstyle: "secondary",
        size: "sm",
    });

    $("#fresh_install").bootstrapToggle({
        on: "Fresh Install",
        off: "With Dummy Data",
        onstyle: "success",
        offstyle: "warning",
        size: "lg",
    });
    $(document).ready(function () {
        $(document).on("submit", "#database_migrate_form", async function (e) {
            e.preventDefault();

            toastr.clear();

            const submitBtn = $("#submit_btn");
            const host = $("#host").val().trim();
            const port = $("#port").val().trim();
            const database = $("#database").val().trim();
            const username = $("#user").val().trim();
            const password = $("#password").val();
            const csrfToken = $('meta[name="csrf-token"]').attr("content");

            const freshInstall = $("#fresh_install").is(":checked");
            const resetDatabase = $("#reset_database").is(":checked");

            // Input validation
            if (!port) {
                toastr.warning("Port is required");
                $("#port").focus();
                return;
            }

            if (!database) {
                toastr.warning("Database Name is required");
                $("#database").focus();
                return;
            }

            if (!username) {
                toastr.warning("Username is required");
                $("#user").focus();
                return;
            }

            // Show loading state
            submitBtn
                .html(
                    'Migrating... <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                )
                .prop("disabled", true);

            try {
                const response = await $.ajax({
                    url: "/setup/database-submit",
                    method: "POST",
                    dataType: "json",
                    data: {
                        host,
                        port,
                        database,
                        user: username,
                        password,
                        _token: csrfToken,
                        ...(freshInstall && { fresh_install: 1 }),
                        ...(resetDatabase && { reset_database: 1 }),
                    },
                });

                $("#reset_database").bootstrapToggle("off");
                $("#reset_database_switcher").addClass("d-none");

                if (response.success) {
                    toastr.success(response.message);
                    submitBtn.addClass("btn-success").html("Redirecting...");
                    setTimeout(() => {
                        window.location.href = "/setup/account";
                    }, 1500);
                } else if (response.create_database) {
                    toastr.error(response.message);
                    submitBtn.prop("disabled", false).html("Setup Database");
                } else if (response.reset_database) {
                    $("#reset_database_switcher").removeClass("d-none");
                    toastr.error(response.message);
                    submitBtn.prop("disabled", false).html("Setup Database");
                } else {
                    toastr.error(response.message || "Something went wrong");
                    submitBtn.prop("disabled", false).html("Setup Database");
                }
            } catch (error) {
                console.error("AJAX error", error);
                submitBtn.prop("disabled", false).html("Setup Database");

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
})();
