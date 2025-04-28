"use strict";
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
		minDate: moment().startOf("day"),
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
		maxDate: moment().endOf("year"),
		useCurrent: false,
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

$(document).on("click", ".change-language", function () {
    var languageCode = $(this).data("language_code");

    $.ajax({
        url: "/admin/flag-change-language",
        type: "POST",
        data: { language_code: languageCode },
        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
        success: function (response) {
            if (response.status === "success") {
                location.reload();
            }
        }
    });
});

let permission_error = $('body').data('permission_error');
if (permission_error) {
    showToast('error', permission_error);
}
$(document).ready(function () {
	fetchNotifications();
});
function fetchNotifications() {
	$.ajax({
		type: "GET",
		url: "/admin/get-notifications",
		dataType: "json",
		success: function (response) {
			$(".noti-content").html(response.html);
			if(response.count > 0){
				$("#newNotificationBadge").removeClass("d-none");
				$(".has-notification").removeClass("d-none");
			}else{
				$("#newNotificationBadge").addClass("d-none");
				$(".has-notification").addClass("d-none");
			}
		},
	});
}

$(document).on("click", "#markAllAsRead", function () {
	$.ajax({
		type: "POST",
		url: "/admin/mark-all-notifications-as-read",
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
		}
	});
}); 