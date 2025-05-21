(function () {
    "use strict";
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

const makeAjaxRequest = (formData, actionUrl) => {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: actionUrl,
            method: "post",
            data: formData,
            success: function (res) {
                resolve(res);
            },
            error: function (err) {
                reject(err);
            },
        });
    });
};
$(document).ready(function () {
    toastr.options = {
        closeButton: true,
        positionClass: "toast-top-right",
        timeOut: "3000",
        progressBar: true,
        onShown: function () {
            $(".toast-success").css({
                "background-color": "#28a745",
                color: "#fff",
            });
            $(".toast-error").css({
                "background-color": "#dc3545",
                color: "#fff",
            });
            $(".toast-warning").css({
                "background-color": "#f0ad4e",
                color: "#fff",
            });
            $(".toast-info").css({
                "background-color": "#17a2b8",
                color: "#fff",
            });
        },
    };
});
})();