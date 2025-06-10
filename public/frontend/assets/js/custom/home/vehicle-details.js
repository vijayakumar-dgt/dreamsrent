(async () => {
    "use strict";
    await loadTranslationFile("web", "user,common,home");

    let _pricing_type;
    let _vehicleDetails;

    $(document).ready(function () {
        fetchVehicleDetails();
        fetchRecommendedVehicles();
        listReviews();

        $("#enquiryForm").validate({
            rules: {
                enquiry_name: {
                    required: true,
                    minlength: 3,
                    maxlength: 30,
                },
                enquiry_email: {
                    required: true,
                    email: true,
                    maxlength: 50,
                },
                enquiry_phone: {
                    required: true,
                    minlength: 10,
                    maxlength: 15,
                },
                enquiry_message: {
                    required: true,
                    minlength: 3,
                },
                terms: {
                    required: true,
                },
            },
            messages: {
                enquiry_name: {
                    required: _l("web.home.name_required"),
                    minlength: _l("web.common.minlength_3"),
                    maxlength: _l("web.common.maxlength_30"),
                },
                enquiry_email: {
                    required: _l("web.home.email_required"),
                    email: _l("web.home.valid_email"),
                    maxlength: _l("web.home.email_max_length"),
                },
                enquiry_phone: {
                    required: _l("web.home.phone_number_required"),
                    minlength: _l("web.home.phone_number_minlength"),
                    maxlength: _l("web.home.phone_number_maxlength"),
                },
                enquiry_message: {
                    required: _l("web.home.message_required"),
                    minlength: _l("web.home.message_minlength"),
                },
                terms: {
                    required: _l("web.home.terms_required"),
                },
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
                var errorId = element.id + "_error";
                $("#" + errorId).text("");
            },
            onkeyup: function (element) {
                $(element).valid();
            },
            onchange: function (element) {
                $(element).valid();
            },
            submitHandler: function (form) {
                let enquiryFormData = new FormData(form);
                $("#enquiryForm .submitbtn").html(
                    `<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l(
                        "web.common.saving"
                    )}..`
                );
                $("#enquiryForm .submitbtn").attr("disabled", true);
                $.ajax({
                    type: "POST",
                    url: "/user/store_enquiry",
                    data: enquiryFormData,
                    processData: false,
                    contentType: false,
                    success: function (resp) {
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            $("#enquiry").modal("hide");
                        }
                        $("#enquiryForm")[0].reset();
                        $("#enquiry .submitbtn").text(_l("web.common.submit"));
                        $("#enquiry .submitbtn").prop("disabled", false);
                    },
                    error: function (error) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        if (error.responseJSON.code === 422) {
                            $.each(
                                error.responseJSON.errors,
                                function (key, val) {
                                    $("#" + key).addClass("is-invalid");
                                    $("#" + key + "_error").text(val[0]);
                                }
                            );
                        } else {
                            showToast("error", error.responseJSON.message);
                        }
                        $("#enquiry .submitbtn").text(_l("web.common.submit"));
                        $("#enquiry .submitbtn").prop("disabled", false);
                    },
                });
            },
        });

        function toggleContainer() {
            if ($("#location_delivery").is(":checked")) {
                $("#devliveryCOntainer").show();
                $("#selfCOntainer").hide();
                $("#rent_value").val("delivery");
            } else {
                $("#devliveryCOntainer").hide();
                $("#selfCOntainer").show();
                $("#rent_value").val("self_pickup");
            }
        }

        // Attach event listeners
        $("input[name='rent_type']").on("change", toggleContainer);

        // Initial state
        toggleContainer();

        // Initialize Select2
        $("#delivery_location, #delivery_return_location").select2();
        $("#pickup_location, #pickup_return_location").select2();

        $("#delivery_remeber").change(function () {
            if ($(this).is(":checked")) {
                let locationValue = $("#delivery_location").val();
                $("#delivery_return_location")
                    .val(locationValue)
                    .trigger("change"); // Update Select2 correctly
            } else {
                $("#delivery_return_location").val("").trigger("change"); // Clear Select2 selection
            }
        });

        // Listen for Select2 change event
        $("#delivery_location").on("change", function () {
            if ($("#delivery_remeber").is(":checked")) {
                $("#delivery_return_location")
                    .val($(this).val())
                    .trigger("change");
            }
        });

        $("#pickup_remeber").change(function () {
            if ($(this).is(":checked")) {
                let locationValue = $("#pickup_location").val();
                $("#pickup_return_location")
                    .val(locationValue)
                    .trigger("change"); // Update Select2 correctly
            } else {
                $("#pickup_return_location").val("").trigger("change"); // Clear Select2 selection
            }
        });

        // Listen for Select2 change event
        $("#pickup_location").on("change", function () {
            if ($("#pickup_remeber").is(":checked")) {
                $("#pickup_return_location")
                    .val($(this).val())
                    .trigger("change");
            }
        });

        function calculatePrice() {
            let selectedPriceType = $("input[name='price_rate']:checked").data(
                "price-type"
            );
            let dailyPrice =
                parseFloat($("input[name='price_rate']:checked").val()) || 0; // Get daily price

            let pickupDate = $("#pickup_date").val();
            let pickupTime = $("#pickup_time").val();
            let returnDate = $("#return_date").val();
            let returnTime = $("#return_time").val();

            if (!pickupDate || !pickupTime || !returnDate || !returnTime) {
                return; // Exit if any value is missing
            }

            let pickupDateTime = moment(
                pickupDate + " " + pickupTime,
                "DD/MM/YYYY HH:mm"
            );
            let returnDateTime = moment(
                returnDate + " " + returnTime,
                "DD/MM/YYYY HH:mm"
            );

            if (selectedPriceType === "daily") {
                $(
                    "#pickup_date, #pickup_time, #return_date, #return_time"
                ).prop("readonly", false);

                let durationDays = Math.ceil(
                    moment
                        .duration(returnDateTime.diff(pickupDateTime))
                        .asDays()
                );
                durationDays = durationDays <= 0 ? 1 : durationDays; // Minimum 1 day

                let finalPrice = dailyPrice * durationDays;
                $("#final_price_rate").val(finalPrice.toFixed(2)); // Update price
            } else {
                $("#return_date, #return_time").prop("readonly", true);

                $("#pickup_date, #pickup_time").prop("readonly", false);

                switch (selectedPriceType) {
                    case "weekly":
                        returnDateTime = pickupDateTime.add(7, "days");
                        break;
                    case "monthly":
                        returnDateTime = pickupDateTime.add(1, "months");
                        break;
                    case "yearly":
                        returnDateTime = pickupDateTime.add(1, "years");
                        break;
                    default:
                        return;
                }
                if (_pricing_type != "daily") {
                    $("#return_date").val(returnDateTime.format("DD-MM-YYYY"));
                    $("#return_time").val(returnDateTime.format("HH:mm"));
                }
                $("#final_price_rate").val(dailyPrice.toFixed(2)); // Update price
            }
        }

        // Trigger calculation on price type change
        $("input[name='price_rate']").on("change", calculatePrice);

        // Trigger calculation on date/time change
        $("#pickup_date, #pickup_time, #return_date, #return_time").on(
            "blur",
            calculatePrice
        );

        $("#validate_btn").on("click", function (e) {
            e.preventDefault(); // Prevent default form submission

            let errors = [];

            // Validate price type
            let selectedPriceType = $("input[name='price_rate']:checked").data(
                "price-type"
            );
            if (!selectedPriceType) {
                errors.push(_l("web.home.select_price_type"));
            }

            // Validate pickup/return locations based on rent type
            let selectedRentType = $("input[name='rent_type']:checked").attr(
                "id"
            );
            let pickupLocation = "",
                returnLocation = "";

            if (selectedRentType === "location_delivery") {
                pickupLocation = $("#delivery_location").val();
                returnLocation = $("#delivery_return_location").val();
            } else if (selectedRentType === "location_pickup") {
                pickupLocation = $("#pickup_location").val();
                returnLocation = $("#pickup_return_location").val();
            }

            if (!pickupLocation || !returnLocation) {
                errors.push(_l("web.home.enter_pickup_and_return_locations"));
            }

            // Validate date/time for all price types
            let pickupDate = $("#pickup_date").val();
            let pickupTime = $("#pickup_time").val();
            let returnDate = $("#return_date").val();
            let returnTime = $("#return_time").val();

            let pickupDateTime, returnDateTime;

            if (!pickupDate || !pickupTime || !returnDate || !returnTime) {
                errors.push(_l("web.home.all_date_time_fields_are_required"));
            } else {
                pickupDateTime = moment(
                    pickupDate + " " + pickupTime,
                    "DD-MM-YYYY HH:mm"
                );
                returnDateTime = moment(
                    returnDate + " " + returnTime,
                    "DD-MM-YYYY HH:mm"
                );

                if (!pickupDateTime.isValid() || !returnDateTime.isValid()) {
                    errors.push(_l("web.home.invalid_date_or_time_format"));
                } else {
                    if (returnDateTime.isBefore(pickupDateTime, "minute")) {
                        errors.push(
                            _l(
                                "web.home.return_date_cannot_be_before_pickup_date"
                            )
                        );
                    }

                    if (
                        selectedPriceType === "daily" &&
                        returnDateTime.diff(pickupDateTime, "hours") < 2
                    ) {
                        errors.push(_l("web.home.duration_must_be_2_hours"));
                    }
                }
            }

            // Show errors if any
            if (errors.length > 0) {
                errors.forEach((error) => showToast("error", error));
                return;
            }

            // Format datetime (set seconds as 00)
            let formattedPickup = pickupDateTime.format("YYYY-MM-DD HH:mm:00");
            let formattedReturn = returnDateTime.format("YYYY-MM-DD HH:mm:00");
            let vehicleId = $("#vehicle_id").val();

            $("#validate_btn")
                .text(_l("web.home.checking_availability"))
                .prop("disabled", true);

            // AJAX call to check booking availability
            $.ajax({
                url: "/api/check-booking",
                type: "POST",
                data: {
                    start_datetime: formattedPickup,
                    end_datetime: formattedReturn,
                    vehicle_id: vehicleId,
                },
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    if (response.status === "success") {
                        $("#validateVehicleBook").submit();
                    } else {
                        showToast(
                            "error",
                            response.message ||
                                _l(
                                    "web.home.vehicle_not_available_for_selected_time"
                                )
                        );
                        $("#validate_btn").text("Book").prop("disabled", false);
                    }
                },
                error: function () {
                    showToast("error", _l("web.common.default_error"));
                    $("#validate_btn").text("Book").prop("disabled", false);
                },
            });
        });

        const $userPhoneInput = $("#enquiry_phone");
        const $intlPhoneInput = $("#international_phone_number");
        const $userProfileForm = $("#enquiryForm");

        if ($userPhoneInput.length && $userProfileForm.length) {
            const iti = window.intlTelInput($userPhoneInput[0], {
                utilsScript: `${window.location.origin}/backend/assets/plugins/intltelinput/js/utils.js`,
                separateDialCode: true,
            });

            $userPhoneInput.addClass("iti");
            $userPhoneInput.parent().addClass("intl-tel-input");

            $userPhoneInput.on("keyup", function () {
                $intlPhoneInput.val(iti.getNumber());
            });
        }
    });

    function fetchRecommendedVehicles() {
        $.ajax({
            url: "/vehicle-intrset-list",
            type: "POST",
            data: {
                category_id: $("#category_id").val(),
            },
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.code === 200) {
                    let cleanHtml = DOMPurify.sanitize(response.html);
                    $("#recommended-vehicle").html(cleanHtml);
                    setTimeout(function () {
                        reInitializeCarousel(".rental-deal-slider");
                    }, 150);
                }
            },
        });
    }

    if ($(".bookingpickupdate").length > 0) {
        $(".bookingpickupdate")
            .datetimepicker({
                format: "DD-MM-YYYY",
                useCurrent: false,
                minDate: moment().startOf("day"),
                icons: {
                    up: "fas fa-angle-up",
                    down: "fas fa-angle-down",
                    next: "fas fa-angle-right",
                    previous: "fas fa-angle-left",
                },
            })
            .on("dp.change", function (e) {
                const pickupDate = e.date;
                const returnDate = $(".bookingreturndate")
                    .data("DateTimePicker")
                    .date();

                if (pickupDate && pickupDate.isSame(moment(), "day")) {
                    $(".booking_timepicker")
                        .data("DateTimePicker")
                        .minDate(moment());
                } else {
                    $(".booking_timepicker")
                        .data("DateTimePicker")
                        .minDate(false);
                }

                if (pickupDate) {
                    const pickupOnly = pickupDate.clone().startOf("day");
                    if (_pricing_type == "daily") {
                        $(".bookingreturndate")
                            .data("DateTimePicker")
                            .date(null);
                    }
                    $(".bookingreturndate")
                        .data("DateTimePicker")
                        .minDate(pickupOnly);
                }

                if (returnDate) {
                    const returnOnly = returnDate.clone().startOf("day");
                    // $(".bookingpickupdate").data("DateTimePicker").maxDate(returnOnly);
                } else {
                    $(".bookingpickupdate")
                        .data("DateTimePicker")
                        .maxDate(false);
                }
            });
    }

    if ($(".booking_timepicker").length > 0) {
        $(".booking_timepicker")
            .datetimepicker({
                format: "HH:mm",
                useCurrent: true,
                icons: {
                    up: "fas fa-angle-up",
                    down: "fas fa-angle-down",
                    next: "fas fa-angle-right",
                    previous: "fas fa-angle-left",
                },
                stepping: 15, // Set interval to 15 minutes
            })
            .on("dp.change", function (e) {
                const pickupTime = e.date;
                const pickupDate = $(".bookingpickupdate")
                    .data("DateTimePicker")
                    .date();
                const returnDate = $(".bookingreturndate")
                    .data("DateTimePicker")
                    .date();
                const returnTimePicker = $(".booking_return_timepicker").data(
                    "DateTimePicker"
                );
                const returnTime = returnTimePicker.date();

                if (!pickupDate || !returnDate || !pickupTime) return;

                const isSameDay = pickupDate.isSame(returnDate, "day");
                const minReturnTime = moment(pickupTime).add(1, "hour");

                if (isSameDay) {
                    returnTimePicker.minDate(minReturnTime);
                    if (!returnTime || returnTime.isBefore(minReturnTime)) {
                        returnTimePicker.date(minReturnTime);
                    }
                } else {
                    returnTimePicker.minDate(false);
                }
            });
    }

    if ($(".bookingreturndate").length > 0) {
        $(".bookingreturndate")
            .datetimepicker({
                format: "DD-MM-YYYY",
                useCurrent: false,
                minDate: moment().startOf("day"),
                icons: {
                    up: "fas fa-angle-up",
                    down: "fas fa-angle-down",
                    next: "fas fa-angle-right",
                    previous: "fas fa-angle-left",
                },
            })
            .on("dp.change", function (e) {
                const returnDate = e.date;
                const pickupDate = $(".bookingpickupdate")
                    .data("DateTimePicker")
                    .date();

                if (!pickupDate || !returnDate) return;

                const returnOnly = returnDate.clone().startOf("day");
                const pickupOnly = pickupDate.clone().startOf("day");

                // $(".bookingpickupdate").data("DateTimePicker").maxDate(returnOnly);
                $(".bookingreturndate")
                    .data("DateTimePicker")
                    .minDate(pickupOnly);

                if (returnOnly.isSame(pickupOnly, "day")) {
                    const pickupTime = $(".booking_timepicker")
                        .data("DateTimePicker")
                        .date();
                    if (pickupTime) {
                        const minReturnTime = moment(pickupTime).add(1, "hour");
                        $(".booking_return_timepicker")
                            .data("DateTimePicker")
                            .minDate(minReturnTime);
                    }
                } else if (returnOnly.isSame(moment(), "day")) {
                    $(".booking_return_timepicker")
                        .data("DateTimePicker")
                        .minDate(moment().add(1, "hour"));
                } else {
                    $(".booking_return_timepicker")
                        .data("DateTimePicker")
                        .minDate(false);
                }
            });
    }

    if ($(".booking_return_timepicker").length > 0) {
        $(".booking_return_timepicker")
            .datetimepicker({
                format: "HH:mm",
                useCurrent: true,
                icons: {
                    up: "fas fa-angle-up",
                    down: "fas fa-angle-down",
                    next: "fas fa-angle-right",
                    previous: "fas fa-angle-left",
                },
                stepping: 15, // 15-minute interval
            })
            .on("dp.change", function (e) {
                const returnTime = e.date;
                const pickupTime = $(".booking_timepicker")
                    .data("DateTimePicker")
                    .date();
                const pickupDate = $(".bookingpickupdate")
                    .data("DateTimePicker")
                    .date();
                const returnDate = $(".bookingreturndate")
                    .data("DateTimePicker")
                    .date();

                if (
                    pickupDate &&
                    returnDate &&
                    pickupTime &&
                    returnTime &&
                    pickupDate.isSame(returnDate, "day")
                ) {
                    if (
                        !returnTime.isAfter(
                            moment(pickupTime).add(59, "minutes")
                        )
                    ) {
                        $(this).data("DateTimePicker").date(null);
                        alert(
                            "Return time must be at least 1 hour after pickup time."
                        );
                    }
                }
            });
    }

    function listReviews() {
        $.ajax({
            url: "/get-reviews",
            type: "POST",
            data: {
                vehicle_id: $("#vehicle_id").val(),
            },
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.code === 200) {
                    if (response.data) {
                        $("#reviews_list_container").empty();
                        let reviews_meta = response.data.reviews_meta;
                        let reviews = response.data.reviews;
                        renderReviewsMeta(reviews_meta);
                        renderReviews(reviews);
                    }
                }
            },
            error: function (res) {
                if (res.responseJSON.code === 500) {
                    showToast("success", res.responseJSON.message);
                } else {
                    showToast("error", _l("web.common.default_retrieve_error"));
                }
            },
        });
    }

    function renderReviewsMeta(reviews_meta) {
        let cleanDescription = DOMPurify.sanitize(
            reviews_meta.overall_avg_ratings
        );
        $("#overall_ratings")
            .empty()
            .append(`${cleanDescription}<span>/5</span>`);
        $("#rating_description").text(reviews_meta.rating_description);
        let totalReview = DOMPurify.sanitize(reviews_meta.total_reviews);
        $("#total_reviews")
            .empty()
            .append(
                `${_l("web.home.based_on")} ${totalReview} ${_l(
                    "web.common.reviews"
                )}`
            );

        $("#service_progress").attr(
            "style",
            `width: ${reviews_meta.service_ratings_percentage}`
        );
        $("#location_progress").attr(
            "style",
            `width: ${reviews_meta.location_ratings_percentage}`
        );
        $("#facility_progress").attr(
            "style",
            `width: ${reviews_meta.facility_ratings_percentage}`
        );
        $("#value_for_money_progress").attr(
            "style",
            `width: ${reviews_meta.value_for_money_ratings_percentage}`
        );
        $("#cleanliness_progress").attr(
            "style",
            `width: ${reviews_meta.cleanliness_ratings_percentage}`
        );

        $("#avg_service_ratings").text(reviews_meta.avg_service_ratings);
        $("#avg_location_ratings").text(reviews_meta.avg_location_ratings);
        $("#avg_facility_ratings").text(reviews_meta.avg_facility_ratings);
        $("#avg_value_for_money_ratings").text(
            reviews_meta.avg_value_for_money_ratings
        );
        $("#avg_cleanliness_ratings").text(
            reviews_meta.avg_cleanliness_ratings
        );
        $("#total_reviews_count").text(
            `${_l("web.common.showing")} ${reviews_meta.total_reviews} ${_l(
                "web.common.reviews"
            )}`
        );
    }

    function renderReviews(reviews) {
        if (reviews.length > 0) {
            $("#review_list_main_card").show();
            $("#review_list_container").empty();
            $.each(reviews, function (index, review) {
                let starsHtml = "";
                for (let i = 1; i <= 5; i++) {
                    if (i <= Math.floor(review.average_ratings)) {
                        starsHtml += '<i class="fas fa-star filled"></i>';
                    } else if (
                        i === Math.ceil(review.average_ratings) &&
                        review.average_ratings % 1 !== 0
                    ) {
                        starsHtml +=
                            '<i class="fas fa-star-half-alt filled"></i>';
                    } else {
                        starsHtml += '<i class="far fa-star"></i>';
                    }
                }
                $("#review_list_container").append(`
                    <li>
                        <div class="review-wraps wrap-card">
                            <div class="review-header-group">
                                <div class="review-widget-header">
                                    <span class="review-widget-img">
                                        <img src="${
                                            review.profile_image
                                        }" class="img-fluid" alt="User">
                                    </span>
                                    <div class="review-design">
                                        <h6>${
                                            review.full_name
                                                ? review.full_name
                                                : review.user_name
                                        }</h6>
                                        <p>${review.review_date}</p>
                                    </div>
                                </div>
                                <div class="reviewbox-list-rating">
                                    <p>
                                        ${starsHtml}
                                        <span> (${
                                            review.average_ratings
                                        })</span>
                                    </p>
                                </div>
                            </div>
                            <p>${review.comments}</p>
                            <div class="review-reply">
                                ${
                                    $("#auth_user_id").val() != ""
                                        ? `<button type="button" class="btn review_reply_btn" data-id="${
                                              review.id
                                          }">
                                    <i class="fa-solid fa-reply"></i>${_l(
                                        "web.home.reply"
                                    )}
                                </button>`
                                        : `<a class="btn" href="/login" data-id="${
                                              review.id
                                          }">
                                    <i class="fa-solid fa-reply"></i>${_l(
                                        "web.home.reply"
                                    )}
                                </a>`
                                }
                                <div class="review-action d-none">
                                    <a href="#"><i class="fa-regular fa-thumbs-up"></i>${
                                        review.likes
                                    }</a>
                                    <a href="#"><i class="fa-regular fa-thumbs-down"></i>${
                                        review.dislikes
                                    }</a>
                                </div>
                            </div>
                            <div class="review_reply_box" style="display: none;">
                            </div>
                            <ul class="mt-3 review_reply_list" id="review_reply_list_${
                                review.id
                            }" data-review_id="${review.id}">

                            </ul>
                        </div>
                    </li>
                `);
                if (review.replies) {
                    renderReviewReplies(review.id, review.replies);
                }
            });
        } else {
            $("#review_list_main_card").hide();
        }
    }

    function renderReviewReplies(review_id, replies) {
        $.each(replies, function (index, reply) {
            $("#review_reply_list_" + review_id).append(`
                <li>
                    <div class="review-wraps">
                        <div class="review-header-group">
                            <div class="review-widget-header">
                                <span class="review-widget-img">
                                    <img src="${
                                        reply.profile_image
                                    }" class="img-fluid" alt="User">
                                </span>
                                <div class="review-design">
                                    <h6>${
                                        reply.full_name
                                            ? reply.full_name
                                            : reply.user_name
                                    }</h6>
                                    <p>${reply.reply_date}</p>
                                </div>
                            </div>
                        </div>
                        <p>${reply.comments}</p>
                        <div class="review-reply justify-content-end d-none">
                            <div class="review-action ">
                                <a href="#"><i class="fa-regular fa-thumbs-up"></i>10</a>
                                <a href="#"><i class="fa-regular fa-thumbs-down"></i>12</a>
                            </div>
                        </div>
                    </div>
                </li>
            `);
        });
    }

    $(document).on("click", ".review_reply_btn", function () {
        $(".review_reply_box").empty();
        let parentContainer = $(this).closest(".review-wraps");
        let replyContainer = parentContainer.find(".review_reply_box");
        let review_id = $(this).data("id");

        replyContainer.html(`
            <form id="reply_review_form">
                <div class="input-group mt-3 reply-box">
                    <textarea class="form-control reply-text" id="reply_comments" name="reply_comments" rows="1" placeholder="${_l(
                        "web.home.write_a_reply"
                    )}..."></textarea>
                    <button class="btn btn-primary btn-sm send-reply" data-review-id="1">
                    <i class="fa-solid fa-paper-plane"></i> ${_l(
                        "web.home.send_reply"
                    )}
                    </button>
                </div>
                <span class="error-text text-danger" id="reply_comments_error"></span>
            </form>
        `);
        $(".review_reply_box").not(replyContainer).slideUp();
        replyContainer.slideToggle();

        $("#reply_review_form").validate({
            rules: {
                reply_comments: {
                    required: true,
                    minlength: 3,
                },
            },
            messages: {
                reply_comments: {
                    required: _l("web.home.reply_comments_required"),
                    minlength: _l("web.home.reply_comments_minlength"),
                },
            },
            errorPlacement: function (error, element) {
                if (element.hasClass("select2-hidden-accessible")) {
                    var errorId = element.attr("id") + "_error";
                    $("#" + errorId).text(error.text());
                } else {
                    var errorId = element.attr("id") + "_error";
                    $("#" + errorId).text(error.text());
                }
            },
            highlight: function (element) {
                if ($(element).hasClass("select2-hidden-accessible")) {
                    $(element)
                        .next(".select2-container")
                        .addClass("is-invalid")
                        .removeClass("is-valid");
                }
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
                if ($(element).hasClass("select2-hidden-accessible")) {
                    $(element)
                        .next(".select2-container")
                        .removeClass("is-invalid")
                        .addClass("is-valid");
                }
                $(element).removeClass("is-invalid").addClass("is-valid");
                var errorId = element.id + "_error";
                $("#" + errorId).text("");
            },
            onkeyup: function (element) {
                $(element).valid();
            },
            onchange: function (element) {
                $(element).valid();
            },
            submitHandler: function (form) {
                let formData = new FormData(form);
                formData.append("review_id", review_id);
                formData.append("vehicle_id", $("#vehicle_id").val());

                $.ajax({
                    type: "POST",
                    url: "/user/add-reply-review",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    beforeSend: function () {
                        $(".send-reply").attr("disabled", true).html(`
                            <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l(
                                "web.common.sending"
                            )}..
                        `);
                    },
                    success: function (resp) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        $(".send-reply")
                            .removeAttr("disabled")
                            .html(_l("web.common.send_reply"));
                        $("#reply_review_form")[0].reset();

                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            listReviews();
                        }
                    },
                    error: function (error) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        $(".send-reply")
                            .removeAttr("disabled")
                            .html(_l("web.common.send_reply"));
                        if (error.responseJSON.code === 422) {
                            $.each(
                                error.responseJSON.errors,
                                function (key, val) {
                                    $("#" + key).addClass("is-invalid");
                                    $("#" + key + "_error").text(val[0]);
                                }
                            );
                        } else {
                            showToast("error", error.responseJSON.message);
                        }
                    },
                });
            },
        });
    });

    $(document).on("click", ".send-reply", function () {
        $("#reply_review_form").validate();
    });

    function fetchVehicleDetails() {
        let slug = $("#slug").data("slug");
        $.ajax({
            type: "POST",
            url: "/vehicle-list-detail-api",
            data: {
                vehicle_slug: slug,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            beforeSend: function () {
                $(".skeleton-container").removeClass("d-none");
                $(".real-data").addClass("d-none");
            },
            success: function (response) {
                if (response.code === 200 && response.data) {
                    populateVehicleDetails(response.data);
                    _vehicleDetails = response.data;
                }
            },
            complete: function () {
                setTimeout(function () {
                    $(".skeleton-container").addClass("d-none");
                    $(".real-data").removeClass("d-none");
                }, 100);
            },
        });
    }

    function populateVehicleDetails(vehicle) {
        $(".vehicle_name").text(vehicle.name ?? "");
        $(".vehicle_type").text(vehicle.car_type ?? "");
        $(".vehicle_year").text(vehicle.year ?? "");
        $(".vehicle_location").text(vehicle.location ?? "");
        $(".vehicle_brand").text(vehicle.brand ?? "");
        $(".vehicle_transmission").text(vehicle.transmission ?? "");
        $(".vehicle_fuel").text(vehicle.fuel_type ?? "");
        $(".vehicle_mileage").text(
            vehicle.mileage ? Math.ceil(vehicle.mileage) : ""
        );
        $(".vehicle_doors").text(vehicle.num_doors ?? "");
        $(".vehicle_hatch").text(vehicle.hatch ?? "");
        let vehicleImages = createVehicleCard(vehicle);
        let cleanImage = DOMPurify.sanitize(vehicleImages);
        $(".detail-product").empty().append(cleanImage);
        let ratingHtml = renderStars(vehicle.rating || 0);
        let cleanRating = DOMPurify.sanitize(ratingHtml);
        $(".headratings").html(cleanRating);
        $("#vin").text(vehicle.vin ?? "");

        renderDescription(vehicle);
        createExtraService(vehicle);
        renderFeatures(vehicle);
        renderTarrif(vehicle);
        renderFaqs(vehicle);
        renderGallery(vehicle);
        renderPriceDetails(vehicle);
        setTimeout(function () {
            reinitializeSleek();
        }, 150);
    }

    function renderStars(rating) {
        let fullStars = Math.floor(rating);
        let halfStar = rating - fullStars >= 0.5;
        let emptyStars = 5 - fullStars - (halfStar ? 1 : 0);

        let starsHtml = "";

        for (let i = 0; i < fullStars; i++) {
            starsHtml += `<i class="fas fa-star filled"></i>`;
        }

        if (halfStar) {
            starsHtml += `<i class="fas fa-star-half-alt filled"></i>`;
        }

        for (let i = 0; i < emptyStars; i++) {
            starsHtml += `<i class="fas fa-star"></i>`;
        }

        starsHtml += `<span class="d-inline-block average-list-rating">(${rating.toFixed(
            1
        )})</span>`;

        return starsHtml;
    }

    function renderPriceDetails(vehicle) {
        let currency = vehicle.currency;
        let priceOptions = "";
        if (
            vehicle.multiple_vehicle_policy &&
            vehicle.multiple_vehicle_policy.length > 0
        ) {
            $("#policy-section").removeClass("d-none");
        }
        $.each(vehicle.price, function (index, value) {
            let priceType = Object.keys(value)[0];
            let amount = value[priceType];
            let formattedPriceType =
                priceType.charAt(0).toUpperCase() + priceType.slice(1);

            priceOptions += `<label class="booking_custom_check bookin-check-2">
                                <input type="radio" name="price_rate" class="price-rate-option"
                                    data-amount="${amount}" data-price-type="${priceType}" value="${amount}" ${
                index === 0 ? "checked" : ""
            }>
                                <span class="booking_checkmark">
                                    <span class="checked-title">${formattedPriceType}</span>
                                    <span class="price-rate">${
                                        currency + amount
                                    }</span>
                                </span>
                            </label>`;

            if (index === 0) {
                _pricing_type = priceType;
            }
        });

        let cleanDescription = DOMPurify.sanitize(priceOptions);
        $(".price_options").empty().append(cleanDescription);
        let has_pickup_date = $("#has_pickup_date").val();
        if (!has_pickup_date) {
            $(".price-rate-option").on("change", handlePriceChange);
            $(".price-rate-option:checked").trigger("change");
            setTimeout(() => {
                let $picker = $(".bookingpickupdate");
                $picker.trigger("dp.change");
                let $booking_timepicker = $(".booking_timepicker");
                $booking_timepicker.data("DateTimePicker").minDate(moment());
                let $bookingreturndate = $(".bookingreturndate");
                $bookingreturndate.trigger("dp.change");
                $(".bookingpickupdate").trigger("focus");
                $(".bookingpickupdate").trigger("blur");
                $(".bookingreturndate").trigger("focus");
                $(".bookingreturndate").trigger("blur");
            }, 500);
        } else {
            setTimeout(function () {
                let $picker = $(".bookingpickupdate");
                let $booking_timepicker = $(".booking_timepicker");
                let pickup_time = $booking_timepicker
                    .data("DateTimePicker")
                    .date();
                $picker.trigger("dp.change");
                setTimeout(function () {
                    $booking_timepicker
                        .data("DateTimePicker")
                        .date(pickup_time);
                    $booking_timepicker.trigger("dp.change");
                }, 100);
                $(".bookingpickupdate").trigger("focus");
                $(".bookingpickupdate").trigger("blur");
                $(".bookingreturndate").trigger("focus");
                $(".bookingreturndate").trigger("blur");
            }, 500);
        }
    }

    $(document).on("click", ".view-policies", function () {
        let policies =
            _vehicleDetails && _vehicleDetails.multiple_vehicle_policy
                ? _vehicleDetails.multiple_vehicle_policy
                : [];
        if (policies.length > 0) {
            $.each(policies, function (index, policy) {
                window.open(policy, "_blank");
            });
        }
    });

    function handlePriceChange() {
        let selectedPriceType = $(this).data("price-type");
        let selectedAmount = parseFloat($(this).val()) || 0;
        $("#price_type").val(selectedPriceType);

        let currentDateTime = moment();
        $("#pickup_date").val(currentDateTime.format("DD-MM-YYYY"));
        $("#pickup_time").val(currentDateTime.format("HH:mm"));

        let returnDateTime = moment(currentDateTime);
        let duration = 1;
        _pricing_type = selectedPriceType;
        let $bookingpickupdate = $(".bookingpickupdate");
        $bookingpickupdate.trigger("dp.change");
        // Reset readonly first
        $("#return_date, #return_time").prop("readonly", false);

        switch (selectedPriceType) {
            case "daily":
                returnDateTime.add(1, "days");
                break;
            case "weekly":
                returnDateTime.add(7, "days");
                $("#return_date, #return_time").prop("readonly", true);
                break;
            case "monthly":
                returnDateTime.add(1, "months");
                $("#return_date, #return_time").prop("readonly", true);
                break;
            case "yearly":
                returnDateTime.add(1, "years");
                $("#return_date, #return_time").prop("readonly", true);
                break;
        }
        if (_pricing_type != "daily") {
            $("#return_date").val(returnDateTime.format("DD-MM-YYYY"));
            $("#return_time").val(returnDateTime.format("HH:mm"));
        }

        if (selectedPriceType === "daily") {
            let pickupDateTime = moment($("#pickup_date").val(), "DD-MM-YYYY");
            let returnDateTime = moment($("#return_date").val(), "DD-MM-YYYY");

            let totalDays = Math.ceil(
                moment.duration(returnDateTime.diff(pickupDateTime)).asDays()
            );
            totalDays = totalDays <= 0 ? 1 : totalDays;
            duration = totalDays;
        }

        let finalPrice = selectedAmount * duration;
        $("#final_price_rate").val(finalPrice.toFixed(2));
    }

    function renderDescription(vehicle) {
        const descriptionSection = $(".description_section");
        descriptionSection.empty().hide();

        if (vehicle.description && vehicle.description.trim() !== "") {
            const maxWords = 50; // Number of words to show before truncation
            // Convert HTML to text first
            const plainText = $("<div>")
                .html(vehicle.description.trim())
                .text();
            const words = plainText
                .split(/\s+/)
                .filter((word) => word.length > 0);

            let html = `
                <div class="review-header">
                    <h4>${_l("web.home.desc_of_listing")}</h4>
                </div>
                <div class="description-list">`;

            if (words.length > maxWords) {
                const visibleWords = words.slice(0, maxWords).join(" ");
                const hiddenWords = words.slice(maxWords).join(" ");

                html += `
                    <div class="visible-text">${escapeHtml(
                        visibleWords
                    )}...</div>
                    <div class="read-more">
                        <div class="more-text" style="display: none;">${escapeHtml(
                            hiddenWords
                        )}</div>
                        <button type="button" class="border-0 bg-white  more-link">${_l(
                            "web.home.show_more"
                        )}</button>
                    </div>`;
            } else {
                html += `<div class="visible-text">${escapeHtml(
                    plainText
                )}</div>`;
            }

            html += `</div>`;
            descriptionSection.html(html).show();

            // Click handler
            descriptionSection
                .find(".more-link")
                .off("click")
                .on("click", function () {
                    const moreText = $(this).siblings(".more-text");
                    const isVisible = moreText.is(":visible");

                    moreText.slideToggle(200);
                    $(this).text(
                        isVisible
                            ? _l("web.home.show_more")
                            : _l("web.home.show_less")
                    );
                });
        }
    }

    // Helper function to escape HTML
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function renderFeatures(vehicle) {
        const featureSection = $(".feature_section");

        if (vehicle.features && vehicle.features.length > 0) {
            // Split features into 3 columns for better layout
            const columnCount = 3;
            const featuresPerColumn = Math.ceil(
                vehicle.features.length / columnCount
            );
            let html = '<div class="row">';

            for (let i = 0; i < columnCount; i++) {
                const columnFeatures = vehicle.features.slice(
                    i * featuresPerColumn,
                    (i + 1) * featuresPerColumn
                );

                if (columnFeatures.length > 0) {
                    html += '<div class="col-md-4"><ul>';
                    columnFeatures.forEach((feature) => {
                        html += `<li><span><i class="bx bx-check-double"></i></span>${feature}</li>`;
                    });
                    html += "</ul></div>";
                }
            }

            html += "</div>";

            let cleanHtml = DOMPurify.sanitize(html);
            featureSection.find(".listing-description").html(cleanHtml);
            featureSection.show();
        } else {
            featureSection.hide();
        }
    }

    function renderGallery(vehicle) {
        if (
            !vehicle.multiple_vehicle_images ||
            vehicle.multiple_vehicle_images.length === 0
        ) {
            $(".gallery_section").hide();
            $(".video_section").hide();
            return;
        }

        const galleryImages = vehicle.multiple_vehicle_images
            .map(
                (img) => `
            <li>
                <div class="gallery-widget">
                    <a href="${img}" data-fancybox="gallery1">
                        <img class="img-fluid thumb-img" alt="Image" src="${img}">
                    </a>
                </div>
            </li>
        `
            )
            .join("");

        const html = `
            <div class="review-header">
                <h4>${_l("web.home.gallery")}</h4>
            </div>
            <div class="gallery-list">
                <ul>${galleryImages}</ul>
            </div>
        `;

        let cleanHtml = DOMPurify.sanitize(html);
        $(".gallery_section").html(cleanHtml).show();

        if (vehicle.vehicle_video && vehicle.vehicle_video != "") {
            $(".video_section").removeClass("d-none");
            $("#video_thumb").attr("src", vehicle.vehicle_image);
            $("#video").attr("href", vehicle.vehicle_video);
        }
        // Fancybox v3
        if ($.fancybox) {
            $.fancybox.destroy();
            $("[data-fancybox='gallery1']").fancybox();
        }

        // Fancybox v4
        if (typeof Fancybox !== "undefined") {
            Fancybox.unbind("[data-fancybox='gallery1']");
            Fancybox.bind("[data-fancybox='gallery1']", {});
        }
    }

    function renderTarrif(vehicle) {
        if (!vehicle?.tariff || vehicle.tariff.length === 0) {
            $(".tariff_section").hide();
            return;
        }

        const currency = vehicle.currency;
        const currency_position = "after";

        const formatPrice = (price) =>
            price
                ? currency_position === "before"
                    ? `${currency}${price}`
                    : `${price}${currency}`
                : "";

        const tbody = document.querySelector("#tarrifTable tbody");

        tbody.innerHTML = "";

        vehicle.tariff.forEach((value) => {
            const tr = document.createElement("tr");

            const td1 = document.createElement("td");
            td1.textContent = value.tariff_title ?? "";

            const td2 = document.createElement("td");
            td2.textContent = formatPrice(value.tariff_daily_price);

            const td3 = document.createElement("td");
            td3.textContent = value.tariff_base_km ?? 0;

            const td4 = document.createElement("td");
            td4.textContent = formatPrice(value.tariff_extra_price);

            tr.append(td1, td2, td3, td4);

            tbody.appendChild(tr);
        });


        $(".tariff_section").show();
    }

    function renderFaqs(vehicle) {
        if (!vehicle?.faqs || vehicle.faqs.length === 0) {
            $(".faq_section").hide();
            return;
        }

        const faqItems = vehicle.faqs
            .map(
                (faq, index) => `
            <div class="faq-card">
                <h4 class="faq-title">
                    <a class="collapsed" data-bs-toggle="collapse" href="#faq${index}" aria-expanded="false">
                        ${faq.question || ""}
                    </a>
                </h4>
                <div id="faq${index}" class="card-collapse collapse">
                    <p>${faq.answer || ""}</p>
                </div>
            </div>
        `
            )
            .join("");

        const html = `
                <div class="review-header">
                    <h4>${_l("web.home.faqs")}</h4>
                </div>
                <div class="faq-info">
                    ${faqItems}
                </div>
        `;

        let cleanHtml = DOMPurify.sanitize(html);
        $(".faq_section").html(cleanHtml).show();
    }

    function createExtraService(vehicle) {
        let html = "";
        if (vehicle.extraservice && vehicle.extraservice.length > 0) {
            let extraservice = vehicle.extraservice
                .map(
                    (service) =>
                        `<div class="servicelist d-flex align-items-center col-xxl-3 col-xl-4 col-sm-6">
                    <div class="service-img">
                        <img src="${
                            service.icon
                        }" class="extra-service-img" alt="${
                            service.name ?? ""
                        }">
                    </div>
                    <div class="service-info">
                        <p>${service.name ?? ""}</p>
                    </div>
                </div>`
                )
                .join("");
            html += `<div class="pb-0 extra-service">
                        <div class="review-header">
                            <h4>${_l("web.user.extra_services")}</h4>
                        </div>
                        <div class="lisiting-service">
                            <div class="row">
                                ${extraservice}
                            </div>
                        </div>
                    </div>`;
            let cleanHtml = DOMPurify.sanitize(html);
            $(".extra-service-div").html(cleanHtml);
        } else {
            $(".extra-service-div").hide();
        }
    }

    function createVehicleCard(vehicle) {
        if (
            !vehicle?.multiple_vehicle_images ||
            vehicle.multiple_vehicle_images.length === 0
        ) {
            return "";
        }

        const vehicleInfo = `
            <div class="pro-info">
                <div class="">
                    <span class="badge-km d-none">
                        <i class="fa-solid fa-person-walking"></i> 4.2 Km Away
                    </span>
                    ${
                        vehicle.authenticated
                            ? `<button type="button" data-id="${
                                  vehicle.id
                              }" class="fav-icon border-0 wishlist-icon ${
                                  vehicle.wishlist ? "selected" : ""
                              }">
                        <i class="fa-regular fa-heart"></i>
                    </button>`
                            : ""
                    }
                </div>
            </div>`;

        const sliderImages = vehicle.multiple_vehicle_images
            .map(
                (img) => `
            <div class="product-img">
                <img src="${img}" alt="${vehicle.name ?? "Vehicle"}">
            </div>`
            )
            .join("");

        const thumbnailImages = vehicle.multiple_vehicle_images
            .map(
                (img) => `
            <div>
                <img src="${img}" alt="${vehicle.name ?? "Vehicle"}">
            </div>`
            )
            .join("");

        return `
            ${vehicleInfo}
            <div class="slider detail-bigimg">${sliderImages}</div>
            <div class="slider slider-nav-thumbnails">${thumbnailImages}</div>`;
    }

    function reinitializeSleek() {
        let isRtl =
            $("body").data("dir") && $("body").data("dir") == "rtl"
                ? true
                : false;
        if ($(".detail-bigimg").length > 0) {
            $(".detail-bigimg").slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                rtl: isRtl,
                arrows: true,
                fade: true,
                asNavFor: ".slider-nav-thumbnails",
            });
        }

        if ($(".slider-nav-thumbnails").length > 0) {
            $(".slider-nav-thumbnails").slick({
                slidesToShow: 4,
                slidesToScroll: 4,
                rtl: isRtl,
                asNavFor: ".detail-bigimg",
                dots: false,
                arrows: false,
                centerMode: false,
                focusOnSelect: true,
            });
        }
    }

    function reInitializeCarousel(className) {
        let isRtl = $("body").data("dir") && $("body").data("dir") == "rtl";
        $(className).owlCarousel({
            loop: true,
            margin: 24,
            nav: true,
            dots: true,
            rtl: isRtl,
            smartSpeed: 2000,
            autoplay: false,
            navText: [
                '<i class="fa-solid fa-chevron-left"></i>',
                '<i class="fa-solid fa-chevron-right"></i>',
            ],
            responsive: {
                0: {
                    items: 1,
                },
                550: {
                    items: 1,
                },
                768: {
                    items: 2,
                },
                1000: {
                    items: 4,
                },
            },
        });
    }

    $(document).on("click", ".wishlist-icon", function () {
        let id = $(this).data("id");
        let button = $(this);
        $.ajax({
            type: "POST",
            url: "/user/add-to-wishlist",
            data: {
                id: id,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "json",
            success: function (response) {
                if (response.status == "success") {
                    if (button.hasClass("selected")) {
                        button.removeClass("selected");
                    } else {
                        button.addClass("selected");
                    }
                    showToast("success", response.message);
                } else {
                    showToast("error", response.message);
                }
            },
            error: function (error) {},
        });
    });

    $(document).on("click", "#enquire_us", function () {
        $("#enquiry").modal("show");
    });
})();
