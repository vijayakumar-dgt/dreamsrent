(async () => {
    "use strict";
    await loadTranslationFile('admin', 'common, finance_accounts');

$(document).ready(function () {
    initTable();
});

let selectedStatuses = []; // Store selected statuses
let selectedPaymentTypes = [];
let currentSearch = "";
let currentSortby = "latest";

$("#search").on("input", function () {
    currentSearch = $(this).val().trim();
    initTable();
});

$(".sort-optionss").on("click", function () {
    $(".sort-optionss").removeClass("active");
    $(this).addClass("active");

    currentSortby = $(this).data("sort"); // Get selected sorting option

    $(".dropdown-toggles").html(
        `<i class="ti ti-filter me-1"></i> Sort By : ${$(this).text()}`
    );

    initTable();
});

$(".filyerPaymentStatus input[type='checkbox']").on("change", function () {
    selectedStatuses = $(".filyerPaymentStatus input[type='checkbox']:checked")
        .map(function () {
            return $(this).val();
        })
        .get();
});

$(".filyerPaymentType input[type='checkbox']").on("change", function () {
    selectedPaymentTypes = $(".filyerPaymentType input[type='checkbox']:checked")
        .map(function () {
            return $(this).val();
        })
        .get();
});

// Apply filters
$(".applyFilter").on("click", function () {
    initTable();
});

$(".clearFilter").on("click", function () {
    selectedStatuses = [];
    $(".filyerPaymentStatus input[type='checkbox']").prop("checked", false);
    selectedPaymentTypes = [];
    $(".filyerPaymentType input[type='checkbox']").prop("checked", false);
    $("#search").val("");
    currentSearch = "";
    initTable();
});

function initTable() {
    $(".table-loader").show();
    $(".input-loader").show();
    $(".real-table, .real-data").addClass("d-none");
    $.ajax({
        url: "/admin/payments-info",
        type: "POST",
        data: {
            search: currentSearch,
            sortby: currentSortby,
            payment_status: selectedStatuses,
            payment_type: selectedPaymentTypes,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            let tableBody = "";
            if ($.fn.DataTable.isDataTable("#paymentInfoData")) {
                $("#paymentInfoData").DataTable().destroy();
            }

            if (response.code === 200 && response.data.length > 0) {
                let data = response.data;

                $.each(data, function (index, value) {
                    tableBody += `<tr>
                            <td>${value.id}</td>
                            <td>
                            <div class="d-flex align-items-center">
                                <a href="customer-details.html" class="avatar me-2 flex-shrink-0">
                                    <img src="/backend/assets/img/profiles/avatar-20.jpg" class="rounded-circle" alt="">
                                </a>
                                <h6><a href="customer-details.html" class="fs-14 fw-semibold">${
                                    value.name
                                }</a></h6>
                            </div>
                            </td>
                            <td>${response.currency_symbol ?? '$'}${value.amount}</td>
                            <td>${value.payment_type}</td>
                            <td>${value.created_at}</td>
                            <td>
    <span class="badge
        ${
            value.payment_status == 1
                ? "badge-info-transparent"
                : value.payment_status == 2
                ? "badge-success-transparent"
                : value.payment_status == 3
                ? "badge-warning-transparent"
                : "badge-danger-transparent"
        }
        d-inline-flex align-items-center badge-sm">
        <i class="ti ti-point-filled me-1"></i>
        ${
            value.payment_status == 1
                ? `${_l("admin.finance_accounts.open")}`
                : value.payment_status == 2
                ? `${_l("admin.finance_accounts.completed")}`
                : value.payment_status == 3
                ? `${_l("admin.finance_accounts.pending")}`
                : `${_l("admin.finance_accounts.closed")}`
        }
    </span>
</td>
                        </tr>`;
                });
            } else {
                tableBody += `
                        <tr>
                            <td colspan="8" class="text-center">${_l("admin.common.empty_table")}</td></td>
                        </tr>`;
                $(".table-footer").empty();
            }

            $("#paymentInfoData tbody").html(tableBody);
            if (response.data.length > 0) {
                $("#paymentInfoData").DataTable({
                    ordering: true,
                    searching: false,
                    pageLength: 10,
                    lengthChange: false,
                    "drawCallback": function () {
                        $(".dataTables_info").addClass('d-none');
                        $(".dataTables_wrapper .dataTables_paginate").addClass('d-none');

                        var tableWrapper = $(this).closest('.dataTables_wrapper');
                        var info = tableWrapper.find('.dataTables_info');
                        var pagination = tableWrapper.find('.dataTables_paginate');

                        $('.table-footer').empty()
                            .append($('<div class="d-flex justify-content-between align-items-center w-100"></div>')
                                .append($('<div class="datatable-info"></div>').append(info.clone(true)))
                                .append($('<div class="datatable-pagination"></div>').append(pagination.clone(true)))
                            );
                        $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
                    },
                    language: {
                        emptyTable: _l("admin.common.empty_table"),
                        info: _l("admin.common.showing") + " _START_ " + _l("admin.common.to") + " _END_ " + _l("admin.common.of") + " _TOTAL_ " + _l("admin.common.entries"),
                        infoEmpty: _l("admin.common.showing") + " 0 " + _l("admin.common.to") + " 0 " + _l("admin.common.of") + " 0 " + _l("admin.common.entries"),
                        infoFiltered: "(" + _l("admin.common.filtered_from") + " _MAX_ " + _l("admin.common.total_entries") + ")",
                        lengthMenu: _l("admin.common.show") + " _MENU_ " + _l("admin.common.entries"),
                        search: _l("admin.common.search") + ":",
                        zeroRecords: _l("admin.common.no_matching_records"),
                        paginate: {
                            first: _l("admin.common.first"),
                            last: _l("admin.common.last"),
                            next: _l("admin.common.next"),
                            previous: _l("admin.common.previous"),
                        },
                    },
                });
            }
        },
        error: function (error) {
            if (error.responseJSON.code === 500) {
                showToast('error', error.responseJSON.message);
            } else {
                showToast('error', _l('admin.common.default_retrieve_error'));
            }
        },
        complete: function () {
            $(".table-loader").hide();
            $(".label-loader, .input-loader").hide();
            $(".real-label, .real-table, .real-data").removeClass("d-none");
        },
    });
}


}) ();
