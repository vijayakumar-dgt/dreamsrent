(async () => {
    "use strict";
    await loadTranslationFile('admin', 'common, finance_accounts');

    $('#linkReservationTable').DataTable({
        ordering: false,
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
            emptyTable: _l("admin.common.no_matching_records"),
            info: _l("admin.common.showing") + " _START_ " + _l("admin.common.to") + " _END_ " + _l("admin.common.of") + " _TOTAL_ " + _l("admin.common.entries"),
            infoEmpty: _l("admin.common.showing") + " 0 " + _l("admin.common.to") + " 0 " + _l("admin.common.of") + " 0 " + _l("admin.common.entries"),
            infoFiltered: "(" + _l("admin.common.filtered_from") + " _MAX_ " + _l("admin.common.total_entries") + ")",
            lengthMenu: _l("admin.common.show") + " _MENU_ " + _l("admin.common.entries"),
            search: _l("admin.common.search") + ":",
            zeroRecords: _l("admin.common.empty_table"),
            paginate: {
                first: _l("admin.common.first"),
                last: _l("admin.common.last"),
                next: _l("admin.common.next"),
                previous: _l("admin.common.previous"),
            },
        },
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
    document.querySelectorAll('.total').forEach(input => {
        const value = parseFloat(input.value) || 0;
        grandTotal += value;
    });
    document.getElementById('grand-total').innerText = `$${grandTotal.toFixed(2)}`;
    document.getElementById('sub-total').innerText = `$${grandTotal.toFixed(2)}`;

    document.getElementById('grand-total-value').value = `${grandTotal.toFixed(2)}`;
    document.getElementById('subtotal-value').value = `${grandTotal.toFixed(2)}`;
}

function bindEvents(row) {
    const qty = row.querySelector('.qty');
    const price = row.querySelector('.price');
    const total = row.querySelector('.total');

    const calculate = () => {
        const qtyVal = parseFloat(qty.value) || 0;
        const priceVal = parseFloat(price.value) || 0;
        total.value = (qtyVal * priceVal).toFixed(2);
        calculateGrandTotal();
    };

    if (qty && price && total) {
        qty.addEventListener('input', calculate);
        price.addEventListener('input', calculate);
    }

    const deleteBtn = row.querySelector('.delete-row');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', () => {
            row.remove();
            calculateGrandTotal();
        });
    }
}

// On page load
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('#rental-details-body tr').forEach(row => bindEvents(row));
    calculateGrandTotal();
});

let rowIndex = 1; // Start from 1 since there's already 1 row in HTML

function createRow(index) {
    return `
        <td class="pe-0"><div><input type="text" name="items[${index}][description]" class="form-control"></div></td>
        <td class="pe-0"><div><input type="number" name="items[${index}][qty]" class="form-control qty"></div></td>
        <td class="pe-0"><div><input type="number" name="items[${index}][price]" class="form-control price"></div></td>
        <td class="pe-0"><div><input type="number" name="items[${index}][tax]" class="form-control"></div></td>
        <td class="pe-0"><div><input type="number" name="items[${index}][total_price]" class="form-control total" readonly></div></td>
        <td><div><a href="javascript:void(0);" class="btn btn-icon btn-sm text-danger delete-row"><i class="ti ti-trash"></i></a></div></td>
    `;
}

document.getElementById('addMoreRow').addEventListener('click', function () {
    const tbody = document.getElementById('rental-details-body');
    const newRow = document.createElement('tr');

    newRow.innerHTML = createRow(rowIndex);
    rowIndex++;

    tbody.appendChild(newRow);
    bindEvents(newRow);
});



// Bind to existing row initially present in DOM
document.querySelectorAll('#rental-details-body tr').forEach(row => bindEvents(row));

// Delegate event to handle dynamically added rows
document.getElementById('rental-details-body').addEventListener('click', function (e) {
    if (e.target.closest('.delete-row')) {
        e.target.closest('tr').remove();
    }
});


