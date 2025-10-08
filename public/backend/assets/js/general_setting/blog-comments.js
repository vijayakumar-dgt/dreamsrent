(async () => {
    "use strict";
    await loadTranslationFile("admin", "blog, common");

    let blogCommentTable = $("#blogCommentTable").DataTable({
        ordering: true,
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
        const tableRows = document.querySelectorAll(
            ".custom-blog-table tbody tr"
        );
        const filterText = document.getElementById("filterText");

        filterItems.forEach((item) => {
            item.addEventListener("click", function () {
                const selected = this.textContent.trim();
                filterText.textContent = selected;

                const table = $("#blogCommentTable").DataTable();

                switch (selected) {
                    case _l("admin.common.ascending"):
                        table.order([1, "asc"]).draw();
                        break;
                    case _l("admin.common.descending"):
                        table.order([1, "desc"]).draw();
                        break;
                    case _l("admin.common.last_month"):
                        filterByDateRange(30, table);
                        break;
                    case _l("admin.common.last_7_days"):
                        filterByDateRange(7, table);
                        break;
                    case _l("admin.common.latest"):
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
