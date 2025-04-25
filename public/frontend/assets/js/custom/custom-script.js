function showToast(toastType, message) {
	let toastId = '';
	if (toastType == 'success') {
		toastId = 'successToast'
	} else if (toastType == 'error') {
		toastId ='dangerToast'
	} else if (toastType == 'warning') {
		toastId ='warningToast'
	} else if (toastType == 'info') {
		toastId ='infoToast'
	} else if (toastType == 'secondary') {
		toastId ='secondaryToast'
	} else {
		toastId ='primaryToast'
	}
	var toastElement = document.getElementById(toastId);
	if (toastElement) {
		toastElement.querySelector(".toast-body").innerText = message;

		var toast = new bootstrap.Toast(toastElement, {
			animation: true,
			autohide: true,
			delay: 2000,
		});
		toast.show();
	}else{
		console.log('toast not found');
	}
}

if ($(".datetimepickerVehicle").length > 0) {
	$(".datetimepickerVehicle").datetimepicker({
		format: "DD-MM-YYYY",
		minDate: moment().startOf("day"), // Disables past dates, allows only future
		icons: {
			up: "fas fa-angle-up",
			down: "fas fa-angle-down",
			next: "fas fa-angle-right",
			previous: "fas fa-angle-left",
		},
	});
}

if ($(".yearpickerVehicle").length > 0) {
	$(".yearpickerVehicle").datetimepicker({
		viewMode: "years",
		format: "YYYY",
		maxDate: moment().endOf("year"), // Restricts selection to past years only
		useCurrent: false, // Prevents auto-setting of the current year in input
		icons: {
			up: "fas fa-angle-up",
			down: "fas fa-angle-down",
			next: "fas fa-angle-right",
			previous: "fas fa-angle-left",
		},
	});
}

function initializeTooltips() {
    $('[data-bs-toggle="tooltip"]').each(function () {
        let tooltipInstance = bootstrap.Tooltip.getInstance(this);
        if (tooltipInstance) {
            tooltipInstance.dispose();
        }
    });

    $('[data-bs-toggle="tooltip"]').tooltip();
}


$(document).ready(function () {
    fetchNotifications();
    const $cookieBanner = $("#cookieConsentBanner");
    const $agreeButton = $("#cookieAgree");
    const $declineButton = $("#cookieDecline");

    function getCookie(name) {
        return document.cookie.split("; ").some(row => row.startsWith(name + "="));
    }

    if (!getCookie("cookie_consent")) {
        setTimeout(() => {
            $cookieBanner.removeClass("d-none");
        }, 2000);
    }

    $agreeButton.on("click", function () {
        document.cookie = "cookie_consent=accepted; path=/; max-age=" + 60 * 60 * 24 * 30;
        $cookieBanner.addClass("d-none");
    });

    $declineButton.on("click", function () {
        $cookieBanner.addClass("d-none");
    });
});
if ($(".homepickupdate").length > 0) {
    $(".homepickupdate").datetimepicker({
        format: "DD-MM-YYYY",
        useCurrent: false,
        minDate: moment().startOf("day"),
        icons: {
            up: "fas fa-angle-up",
            down: "fas fa-angle-down",
            next: "fas fa-angle-right",
            previous: "fas fa-angle-left",
        },
    }).on("dp.change", function (e) {
        const pickupDate = e.date;
        const returnDate = $(".homereturndate").data("DateTimePicker").date();

        if (pickupDate && pickupDate.isSame(moment(), "day")) {
            $(".hometimepicker").data("DateTimePicker").minDate(moment());
        } else {
            $(".hometimepicker").data("DateTimePicker").minDate(false);
        }

        if (pickupDate) {
            const pickupOnly = pickupDate.clone().startOf("day");
            $(".homereturndate").data("DateTimePicker").minDate(pickupOnly);
        }

        if (returnDate) {
            const returnOnly = returnDate.clone().startOf("day");
            $(".homepickupdate").data("DateTimePicker").maxDate(returnOnly);
        } else {
            $(".homepickupdate").data("DateTimePicker").maxDate(false);
        }
    });
}

