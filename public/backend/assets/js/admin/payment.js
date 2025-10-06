/* global $, loadTranslationFile, document, showToast, _l */
(async () => {
    "use strict";
    await loadTranslationFile("admin", "common, finance_accounts");
    let selectedStatuses = [];
    let selectedPaymentTypes = [];
    let currentSearch = "";
    let currentSortby = "latest";

    $(document).ready(function () {
        initTable();
    });

    function initTable() {
        $("#paymentInfoData").DataTable({
            processing: false,
            serverSide: true,
            destroy: true,
            ajax: {
                url: "/admin/payments-info",
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content"),
                },
                data: function (d) {
                    d.search = currentSearch;
                    d.sortby = currentSortby;
                    d.payment_status = selectedStatuses;
                    d.payment_type = selectedPaymentTypes;
                },
                error: function (error) {
                    if (error.responseJSON?.code === 500) {
                        showToast("error", error.responseJSON.message);
                    } else {
                        showToast("error", _l("admin.common.default_retrieve_error"));
                    }
                },
                beforeSend: function () {
                    $(".table-loader").show();
                    $(".real-table, .table-footer").addClass("d-none");
                },
                complete: function () {
                    $(".table-loader").show();
                    $(".input-loader").show();
                    $(".real-table, .real-data").addClass("d-none");
                    $(".table-loader, .input-loader, .label-loader").hide();
                    $(".real-table, .real-label, .real-input").removeClass("d-none");

                    if ($("#paymentInfoData").DataTable().rows().count() === 0) {
                        $(".table-footer").addClass("d-none");
                    } else {
                        $(".table-footer").removeClass("d-none");
                    }
                },
            },
            columns: [
                { data: "id" },
                { data: "name",
                    render: function (data, type, row) {
                        return `
                            <div class="d-flex align-items-center">
                                <div class="avatar me-2 flex-shrink-0">
                                    <img src="${row.profile_image}" class="rounded-circle" alt="Image Preview">
                                </div>
                                <h6 class="fs-14 fw-semibold text-black">${row.name}</h6>
                            </div>`;
                    }
                },
                {
                    data: "final_price",
                    render: function (data, type, row) {
                        return (row.currency_symbol ?? "$") + row.amount;
                    }
                },
                { data: "payment_type" },
                { data: "created_at" },
                {
                    data: "payment_status",
                    render: function (data) {
                        let badgeClass, label;
                        switch (data) {
                            case 1:
                                badgeClass = "badge-info-transparent";
                                label = _l("admin.general_settings.open");
                                break;
                            case 2:
                                badgeClass = "badge-success-transparent";
                                label = _l("admin.common.completed");
                                break;
                            case 3:
                                badgeClass = "badge-warning-transparent";
                                label = _l("admin.rentals.pending");
                                break;
                            default:
                                badgeClass = "badge-danger-transparent";
                                label = _l("admin.finance_accounts.closed");
                        }
                        return `<span class="badge ${badgeClass} d-inline-flex align-items-center badge-sm">
                                    <i class="ti ti-point-filled me-1"></i>${label}
                                </span>`;
                    }
                }
            ],
            order: [[1, "desc"]],
            ordering: false,
            searching: false,
            pageLength: 10,
            lengthChange: false,
            responsive: false,
            autoWidth: false,
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
            drawCallback: function () {
                $(".dataTables_info").addClass("d-none");
                $(".dataTables_wrapper .dataTables_paginate").addClass("d-none");

                const tableWrapper = $(this).closest(".dataTables_wrapper");
                const info = tableWrapper.find(".dataTables_info");
                const pagination = tableWrapper.find(".dataTables_paginate");

                $(".table-footer")
                    .empty()
                    .append(
                        $("<div class='d-flex justify-content-between align-items-center w-100'></div>")
                            .append($("<div class='datatable-info'></div>").append(info.clone(true)))
                            .append($("<div class='datatable-pagination'></div>").append(pagination.clone(true)))
                    );
                $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
            },
        });
    }

    $("#search").on("input", function () {
        currentSearch = $(this).val().trim();
        initTable();
    });

    $(".sort-optionss").on("click", function () {
        $(".sort-optionss").removeClass("active");
        $(this).addClass("active");
        currentSortby = $(this).data("sort");
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
}) ();
