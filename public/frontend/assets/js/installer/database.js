/* global $, document, toastr, setTimeout, window */

(function () {
    "use strict";

    $(function () {
        // Initialize Bootstrap toggle switches safely
        if ($.fn.bootstrapToggle) {
            $("#reset_database").bootstrapToggle({
                on: "Yes",
                off: "No",
                onstyle: "danger",
                offstyle: "secondary",
                size: "sm"
            });

            $("#fresh_install").bootstrapToggle({
                on: "Fresh Install",
                off: "With Dummy Data",
                onstyle: "success",
                offstyle: "warning",
                size: "lg"
            });
        }

        // Handle database migration form submission
        $(document).on("submit", "#database_migrate_form", async function (e) {
            e.preventDefault();
            toastr.clear();

            const $submitBtn = $("#submit_btn");
            const csrfToken = $("meta[name='csrf-token']").attr("content");

            const host = $("#host").val().trim();
            const port = $("#port").val().trim();
            const database = $("#database").val().trim();
            const username = $("#user").val().trim();
            const dbPass = $("#password").val();
            const freshInstall = $("#fresh_install").is(":checked");
            const resetDatabase = $("#reset_database").is(":checked");

            // Input validations
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

            // Show loading spinner
            $submitBtn
                .html(
                    "Migrating... <span class=\"spinner-border spinner-border-sm\" role=\"status\" aria-hidden=\"true\"></span>"
                )
                .prop("disabled", true);

            try {
                const response = await $.ajax({
                    url: "/setup/database-submit",
                    method: "POST",
                    dataType: "json",
                    data: {
                        host: host,
                        port: port,
                        database: database,
                        user: username,
                        db_pass: dbPass,
                        _token: csrfToken,
                        ...(freshInstall ? { fresh_install: 1 } : {}),
                        ...(resetDatabase ? { reset_database: 1 } : {})
                    }
                });

                // Toggle reset off and hide the switcher
                $("#reset_database").bootstrapToggle("off");
                $("#reset_database_switcher").addClass("d-none");

                if (response.success) {
                    toastr.success(response.message);
                    $submitBtn.addClass("btn-success").html("Redirecting...");
                    setTimeout(function () {
                        window.location.href = "/setup/account";
                    }, 1500);
                    return;
                }

                if (response.create_database || response.reset_database) {
                    if (response.reset_database) {
                        $("#reset_database_switcher").removeClass("d-none");
                    }
                    toastr.error(response.message || "Database operation failed");
                    $submitBtn.prop("disabled", false).html("Setup Database");
                    return;
                }

                toastr.error(response.message || "Something went wrong");
                $submitBtn.prop("disabled", false).html("Setup Database");
            } catch (err) {
                $submitBtn.prop("disabled", false).html("Setup Database");

                if (err.responseJSON && err.responseJSON.errors) {
                    $.each(err.responseJSON.errors, function (key, messages) {
                        if (Array.isArray(messages) && messages.length > 0) {
                            toastr.error(messages[0]);
                        }
                    });
                } else {
                    toastr.error("Unexpected error. Please try again.");
                }
            }
        });
    });
})();