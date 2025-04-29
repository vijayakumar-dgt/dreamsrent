(async () => {
    "use strict";
    await loadTranslationFile('admin', 'blog, common');

    $('#blogCommentTable').DataTable({
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
            if ($("#blogCommentTable").length === 0) {
                $(".table-footer").addClass("d-none");
            } else {
                $(".table-footer").removeClass("d-none");
            }
        }
    });

})();

document.addEventListener('DOMContentLoaded', function () {
    const filterItems = document.querySelectorAll('.dropdown-menu .dropdown-item');
    const tableRows = document.querySelectorAll('.custom-blog-table tbody tr');
    const filterText = document.getElementById('filterText');

    filterItems.forEach(item => {
        item.addEventListener('click', function () {
            const selected = this.textContent.trim();
            filterText.textContent = selected;

            tableRows.forEach(row => row.style.display = '');

            switch (selected) {
                case 'Ascending':
                    sortTable(1, true);
                    break;
                case 'Desending':
                    sortTable(1, false);
                    break;
                case 'Last Month':
                    filterByDateRange(30);
                    break;
                case 'Last 7 Days':
                    filterByDateRange(7);
                    break;
                case 'Latest':
                default:
                    sortTable(1, false);
                    break;
            }
        });
    });

    function sortTable(columnIndex, ascending = true) {
        const rowsArray = Array.from(tableRows);
        rowsArray.sort((a, b) => {
            const dateA = new Date(a.children[columnIndex].textContent.trim());
            const dateB = new Date(b.children[columnIndex].textContent.trim());
            return ascending ? dateA - dateB : dateB - dateA;
        });

        const tbody = document.querySelector('.custom-blog-table tbody');
        tbody.innerHTML = '';
        rowsArray.forEach(row => tbody.appendChild(row));
    }

    function filterByDateRange(days) {
        const now = new Date();
        const pastDate = new Date();
        pastDate.setDate(now.getDate() - days);

        tableRows.forEach(row => {
            const dateText = row.children[1].textContent.trim();
            const rowDate = new Date(dateText);
            row.style.display = (rowDate >= pastDate && rowDate <= now) ? '' : 'none';
        });
    }
});


document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('tableSearch');
    const tableRows = document.querySelectorAll('.custom-blog-table tbody tr');

    searchInput.addEventListener('keyup', function () {
        const query = this.value.toLowerCase();

        tableRows.forEach(row => {
            const textContent = row.textContent.toLowerCase();
            row.style.display = textContent.includes(query) ? '' : 'none';
        });
    });
});
