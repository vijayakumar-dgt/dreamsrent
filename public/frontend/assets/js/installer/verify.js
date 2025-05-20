$(document).ready(function () {
    $(document).on("submit", "#verify_form", async function (e) {
    e.preventDefault();

    const code = $("#purchase_code").val().trim();
    const submitBtn = $("#submit_btn");
    const form = $(this);

    toastr.clear();

    if (!code) {
        toastr.warning("Purchase code is required");
        return;
    }

    submitBtn.html(
        'Checking... <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
    ).prop("disabled", true);

    try {
        const response = await $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: {
                purchase_code: code,
               
            },
            dataType: 'json'
        });

        if (response.success) {
            toastr.success(response.message);
            submitBtn.addClass("btn-success").html("Redirecting...");
            setTimeout(() => {
                window.location.href = '/setup/requirements';
            }, 1500);
        } else {
            $("#purchase_code").val("");
            toastr.error(response.message);
            setTimeout(() => {
                window.location.reload();
            }, 4000);
        }
    } catch (error) {
        console.error("Verification error:", error);

        if (error.responseJSON) {
            if (error.responseJSON.errors) {
                $.each(error.responseJSON.errors, function (key, value) {
                    toastr.error(value);
                });
            } else if (error.responseJSON.message) {
                toastr.error(error.responseJSON.message);
            }
        } else {
            toastr.error("An unexpected error occurred. Please try again.");
        }

        $("#purchase_code").val("");
    } finally {
        submitBtn.html("Check").prop("disabled", false).removeClass("btn-success");
    }
});
});
