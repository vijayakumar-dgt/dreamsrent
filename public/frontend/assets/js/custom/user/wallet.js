/* global $, loadTranslationFile, bootstrap, window, document, showToast, _l */
(async () => {
    "use strict";

    await loadTranslationFile("web", "user,common");

    const walletAmountInput = document.getElementById("wallet_amount");
    const addWalletForm = $("#add_wallet");

    // Limit wallet input to 5 characters
    walletAmountInput.addEventListener("input", () => {
        walletAmountInput.value = walletAmountInput.value.slice(0, 5);
    });

    $(document).ready(() => {
        initializeWalletTable();

        // Open wallet modal
        $(".open-wallet-modal").on("click", () => {
            const amount = walletAmountInput.value;

            if (amount && parseFloat(amount) >= 50) {
                $("#add_payment input[name='wallet_amount']").val(amount);
                const modal = new bootstrap.Modal(document.getElementById("add_payment"));
                modal.show();
            } else {
                showToast("error", _l("web.user.amount_must_be_greater_than_50"));
            }
        });

        // Submit wallet form
        addWalletForm.on("submit", (event) => {
            event.preventDefault();

            const amount = walletAmountInput.value;
            const paymentType = $("input[name='payment_one']:checked").attr("id");

            if (!amount || !paymentType) {
                showToast("error", _l("web.user.enter_amount_and_select_payment_method"));
                return;
            }

            $.post("/user/addwallet", {
                wallet_amount: amount,
                payment_type: paymentType,
                _token: $("meta[name='csrf-token']").attr("content")
            })
                .done((response) => {
                    if (response.code === 200) {
                        showToast("success", response.message);
                        if (response.paypal_url) {
                            window.location.href = response.paypal_url;
                        } else if (response.stripe_url) {
                            window.location.href = response.stripe_url;
                        }
                        $("#add_payment").modal("hide");
                    } else {
                        showToast("error", response.message);
                    }
                })
                .fail((xhr) => {
                    let errorMessage = _l("web.user.something_went_wrong");

                    if (xhr?.responseJSON?.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr?.responseText) {
                        try {
                            const parsed = JSON.parse(xhr.responseText);
                            if (parsed.message) {
                                errorMessage = parsed.message;
                            }
                        } catch {
                            // Ignore JSON parse error
                        }
                    }

                    showToast("error", errorMessage);
                });
        });
    });

    function ucfirst(str) {
        if (!str) {
            return "";
        }
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function initializeWalletTable() {
        $.ajax({
            type: "GET",
            url: "/user/wallet-list",
            dataType: "json",
            beforeSend: () => {
                $(".table-loader").removeClass("d-none");
                $(".real-table").addClass("d-none");
            },
            success: (response) => {
                const currencySymbol = response.currency_symbol || "$";

                $(".total_credit").text(`${currencySymbol}${parseFloat(response.total_credit).toFixed(2)}`);
                $(".total_debit").text(`${currencySymbol}${parseFloat(response.total_debit).toFixed(2)}`);
                $(".available_balance").text(`${currencySymbol}${parseFloat(response.total_balance).toFixed(2)}`);

                const $tbody = $("#walletTable").find("tbody");
                $tbody.empty();

                if (response.data.length) {
                    response.data.forEach((value) => {
                        const row = $("<tr>");

                        row.append($("<td>").text(`#${value.id || "N/A"}`));

                        const paymentLink = $("<a>", {
                            href: "javascript:void(0);",
                            class: "mb-0"
                        }).text(ucfirst(value.payment_type));

                        const paymentTypeCell = $("<td>").append(
                            $("<div>").addClass("table-avatar").append(
                                $("<div>").addClass("table-head-name flex-grow-1").append(paymentLink)
                            )
                        );
                        row.append(paymentTypeCell);

                        row.append($("<td>").text(value.formatted_created_at));

                        const statusClass = value.status === "Completed" ? "success" : "danger";
                        const prefix = value.status === "Completed" ? "+ " : "- ";
                        const amountCell = $("<td>")
                            .addClass(`text-${statusClass}-light`)
                            .text(`${prefix}${currencySymbol}${value.amount}`);
                        row.append(amountCell);

                        const badge = $("<span>")
                            .addClass(`badge badge-light-${statusClass}`)
                            .text(value.status);
                        row.append($("<td>").append(badge));

                        $tbody.append(row);
                    });

                    $("#walletTable").DataTable({
                        ordering: false,
                        searching: false,
                        pageLength: 10,
                        lengthChange: false,
                        destroy: true,
                        drawCallback: function () {
                            $(".dataTables_info").addClass("d-none");
                            $(".dataTables_wrapper .dataTables_paginate").addClass("d-none");

                            const tableWrapper = $(this).closest(".dataTables_wrapper");
                            const info = tableWrapper.find(".dataTables_info");
                            const pagination = tableWrapper.find(".dataTables_paginate");

                            $(".table-footer").empty().append(
                                $("<div>", { class: "d-flex justify-content-between align-items-center w-100" }).append(
                                    $("<div>", { class: "datatable-info" }).append(info.clone(true)),
                                    $("<div>", { class: "datatable-pagination" }).append(pagination.clone(true))
                                )
                            );
                            $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
                        }
                    });

                } else {
                    $tbody.append(
                        $("<tr>").append(
                            $("<td>", {
                                colspan: 5,
                                class: "text-center"
                            }).text(_l("web.common.empty_table"))
                        )
                    );
                    $(".table-footer").empty();
                }
            },
            error: (error) => {
                const errorMessage = error?.responseJSON?.error || _l("web.user.errot_occured_while_retrieving_wallet_history");
                showToast("error", errorMessage);
            },
            complete: () => {
                $(".table-loader").addClass("d-none");
                $(".real-table").removeClass("d-none");
            }
        });
    }
})();