if ($(".hometimepicker").length > 0) {
    $(".hometimepicker").datetimepicker({
        format: "HH:mm",
        useCurrent: true,
        icons: {
            up: "fas fa-angle-up",
            down: "fas fa-angle-down",
            next: "fas fa-angle-right",
            previous: "fas fa-angle-left",
        },
    }).on("dp.change", function (e) {
        const pickupTime = e.date;
        const pickupDate = $(".homepickupdate").data("DateTimePicker").date();
        const returnDate = $(".homereturndate").data("DateTimePicker").date();
        const returnTimePicker = $(".homereturntimepicker").data("DateTimePicker");
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


if ($(".homereturndate").length > 0) {
    $(".homereturndate").datetimepicker({
        format: "DD-MM-YYYY",
        useCurrent: false,
        minDate: moment().startOf("day"),
        icons: {
            up: "fas fa-angle-up",
            down: "fas fa-angle-down",
            next: "fas fa-angle-right",
            previous: "fas fa-angle-left",
        },
    }).on("dp.change", function (e) {
        const returnDate = e.date;
        const pickupDate = $(".homepickupdate").data("DateTimePicker").date();

        if (!pickupDate || !returnDate) return;

        const returnOnly = returnDate.clone().startOf("day");
        const pickupOnly = pickupDate.clone().startOf("day");

        $(".homepickupdate").data("DateTimePicker").maxDate(returnOnly);
        $(".homereturndate").data("DateTimePicker").minDate(pickupOnly);

        if (returnOnly.isSame(pickupOnly, "day")) {
            const pickupTime = $(".hometimepicker").data("DateTimePicker").date();
            if (pickupTime) {
                const minReturnTime = moment(pickupTime).add(1, "hour");
                $(".homereturntimepicker").data("DateTimePicker").minDate(minReturnTime);
            }
        } else if (returnOnly.isSame(moment(), "day")) {
            $(".homereturntimepicker").data("DateTimePicker").minDate(moment().add(1, "hour"));
        } else {
            $(".homereturntimepicker").data("DateTimePicker").minDate(false);
        }
    });
}

if ($(".homereturntimepicker").length > 0) {
    $(".homereturntimepicker").datetimepicker({
        format: "HH:mm",
        useCurrent: true,
        icons: {
            up: "fas fa-angle-up",
            down: "fas fa-angle-down",
            next: "fas fa-angle-right",
            previous: "fas fa-angle-left",
        },
    }).on("dp.change", function (e) {
        const returnTime = e.date;
        const pickupTime = $(".hometimepicker").data("DateTimePicker").date();
        const pickupDate = $(".homepickupdate").data("DateTimePicker").date();
        const returnDate = $(".homereturndate").data("DateTimePicker").date();

        if (
            pickupDate &&
            returnDate &&
            pickupTime &&
            returnTime &&
            pickupDate.isSame(returnDate, "day")
        ) {
            if (!returnTime.isAfter(moment(pickupTime).add(59, "minutes"))) {
                $(this).data("DateTimePicker").date(null);
                alert("Return time must be at least 1 hour after pickup time.");
            }
        }
    });
}




//list
if ($(".listpickupdate").length > 0) {
    $(".listpickupdate").datetimepicker({
        format: "DD-MM-YYYY",
        useCurrent: false,
        minDate: moment().startOf("day"),
        icons: {
            up: "fas fa-angle-up",
            down: "fas fa-angle-down",
            next: "fas fa-angle-right",
            previous: "fas fa-angle-left",
        },
    }).on("dp.change", function (e) {
        const pickupDate = e.date;
        const returnDate = $(".listreturndate").data("DateTimePicker").date();

        if (pickupDate && pickupDate.isSame(moment(), "day")) {
            $(".listtimepicker").data("DateTimePicker").minDate(moment());
        } else {
            $(".listtimepicker").data("DateTimePicker").minDate(false);
        }

        if (pickupDate) {
            const pickupOnly = pickupDate.clone().startOf("day");
            $(".listreturndate").data("DateTimePicker").minDate(pickupOnly);
        }

        if (returnDate) {
            const returnOnly = returnDate.clone().startOf("day");
            $(".listpickupdate").data("DateTimePicker").maxDate(returnOnly);
        } else {
            $(".listpickupdate").data("DateTimePicker").maxDate(false);
        }
    });
}

if ($(".listtimepicker").length > 0) {
    $(".listtimepicker").datetimepicker({
        format: "HH:mm",
        useCurrent: true,
        icons: {
            up: "fas fa-angle-up",
            down: "fas fa-angle-down",
            next: "fas fa-angle-right",
            previous: "fas fa-angle-left",
        },
    }).on("dp.change", function (e) {
        const pickupTime = e.date;
        const pickupDate = $(".listpickupdate").data("DateTimePicker").date();
        const returnDate = $(".listreturndate").data("DateTimePicker").date();
        const returnTimePicker = $(".listreturntimepicker").data("DateTimePicker");
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


if ($(".listreturndate").length > 0) {
    $(".listreturndate").datetimepicker({
        format: "DD-MM-YYYY",
        useCurrent: false,
        minDate: moment().startOf("day"),
        icons: {
            up: "fas fa-angle-up",
            down: "fas fa-angle-down",
            next: "fas fa-angle-right",
            previous: "fas fa-angle-left",
        },
    }).on("dp.change", function (e) {
        const returnDate = e.date;
        const pickupDate = $(".listpickupdate").data("DateTimePicker").date();

        if (!pickupDate || !returnDate) return;

        const returnOnly = returnDate.clone().startOf("day");
        const pickupOnly = pickupDate.clone().startOf("day");

        $(".listpickupdate").data("DateTimePicker").maxDate(returnOnly);
        $(".listreturndate").data("DateTimePicker").minDate(pickupOnly);

        if (returnOnly.isSame(pickupOnly, "day")) {
            const pickupTime = $(".listtimepicker").data("DateTimePicker").date();
            if (pickupTime) {
                const minReturnTime = moment(pickupTime).add(1, "hour");
                $(".listreturntimepicker").data("DateTimePicker").minDate(minReturnTime);
            }
        } else if (returnOnly.isSame(moment(), "day")) {
            $(".listreturntimepicker").data("DateTimePicker").minDate(moment().add(1, "hour"));
        } else {
            $(".listreturntimepicker").data("DateTimePicker").minDate(false);
        }
    });
}

if ($(".listreturntimepicker").length > 0) {
    $(".listreturntimepicker").datetimepicker({
        format: "HH:mm",
        useCurrent: true,
        icons: {
            up: "fas fa-angle-up",
            down: "fas fa-angle-down",
            next: "fas fa-angle-right",
            previous: "fas fa-angle-left",
        },
    }).on("dp.change", function (e) {
        const returnTime = e.date;
        const pickupTime = $(".listtimepicker").data("DateTimePicker").date();
        const pickupDate = $(".listpickupdate").data("DateTimePicker").date();
        const returnDate = $(".listreturndate").data("DateTimePicker").date();

        if (
            pickupDate &&
            returnDate &&
            pickupTime &&
            returnTime &&
            pickupDate.isSame(returnDate, "day")
        ) {
            if (!returnTime.isAfter(moment(pickupTime).add(59, "minutes"))) {
                $(this).data("DateTimePicker").date(null);
                alert("Return time must be at least 1 hour after pickup time.");
            }
        }
    });
}

//theme 2
var pickupDateTime = null;
var dropInstance = null;

if ($(".flatpickr-pickupadtetime").length > 0) {
    flatpickr(".flatpickr-pickupadtetime", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        minDate: "today",
        onChange: function (selectedDates, dateStr, instance) {
            const now = new Date();
            pickupDateTime = selectedDates[0];

            if (pickupDateTime) {
                const isToday = pickupDateTime.toDateString() === now.toDateString();

                instance.set("minTime", isToday ? formatTime(now) : "00:00");

                // Update drop minDate (can't be before pickup)
                if (dropInstance) {
                    dropInstance.set("minDate", pickupDateTime);
                    dropInstance.setDate(null); // reset drop selection
                }
            }
        }
    });
}

if ($(".flatpickr-dropdatetime").length > 0) {
    dropInstance = flatpickr(".flatpickr-dropdatetime", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        minDate: "today",
        onOpen: function (selectedDates, dateStr, instance) {
            if (pickupDateTime) {
                instance.set("minDate", pickupDateTime);
            }
        },
        onChange: function (selectedDates, dateStr, instance) {
            if (!pickupDateTime) return;

            const dropDate = selectedDates[0];

            if (dropDate.toDateString() === pickupDateTime.toDateString()) {
                const minDropTime = new Date(pickupDateTime.getTime() + 60 * 60 * 1000); // +1 hour
                instance.set("minTime", formatTime(minDropTime));
            } else {
                instance.set("minTime", "00:00");
            }
        }
    });
}

function formatTime(date) {
    const hours = String(date.getHours()).padStart(2, "0");
    const minutes = String(date.getMinutes()).padStart(2, "0");
    return `${hours}:${minutes}`;
}


$("#newsletterForm").validate({
    rules: {
        subscriber_email: {
            required: true,
            email: true,
        }
    },
    messages:{
        subscriber_email: {
            required: "Email is required.",
            email: "Please enter a valid email address.",
        }
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
            $(element).next(".select2-container").addClass("is-invalid").removeClass('is-valid');
        }
        $(element).addClass("is-invalid").removeClass("is-valid");
    },
    unhighlight: function (element) {
        if ($(element).hasClass("select2-hidden-accessible")) {
            $(element).next(".select2-container").removeClass("is-invalid").addClass('is-valid');
        }
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
        let formData = new FormData(form);
        let theme = $('body').attr('data-theme');
        $.ajax({
            type:"POST",
            url:"/user/save-newsletter-subscriber",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                $('.submitbtn').attr('disabled', true).html(`
                    <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span>
                `);
            },
            success:function(resp){
                $(".error-text").text("");
                $(".form-control").removeClass("is-invalid is-valid");
                if(theme == 1){
                    $(".submitbtn").removeAttr("disabled").html('<span><i class="feather-send"></i></span>');
                }else{
                    $(".submitbtn").removeAttr("disabled").html('<i class="feather-send"></i>');
                }
                if (resp.code === 200) {
                    showToast('success', resp.message);
                    $('#newsletterForm')[0].reset();
                }
            },
            error:function(error){
                $(".error-text").text("");
                $(".form-control").removeClass("is-invalid is-valid");
                if(theme == 1){
                   $(".submitbtn").removeAttr("disabled").html('<span><i class="feather-send"></i></span>');
                }else{
                    $(".submitbtn").removeAttr("disabled").html('<i class="feather-send"></i>');
                }
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

$(document).on("click",".change-user-language", function () {
    console.log($(this).data("language_code"));
    let languageCode = $(this).data("language_code");
    let language_id = $(this).data("id");
    $.ajax({
        type:"POST",
        url:"/user/flag-change-language",
        data:{
            language_code:languageCode,
            language_id:language_id,
            _token:$('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if (response.status === "success") {
                location.reload();
            }
        }
    });

});

function fetchNotifications() {
    $.ajax({
        type: "GET",
        url: "/user/get-notifications",
        dataType: "json",
        success: function (response) {
            $(".notification-list").html(response.html);
            if(response.count > 0){
                $("#newNotificationBadge").removeClass("d-none");
                $(".has-notification").removeClass("d-none");
            }else{
                $("#newNotificationBadge").addClass("d-none");
                $(".has-notification").addClass("d-none");
            }
        },
        error: function (error) {
            console.error(error);
        }
    });
}
$(document).on("click", "#markAllAsRead", function () {
    $.ajax({
        type: "POST",
        url: "/user/mark-all-notifications-as-read",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content")
        },
        dataType: "json",
        success: function (response) {
            if(response.code === 200){
              showToast(response.status, response.message);
              fetchNotifications();
            }else{
              showToast(response.status, response.message);
            }
        },
        error: function (error) {
            console.error(error);
        }
    });
}); 

window.addEventListener('scroll', () => {
    const header = document.querySelector('.theme-2-header');
    if (window.scrollY > 50) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
});