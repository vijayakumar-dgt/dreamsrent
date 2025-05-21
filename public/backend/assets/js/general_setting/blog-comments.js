(async () => {
    "use strict";
    await loadTranslationFile("admin", "blog, common");

    let blogCommentTable = $("#blogCommentTable").DataTable({
        ordering: true,
        searching: true,
        pageLength: 10,
        lengthChange: false,
        drawCallback: function () {
            $(".dataTables_filter").hide();
            $(".dataTables_info").addClass("d-none");
            $(".dataTables_wrapper .dataTables_paginate").addClass("d-none");

            var tableWrapper = $(this).closest(".dataTables_wrapper");
            var info = tableWrapper.find(".dataTables_info");
            var pagination = tableWrapper.find(".dataTables_paginate");

            $(".table-footer")
                .empty()
                .append(
                    $(
                        '<div class="d-flex justify-content-between align-items-center w-100"></div>'
                    )
                        .append(
                            $('<div class="datatable-info"></div>').append(
                                info.clone(true)
                            )
                        )
                        .append(
                            $(
                                '<div class="datatable-pagination"></div>'
                            ).append(pagination.clone(true))
                        )
                );
            $(".table-footer")
                .find(".dataTables_paginate")
                .removeClass("d-none");
        },
        language: {
            emptyTable: _l("admin.common.empty_table"),
            info:
                _l("admin.common.showing") +
                " _START_ " +
                _l("admin.common.to") +
                " _END_ " +
                _l("admin.common.of") +
                " _TOTAL_ " +
                _l("admin.common.entries"),
            infoEmpty:
                _l("admin.common.showing") +
                " 0 " +
                _l("admin.common.to") +
                " 0 " +
                _l("admin.common.of") +
                " 0 " +
                _l("admin.common.entries"),
            infoFiltered:
                "(" +
                _l("admin.common.filtered_from") +
                " _MAX_ " +
                _l("admin.common.total_entries") +
                ")",
            lengthMenu:
                _l("admin.common.show") +
                " _MENU_ " +
                _l("admin.common.entries"),
            search: _l("admin.common.search") + ":",
            zeroRecords: _l("admin.common.no_matching_records"),
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
            if ($("#blogCommentTable").DataTable().rows().count() == 0) {
                $(".table-footer").addClass("d-none");
            } else {
                $(".table-footer").removeClass("d-none");
            }
        },
    });

    $("#tableSearch").on("keyup", function () {
        blogCommentTable.search(this.value).draw();
    });
    document.addEventListener("DOMContentLoaded", function () {
    const filterItems = document.querySelectorAll(
        ".dropdown-menu .dropdown-item"
    );
    const tableRows = document.querySelectorAll(".custom-blog-table tbody tr");
    const filterText = document.getElementById("filterText");

    filterItems.forEach((item) => {
        item.addEventListener("click", function () {
            const selected = this.textContent.trim();
            filterText.textContent = selected;

            const table = $("#blogCommentTable").DataTable();

            switch (selected) {
                case _l("admin.blog.ascending"):
                    table.order([1, "asc"]).draw();
                    break;
                case _l("admin.blog.descending"):
                    table.order([1, "desc"]).draw();
                    break;
                case _l("admin.blog.last_month"):
                    filterByDateRange(30, table);
                    break;
                case _l("admin.blog.last_7_days"):
                    filterByDateRange(7, table);
                    break;
                case _l("admin.blog.latest"):
                default:
                    table.order([1, "desc"]).draw();
                    break;
            }
        });
    });

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        if (!window.customDateFilterDays) return true;

        const dateText = data[1]; // second column
        const rowDate = new Date(dateText);
        const now = new Date();
        const pastDate = new Date();
        pastDate.setDate(now.getDate() - window.customDateFilterDays);

        return rowDate >= pastDate && rowDate <= now;
    });

    function filterByDateRange(days, table) {
        window.customDateFilterDays = days;
        table.draw();
    }
});

})();