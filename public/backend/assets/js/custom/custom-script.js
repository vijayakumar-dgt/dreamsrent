(function () {
    "use strict";
	window.showToast = function (toastType, message) {
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

	window.initializeTooltips = function () {
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
				$(".noti-content").html(DOMPurify.sanitize(response.html));
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

	$(document).on('hide.bs.modal', function (e) {
		const activeElement = document.activeElement;
		const modal = e.target;

		if (modal.contains(activeElement)) {
			activeElement.blur();
		}
	});

	window.customizeTableFooter = function (tableInstance) {
		$(".dataTables_info").addClass("d-none");
		$(".dataTables_wrapper .dataTables_paginate").addClass("d-none");

		let tableWrapper = tableInstance.closest(".dataTables_wrapper");
		let info = tableWrapper.find(".dataTables_info");
		let pagination = tableWrapper.find(".dataTables_paginate");

		const footerContent = $('<div class="d-flex justify-content-between align-items-center w-100"></div>')
			.append(
				$('<div class="datatable-info"></div>').append(info.clone(true))
			)
			.append(
				$('<div class="datatable-pagination"></div>').append(pagination.clone(true))
			);

		$(".table-footer").empty().append(footerContent);
		$(".table-footer").find(".dataTables_paginate").removeClass("d-none");
	}

	window.getDataTableLanguage = function () {
		const $langEl = $(".datatable-language-data");
		if (!$langEl.length) return {};

		return {
			emptyTable: $langEl.data("empty_table"),
			info: $langEl.data("info"),
			infoEmpty: $langEl.data("info_empty"),
			infoFiltered: $langEl.data("info_filtered"),
			lengthMenu: $langEl.data("length_menu"),
			search: $langEl.data("search"),
			zeroRecords: $langEl.data("zero_records"),
			paginate: {
				first: $langEl.data("paginate_first"),
				last: $langEl.data("paginate_last"),
				next: $langEl.data("paginate_next"),
				previous: $langEl.data("paginate_previous"),
			},
		};
	}
})();
