(function () {
    "use strict";

(async () => {

    await loadTranslationFile('admin', 'common, finance_accounts');

    $('#invoicesTable').DataTable({
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
            emptyTable: _l("admin.common.empty_table"),
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
            if ($("#invoicesTable").DataTable().rows().count() === 0) {
                $(".table-footer").addClass("d-none");
            } else {
                $(".table-footer").removeClass("d-none");
            }
        }
    });

})();
document.addEventListener("DOMContentLoaded", function () {
    let filters = {
        sort: null,
        status: null,
    };

    const rows = Array.from(document.querySelectorAll("#invoicesTable tbody tr"));

    document.querySelectorAll(".filter-option").forEach((item) => {
        item.addEventListener("click", function () {
            const type = this.getAttribute("data-type");
            const value = this.getAttribute("data-value");

            filters[type] = value; // Apply new filter, replacing old one of the same type
            filterTable();

            if (type === "status") {
                document.getElementById("statusDropdownBtn").innerHTML = `<i class="ti ti-badge me-1"></i> ${value.charAt(0).toUpperCase() + value.slice(1)
                    }`;
            }

            if (type === "sort") {
                const sortText = {
                    asc: "Ascending",
                    desc: "Descending",
                    latest: "Latest",
                    last_7_days: "Last 7 Days",
                    last_month: "Last Month",
                };

                document.getElementById("sortDropdownBtn").innerHTML = `<i class="ti ti-filter me-1"></i> Sort By : ${sortText[value] || "Latest"
                    }`;
            }
        });
    });

   function filterTable() {
    rows.forEach((row) => (row.style.display = "table-row")); // Reset all rows

    let filteredRows = [...rows];

    // Apply status filter
    if (filters.status) {
        filteredRows = filteredRows.filter((row) => {
            const status = row
                .querySelector("td:nth-child(7) span")
                .innerText.toLowerCase();
            return status.includes(filters.status);
        });
    }

    // Apply date range filter (for last_7_days and last_month)
    if (filters.sort === "last_7_days" || filters.sort === "last_month") {
        const now = new Date();
    
        filteredRows = filteredRows.filter((row) => {
            const createdAttr = row.getAttribute("data-created");
            const rowDate = new Date(createdAttr); // YYYY-MM-DD is safely parsable
    
            if (filters.sort === "last_7_days") {
                const sevenDaysAgo = new Date();
                sevenDaysAgo.setDate(now.getDate() - 7);
                return rowDate >= sevenDaysAgo && rowDate <= now;
            }
    
            if (filters.sort === "last_month") {
                const firstDayLastMonth = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                const lastDayLastMonth = new Date(now.getFullYear(), now.getMonth(), 0);
                return rowDate >= firstDayLastMonth && rowDate <= lastDayLastMonth;
            }
    
            return true;
        });
    
        // Sort after filter
        filteredRows.sort((a, b) => {
            const dateA = new Date(a.getAttribute("data-created"));
            const dateB = new Date(b.getAttribute("data-created"));
            return dateB - dateA;
        });
    }else if (filters.sort === "asc" || filters.sort === "desc") {
        // Sort by Invoice Number (column 1)
        filteredRows.sort((a, b) => {
            const valA = a.querySelector("td:nth-child(1)").innerText.replace("#", "").trim();
            const valB = b.querySelector("td:nth-child(1)").innerText.replace("#", "").trim();
            return filters.sort === "asc" ? valA.localeCompare(valB) : valB.localeCompare(valA);
        });
    } else if (filters.sort === "latest") {
        // Sort by Created Date (column 4) descending
        filteredRows.sort((a, b) => {
            const dateA = new Date(a.querySelector("td:nth-child(4) p").innerText);
            const dateB = new Date(b.querySelector("td:nth-child(4) p").innerText);
            return dateB - dateA;
        });
    }

    // Re-insert rows in filtered and sorted order
    const tbody = document.querySelector("#invoicesTable tbody");
    tbody.innerHTML = "";
    filteredRows.forEach((row) => tbody.appendChild(row));
}

    // Get the search input element and add event listener
    const searchInput = document.getElementById("searchInput");
    searchInput.addEventListener("input", filterSearchTable);

    function filterSearchTable() {
        const searchQuery = searchInput.value.toLowerCase(); // Get the search text and convert it to lowercase
        const rows = document.querySelectorAll("#invoicesTable tbody tr"); // Get all table rows

        // Loop through all rows and hide those that don't match the search query
        rows.forEach((row) => {
            let rowText = row.innerText.toLowerCase(); // Get all the text content of the row
            if (rowText.includes(searchQuery)) {
                row.style.display = ""; // Show the row if it matches the search query
            } else {
                row.style.display = "none"; // Hide the row if it doesn't match
            }
        });
    }
});


$(document).ready(function () {


    $(document).on('click', '#delete-invoice-btn', function () {
        const invoiceId = $(this).data('id');
        $('#delete_modal').data('id', invoiceId);
    });


    $(document).on('click', '#delete_modal .btn-primary', function () {
        const invoiceId = $('#delete_modal').data('id');
        $.ajax({
            url: '/admin/delete-invoices/' + invoiceId,
            type: 'GET',
            success: function (response) {
                if (response.success) {
                    showToast("success", response.message);
                    $("#delete_modal").modal("hide");
                    location.reload(); 
                } else {
                    showToast(response.message);
                }
            },
            error: function (xhr) {
                showToast("error", xhr.responseJSON.message);
            }
        });
    });
});
})();