$(document).ready(function () {

    // Set up CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Form submit via AJAX
    $('#invoiceAdd').on('submit', function (e) {
        e.preventDefault();

        let form = $(this)[0];
        let formData = new FormData(form);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                // You can show a loader here
            },
            success: function (response) {
                // Handle success (customize as needed)
                showToast("success", "Invoice Created!");
                window.location.href = "/admin/invoices";
            },
            error: function (xhr) {
                // Handle validation errors
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let message = '';
                    $.each(errors, function (key, value) {
                        message += value + '\n';
                    });
                    showToast("warning", message);
                } else {
                    showToast("error", "An error occurred while saving the invoice.");
                }
            }
        });
    });


    $('#invoiceEdit').on('submit', function (e) {
        e.preventDefault();
    
        let form = $(this)[0];
        let formData = new FormData(form);
    
        // Remove existing item-related keys (optional safety cleanup)
        for (let key of formData.keys()) {
            if (key.startsWith('items')) {
                formData.delete(key);
            }
        }
    
        // Build items array manually
        let items = [];
        $('#rental-details-body tr').each(function () {
            let description = $(this).find('input[name*="[description]"]').val();
            let qty = $(this).find('input[name*="[qty]"]').val();
            let price = $(this).find('input[name*="[price]"]').val();
            let tax = $(this).find('input[name*="[tax]"]').val();
            let total_price = $(this).find('input[name*="[total_price]"]').val();
    
            // Avoid pushing empty rows (optional)
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
        formData.append('items', JSON.stringify(items));
    
        let invoiceId = $('input[name="id"]').val();
    
        $.ajax({
            url: `/../admin/update-invoice/${invoiceId}`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                showToast("success", "Invoice Updated!");
                window.location.href = "/admin/invoices";
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let message = '';
                    $.each(errors, function (key, value) {
                        message += value + '\n';
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
    let itemIndex = 1; // Start from 1 if the first row is 0

    $(document).on('click', '.booking-row', function () {
        $("#link_reservation").modal("hide");

        // Remove any row that has all blank or zero values
        $('#rental-details-body tr').each(function () {
            const description = $(this).find('input[name*="[description]"]').val();
            const price = parseFloat($(this).find('input[name*="[price]"]').val()) || 0;
            const tax = parseFloat($(this).find('input[name*="[tax]"]').val()) || 0;
            const total = parseFloat($(this).find('input[name*="[total_price]"]').val()) || 0;

            if (!description && price == '' && tax == '' && total == '') {
                $(this).remove();
            }
        });

        const vehicle = $(this).data('vehicle');
        const price = parseFloat($(this).data('price')) || 0;
        const tax = parseFloat($(this).data('tax')) || 0;
        const total = parseFloat($(this).data('final_price')) || 0;

        let row = `
            <tr>
                <td class="pe-0">
                    <div><input type="text" name="items[${itemIndex}][description]" class="form-control" value="${vehicle}"></div>
                </td>
                <td class="pe-0">
                    <div><input type="number" name="items[${itemIndex}][qty]" class="form-control qty" value="0" readonly></div>
                </td>
                <td class="pe-0">
                    <div><input type="number" name="items[${itemIndex}][price]" class="form-control price" value="${price}" readonly></div>
                </td>
                <td class="pe-0">
                    <div><input type="number" name="items[${itemIndex}][tax]" class="form-control" value="${tax}" readonly></div>
                </td>
                <td class="pe-0">
                    <div><input type="number" name="items[${itemIndex}][total_price]" class="form-control total" value="${total}" readonly></div>
                </td>
                <td>
                    <div><a href="javascript:void(0);" class="btn btn-icon btn-sm text-danger delete-row"><i class="ti ti-trash"></i></a></div>
                </td>
            </tr>
        `;

        $('#rental-details-body').append(row);
        calculateGrandTotal(); // Assuming this exists
        itemIndex++;
    });

    // Delete row on trash icon click
    $(document).on('click', '.delete-row', function () {
        $(this).closest('tr').remove();
    });
});

$(document).ready(function () {
    $(function () {
        $('.datetimepicker').datetimepicker({
            format: 'DD/MM/YYYY',
            minDate: new Date()
        });
    });
    const timestamp = Math.floor(Date.now() / 1000); // current UNIX timestamp
    const invoiceNumber = 'INV-' + timestamp;
    $('#invoice_number').val(invoiceNumber);
});


