"use strict";

// Toggle switches (optional if using custom UI toggles)
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

$(document).ready(function () {
    // Get the form and route URLs from data attributes
    const form = $("#account_form");
    const submitRoute = form.data('submit-route') || "{{ route('setup.account.submit') }}";
   
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    form.on("submit", async function (e) {
        e.preventDefault();
        
        // Clear previous errors
        toastr.clear();
        
        // Get form values
        const name = $("#name").val().trim();
        const email = $("#email").val().trim();
        const password = $("#password").val().trim();
        const confirm_password = $("#confirm_password").val().trim();
        const submitBtn = $("#submit_btn");

        // Validate inputs
        if (!name) {
            toastr.warning("Name is required");
            $("#name").focus();
            return;
        }

        if (!email) {
            toastr.warning("Email is required");
            $("#email").focus();
            return;
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            toastr.warning("Invalid email format");
            $("#email").focus();
            return;
        }

        if (!password) {
            toastr.warning("Password is required");
            $("#password").focus();
            return;
        }

        if (password.length < 8) {
            toastr.warning("Password must be at least 8 characters");
            $("#password").focus();
            return;
        }

        if (password !== confirm_password) {
            toastr.warning("Password & Confirm Password must match");
            $("#confirm_password").focus();
            return;
        }

        // Set loading state
        submitBtn.html(
            'Creating... <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
        ).prop("disabled", true);

        try {
            const response = await $.ajax({
                url: submitRoute,
                method: 'POST',
                data: {
                    name: name,
                    email: email,
                    password: password,
                    password_confirmation: confirm_password,
                    _token: csrfToken
                },
                dataType: 'json'
            });

            if (response.success) {
                toastr.success(response.message);
                submitBtn.addClass("btn-success").html("Redirecting...");
                
                // Small delay before redirect for better UX
                setTimeout(() => {
                    window.location.href = '/setup/account';
                }, 1500);
            } else {
                toastr.error(response.message);
            }
        } catch (error) {
            console.error("Account creation error:", error);
            
            if (error.responseJSON) {
                // Handle server validation errors
                if (error.responseJSON.errors) {
                    $.each(error.responseJSON.errors, function (key, value) {
                        toastr.error(value[0]); // Show first error for each field
                    });
                } else if (error.responseJSON.message) {
                    toastr.error(error.responseJSON.message);
                }
            } else {
                toastr.error("An unexpected error occurred. Please try again.");
            }
        } finally {
            // Always reset button state
            submitBtn.html("Create Account").prop("disabled", false).removeClass("btn-success");
        }
    });
});
