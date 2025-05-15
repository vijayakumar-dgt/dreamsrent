(async () => {
    "use strict";

    await loadTranslationFile('web', 'user,common');

    const walletAmountInput = document.getElementById('wallet_amount');
    const addWalletForm = $("#add_wallet");
    const walletTableElement = $("#walletTable");

    walletAmountInput.addEventListener('input', () => {
        walletAmountInput.value = walletAmountInput.value.slice(0, 5);
    });

    $(document).ready(() => {
        initializeWalletTable();

        $(".wallet-btn a").on("click", () => {
            const walletAmount = walletAmountInput.value;

            if (walletAmount && parseFloat(walletAmount) >= 50) {
                $("#add_payment input[name='wallet_amount']").val(walletAmount);
            } else {
                showToast('error', _l('web.user.amount_must_be_greater_than_50'));
            }
        });

        addWalletForm.on("submit", (event) => {
            event.preventDefault();

            const walletAmount = walletAmountInput.value;
            const paymentType = $("input[name='payment_one']:checked").attr("id");

            if (!walletAmount || !paymentType) {
                showToast('error', _l('web.user.enter_amount_and_select_payment_method'));
                return;
            }

            $.post("/user/addwallet", {
                wallet_amount: walletAmount,
                payment_type: paymentType,
                _token: $('meta[name="csrf-token"]').attr("content"),
            })
                .done((response) => {
                    if (response.code === 200) {
                        showToast('success', response.message);
                        if (response.paypal_url) window.location.href = response.paypal_url;
                        else if (response.stripe_url) window.location.href = response.stripe_url;
                        $("#add_payment").modal("hide");
                    } else {
                        showToast('error', response.message);
                    }
                })
               .fail((xhr) => {
                    let errorMessage = _l('web.user.something_went_wrong');
                    if (xhr?.responseJSON?.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr?.responseText) {
                        try {
                            const parsed = JSON.parse(xhr.responseText);
                            if (parsed.message) {
                                errorMessage = parsed.message;
                            }
                        } catch (e) {
                        }
                    }
                    showToast('error', errorMessage);
                });
        });
    });

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
               const currencySymbol = response.currency_symbol || '$';
                $(".total_credit").text(`${currencySymbol}${parseFloat(response.total_credit).toFixed(2)}`);
                $(".total_debit").text(`${currencySymbol}${parseFloat(response.total_debit).toFixed(2)}`);
                $(".available_balance").text(`${currencySymbol}${parseFloat(response.total_balance).toFixed(2)}`);

                const tableBody = response.data.length
                    ? response.data.map((value) => `
                        <tr>
                            <td>#${value.id || 'N/A'}</td>
                            <td>
                                <div class="table-avatar">
                                    <div class="table-head-name flex-grow-1">
                                        <a href="javascript:void(0);" class="mb-0">${ucfirst(value.payment_type)}</a>
                                    </div>
                                </div>
                            </td>
                            <td>${new Date(value.transaction_date).toLocaleString()}</td>
                            <td class="text-${value.status === 'Completed' ? 'success' : 'danger'}-light">
                                ${value.status === 'Completed' ? '+ ' : '- '} ${currencySymbol}${value.amount}
                            </td>
                            <td>
                                <span class="badge badge-light-${value.status === 'Completed' ? 'success' : 'danger'}">
                                    ${value.status}
                                </span>
                            </td>
                        </tr>`).join('')
                    : `<tr>
                        <td colspan="5" class="text-center">${_l('web.user.no_wallet_transaction_available')}</td>
                    </tr>`;

                walletTableElement.find("tbody").html(tableBody);

                if (response.data.length) {
                    walletTableElement.DataTable({
                        ordering: false,
                        searching: false,
                        pageLength: 10,
                        lengthChange: false,
                        drawCallback: function() {
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
                        }
                    });
                } else {
                    $('.table-footer').empty();
                }
           },
           error: (error) => {
               const errorMessage = error.responseJSON?.error || _l('web.user.errot_occured_while_retrieving_wallet_history');
                showToast('error', errorMessage);
           },
           complete: () => {
               $(".table-loader").addClass("d-none");
               $(".real-table").removeClass("d-none");
           }
        });
    }
})();
