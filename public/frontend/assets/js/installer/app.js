/* global $, toastr, window */

(function () {
    "use strict";

    // CSRF token setup for all AJAX requests
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")
        }
    });

    /**
     * Makes a secure AJAX POST request.
     * @param {Object} formData - The form data to be submitted.
     * @param {string} actionUrl - The endpoint to send the data.
     * @returns {Promise<Object>} - Resolves with response or rejects with error.
     */
    const makeAjaxRequest = function (formData, actionUrl) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                url: actionUrl,
                method: "POST",
                data: formData,
                dataType: "json",
                success: function (res) {
                    resolve(res);
                },
                error: function (err) {
                    reject(err);
                }
            });
        });
    };
	
	window.makeAjaxRequest = makeAjaxRequest;
	
    $(function () {
        // Toastr config with consistent styles
        toastr.options = {
            closeButton: true,
            positionClass: "toast-top-right",
            timeOut: 3000,
            progressBar: true,
            onShown: function () {
                const colorMap = {
                    "toast-success": "#28a745",
                    "toast-error": "#dc3545",
                    "toast-warning": "#f0ad4e",
                    "toast-info": "#17a2b8"
                };

                Object.entries(colorMap).forEach(function ([cls, bg]) {
                    $("." + cls).css({
                        backgroundColor: bg,
                        color: "#ffffff"
                    });
                });
            }
        };
    });
})();
