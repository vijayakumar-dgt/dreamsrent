/* global $, loadTranslationFile, window, document, showToast, _l, FormData */
(function () {
    "use strict";

    (async () => {
        "use strict";

        await loadTranslationFile("admin", "common, finance_accounts");

        const table = $("#linkReservationTable").DataTable({
            ordering: false,
            searching: false,
            pageLength: 10,
            lengthChange: false,
            drawCallback: function () {
                customizeTableFooter($(this));
            },
            language: getDataTableLanguage(),
            initComplete: function () {
                $(".table-loader, .input-loader, .label-loader").hide();
                $(".real-table, .real-label, .real-input").removeClass("d-none");
                if ($("#linkReservationTable").length === 0) {
                    $(".table-footer").addClass("d-none");
                } else {
                    $(".table-footer").removeClass("d-none");
                }
            }
        });
    })();

    function calculateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll(".total").forEach(function (input) {
            const value = parseFloat(input.value) || 0;
            grandTotal += value;
        });

        const formattedTotal = `$${grandTotal.toFixed(2)}`;
        document.getElementById("grand-total").innerText = formattedTotal;
        document.getElementById("sub-total").innerText = formattedTotal;
        document.getElementById("grand-total-value").value = grandTotal.toFixed(2);
        document.getElementById("subtotal-value").value = grandTotal.toFixed(2);
    }

    function bindEvents(row) {
        const qty = row.querySelector(".qty");
        const price = row.querySelector(".price");
        const total = row.querySelector(".total");

        function calculate() {
            const qtyVal = parseFloat(qty.value) || 0;
            const priceVal = parseFloat(price.value) || 0;
            total.value = (qtyVal * priceVal).toFixed(2);
            calculateGrandTotal();
        }

        if (qty && price && total) {
            qty.addEventListener("input", calculate);
            price.addEventListener("input", calculate);
        }

        const deleteBtn = row.querySelector(".delete-row");
        if (deleteBtn) {
            deleteBtn.addEventListener("click", function () {
                row.remove();
                calculateGrandTotal();
            });
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll("#rental-details-body tr").forEach(function (row) {
            bindEvents(row);
        });
        calculateGrandTotal();
    });

    let rowIndex = 1;

    function createRow(index) {
        return (
            "<td class=\"pe-0\"><div><input type=\"text\" name=\"items[" + index + "][description]\" class=\"form-control\"></div></td>" +
            "<td class=\"pe-0\"><div><input type=\"number\" name=\"items[" + index + "][qty]\" class=\"form-control qty\"></div></td>" +
            "<td class=\"pe-0\"><div><input type=\"number\" name=\"items[" + index + "][price]\" class=\"form-control price\"></div></td>" +
            "<td class=\"pe-0\"><div><input type=\"number\" name=\"items[" + index + "][tax]\" class=\"form-control\"></div></td>" +
            "<td class=\"pe-0\"><div><input type=\"number\" name=\"items[" + index + "][total_price]\" class=\"form-control total\" readonly></div></td>" +
            "<td><div><a href=\"javascript:void(0);\" class=\"btn btn-icon btn-sm text-danger delete-row\"><i class=\"ti ti-trash\"></i></a></div></td>"
        );
    }

    document.getElementById("addMoreRow").addEventListener("click", function () {
        const tbody = document.getElementById("rental-details-body");
        const newRow = document.createElement("tr");

        newRow.innerHTML = createRow(rowIndex);
        rowIndex += 1;

        tbody.appendChild(newRow);
        bindEvents(newRow);
    });

    // Bind events to existing rows initially present in DOM
    document.querySelectorAll("#rental-details-body tr").forEach(function (row) {
        bindEvents(row);
    });

    // Delegate event to handle dynamically added rows
    document.getElementById("rental-details-body").addEventListener("click", function (e) {
        const deleteBtn = e.target.closest(".delete-row");
        if (deleteBtn) {
            deleteBtn.closest("tr").remove();
        }
    });

    $(document).ready(function () {
        // Set up CSRF token
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $("meta[name=\"csrf-token\"]").attr("content")
            }
        });

        // Form submit via AJAX
        $("#invoiceAdd").on("submit", function (e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);

            $.ajax({
                url: $(form).attr("action"),
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    // Optional loader can be added here
                },
                success: function () {
                    showToast("success", "Invoice Created!");
                    window.location.href = "/admin/invoices";
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let message = "";
                        $.each(errors, function (key, value) {
                            message += value + "\n";
                        });
                        showToast("warning", message);
                    } else {
                        showToast("error", "An error occurred while saving the invoice.");
                    }
                }
            });
        });

        $("#invoiceEdit").on("submit", function (e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);
            const keysToDelete = [];
            for (const key of formData.keys()) {
                if (key.startsWith("items")) {
                    keysToDelete.push(key);
                }
            }
            keysToDelete.forEach(function (key) {
                formData.delete(key);
            });

            // Build items array manually
            const items = [];
            $("#rental-details-body tr").each(function () {
                const description = $(this).find("input[name*=\"[description]\"]").val();
                const qty = $(this).find("input[name*=\"[qty]\"]").val();
                const price = $(this).find("input[name*=\"[price]\"]").val();
                const tax = $(this).find("input[name*=\"[tax]\"]").val();
                const total_price = $(this).find("input[name*=\"[total_price]\"]").val();

                // Avoid pushing empty rows
                if (description || qty || price || tax || total_price) {
                    items.push({
                        description: description,
                        qty: qty,
                        price: price,
                        tax: tax,
                        total_price: total_price
                    });
                }
            });

            // Append items as JSON string
            formData.append("items", JSON.stringify(items));

            const invoiceId = $("input[name=\"id\"]").val();

            $.ajax({
                url: "/../admin/update-invoice/" + invoiceId,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function () {
                    showToast("success", "Invoice Updated!");
                    window.location.href = "/admin/invoices";
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let message = "";
                        $.each(errors, function (key, value) {
                            message += value + "\n";
                        });
                        showToast("warning", message);
                    } else {
                        showToast("error", "An error occurred while saving the invoice.");
                    }
                }
            });
        });
    });

    $(document).ready(function () {
        let itemIndex = 1;

        $(document).on("click", ".booking-row", function () {
            $("#link_reservation").modal("hide");

            // Remove any row that has all blank or zero values
            $("#rental-details-body tr").each(function () {
                const description = $(this).find("input[name*=\"[description]\"]").val();
                const price = parseFloat($(this).find("input[name*=\"[price]\"]").val()) || 0;
                const tax = parseFloat($(this).find("input[name*=\"[tax]\"]").val()) || 0;
                const total = parseFloat($(this).find("input[name*=\"[total_price]\"]").val()) || 0;

                if (!description && price === 0 && tax === 0 && total === 0) {
                    $(this).remove();
                }
            });

            const vehicle = $(this).data("vehicle");
            const price = parseFloat($(this).data("price")) || 0;
            const tax = parseFloat($(this).data("tax")) || 0;
            const total = parseFloat($(this).data("final_price")) || 0;

            const row = "<tr>" +
                "<td class=\"pe-0\">" +
                "<div><input type=\"text\" name=\"items[" + itemIndex + "][description]\" class=\"form-control\" value=\"" + vehicle + "\"></div>" +
                "</td>" +
                "<td class=\"pe-0\">" +
                "<div><input type=\"number\" name=\"items[" + itemIndex + "][qty]\" class=\"form-control qty\" value=\"0\" readonly></div>" +
                "</td>" +
                "<td class=\"pe-0\">" +
                "<div><input type=\"number\" name=\"items[" + itemIndex + "][price]\" class=\"form-control price\" value=\"" + price + "\" readonly></div>" +
                "</td>" +
                "<td class=\"pe-0\">" +
                "<div><input type=\"number\" name=\"items[" + itemIndex + "][tax]\" class=\"form-control\" value=\"" + tax + "\" readonly></div>" +
                "</td>" +
                "<td class=\"pe-0\">" +
                "<div><input type=\"number\" name=\"items[" + itemIndex + "][total_price]\" class=\"form-control total\" value=\"" + total + "\" readonly></div>" +
                "</td>" +
                "<td>" +
                "<div><a href=\"javascript:void(0);\" class=\"btn btn-icon btn-sm text-danger delete-row\"><i class=\"ti ti-trash\"></i></a></div>" +
                "</td>" +
                "</tr>";

            $("#rental-details-body").append(row);
            if (typeof calculateGrandTotal === "function") {
                calculateGrandTotal();
            }
            itemIndex += 1;
        });

        $(document).on("click", ".delete-row", function () {
            $(this).closest("tr").remove();
        });

        // Initialize datetimepicker
        $(".datetimepicker").datetimepicker({
            format: "DD/MM/YYYY",
            minDate: new Date()
        });

        // Generate invoice number
        const timestamp = Math.floor(Date.now() / 1000);
        const invoiceNumber = "INV-" + timestamp;
        $("#invoice_number").val(invoiceNumber);
    });
})();