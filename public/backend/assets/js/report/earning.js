(function () {
    "use strict";

    document.addEventListener("DOMContentLoaded", function () {
        fetch("/admin/earnings/monthly")
            .then((response) => response.text()) // Use text() instead of json() to debug
            .then((text) => {
                return JSON.parse(text); // Manually parse if it's valid JSON
            })
            .then((data) => {
                let months = [
                    "Jan",
                    "Feb",
                    "Mar",
                    "Apr",
                    "May",
                    "Jun",
                    "Jul",
                    "Aug",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dec",
                ];
                let chartData = Array(12).fill(0);

                data.forEach((item) => {
                    chartData[item.month - 1] = item.total_income;
                });

                const options = {
                    series: [{ name: "Earnings", data: chartData }],
                    chart: { type: "area", height: 300 },
                    xaxis: { categories: months },
                    colors: ["#ff9900"],
                };

                const chart = new ApexCharts(
                    document.querySelector("#expense-analysis"),
                    options
                );
                chart.render();
            })
            .catch((error) => console.error("Fetch error:", error));
    });

    document.addEventListener("DOMContentLoaded", function () {
        fetch("/admin/earnings/breakdown")
            .then((response) => response.json())
            .then((data) => {
                let categories = [
                    "Insurance",
                    "Extra Services",
                    "Vehicle Price",
                ];
                let values = [
                    data.total_insurance_price,
                    data.total_extra_service_price,
                    data.vehicle_total_price,
                ];
                let colors = ["#7d3289", "#008778", "#ffa633"];

                // Render Pie Chart
                const options = {
                    series: values,
                    chart: {
                        type: "pie",
                        height: 300,
                        width: 280,
                    },
                    labels: categories,
                    colors: colors,
                };

                const chart = new ApexCharts(
                    document.querySelector("#project-report"),
                    options
                );
                chart.render();

              let breakdownList = document.getElementById("breakdown-list");

            breakdownList.textContent = "";

            values.forEach((value, index) => {
                const listItem = document.createElement("li");

                const paragraph = document.createElement("p");
                paragraph.className = "text-gray-9 fs-10 d-flex align-items-center mb-0";

                const icon = document.createElement("i");
                icon.className = "ti ti-point-filled";
                icon.style.color = colors[index];

                const categoryText = document.createTextNode(` ${categories[index]}`);

                paragraph.appendChild(icon);
                paragraph.appendChild(categoryText);

                const valueSpan = document.createElement("span");
                valueSpan.className = "fs-10 text-gray-5";
                valueSpan.textContent = `$${value.toLocaleString()}`;

                listItem.appendChild(paragraph);
                listItem.appendChild(valueSpan);

                breakdownList.appendChild(listItem);
            });

            })
            .catch((error) =>
                console.error("Error loading earnings breakdown:", error)
            );
    });

    document.addEventListener("DOMContentLoaded", function () {
        const filterText = document.getElementById("filterText");
        let tableRows = document.querySelectorAll("#earningTable tbody tr");

        const filterOptions = document.querySelectorAll(".filter-option");

        filterOptions.forEach((item) => {
            item.addEventListener("click", handleFilterClick);
        });

        function handleFilterClick(event) {
            const selectedFilter = event.currentTarget.getAttribute("data-filter");

            updateFilterText(event.currentTarget.innerText);
            resetTableFilters();
            applyFilter(selectedFilter);
        }

        function updateFilterText(text) {
            const filterText = document.getElementById("filterText");
            if (filterText) filterText.innerText = text;
        }

        function resetTableFilters() {
            const tableRows = document.querySelectorAll("#earningTable tbody tr");
            tableRows.forEach((row) => (row.style.display = ""));
        }

        function applyFilter(filterType) {
            const tbody = document.querySelector("#earningTable tbody");
            const tableRowsArray = Array.from(tableRows); // Convert NodeList to Array

            let filteredRows;

            switch (filterType) {
                case "latest":
                    filteredRows = sortByDateDesc(tableRowsArray);
                    break;
                case "ascending":
                    filteredRows = sortByAmountAsc(tableRowsArray);
                    break;
                case "descending":
                    filteredRows = sortByAmountDesc(tableRowsArray);
                    break;
                case "last_month":
                    filteredRows = filterByLastMonth(tableRowsArray);
                    break;
                case "last_7_days":
                    filteredRows = filterByLast7Days(tableRowsArray);
                    break;
                default:
                    filteredRows = tableRowsArray;
            }

            // Clear tbody and append filtered/sorted rows
            tbody.innerHTML = "";
            filteredRows.forEach((row) => tbody.appendChild(row));
        }

        // ------------------- Helper Functions -------------------

        function sortByDateDesc(rows) {
            return rows.sort((a, b) => {
                const dateA = new Date(a.querySelector("td:nth-child(4) p").innerText);
                const dateB = new Date(b.querySelector("td:nth-child(4) p").innerText);
                return dateB - dateA;
            });
        }

        function sortByAmountAsc(rows) {
            return rows.sort((a, b) => {
                const amountA = parseFloat(
                    a.querySelector("td:nth-child(2) p").innerText.replace(/[^0-9.-]+/g, "")
                );
                const amountB = parseFloat(
                    b.querySelector("td:nth-child(2) p").innerText.replace(/[^0-9.-]+/g, "")
                );
                return amountA - amountB;
            });
        }

        function sortByAmountDesc(rows) {
            return rows.sort((a, b) => {
                const amountA = parseFloat(
                    a.querySelector("td:nth-child(2) p").innerText.replace(/[^0-9.-]+/g, "")
                );
                const amountB = parseFloat(
                    b.querySelector("td:nth-child(2) p").innerText.replace(/[^0-9.-]+/g, "")
                );
                return amountB - amountA;
            });
        }

        function filterByLastMonth(rows) {
            const lastMonth = new Date();
            lastMonth.setMonth(lastMonth.getMonth() - 1);

            return rows.filter((row) => {
                const dateCell = new Date(row.querySelector("td:nth-child(4) p").innerText);
                return dateCell >= lastMonth;
            });
        }

        function filterByLast7Days(rows) {
            const last7Days = new Date();
            last7Days.setDate(last7Days.getDate() - 7);

            return rows.filter((row) => {
                const dateCell = new Date(row.querySelector("td:nth-child(4) p").innerText);
                return dateCell >= last7Days;
            });
        }

    });

    $(document).ready(function () {
        $(".bookingrange").daterangepicker({
            autoUpdateInput: false,
            locale: { cancelLabel: "Clear" },
        });

        $(".bookingrange").on("apply.daterangepicker", function (ev, picker) {
            $(this).val(
                picker.startDate.format("DD/MM/YYYY") +
                    " - " +
                    picker.endDate.format("DD/MM/YYYY")
            );
            filterTableByDate(picker.startDate, picker.endDate);
        });

        $(".bookingrange").on("cancel.daterangepicker", function (ev, picker) {
            $(this).val("");
            filterTableByDate(null, null);
        });

        function filterTableByDate(startDate, endDate) {
            $("#earningTable tbody tr").each(function () {
                let dateText = $(this).find("td:nth-child(4) p").text(); // Get Date Column (4th column)
                let rowDate = moment(dateText, "DD MMM YYYY");

                if (startDate && endDate) {
                    if (
                        rowDate.isBefore(startDate) ||
                        rowDate.isAfter(endDate)
                    ) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                } else {
                    $(this).show(); // Show all if no filter is applied
                }
            });
        }
    });

    $(document).ready(function () {
        $(".bookingrange").daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: "Clear",
            },
        });

        $(".bookingrange").on("apply.daterangepicker", function (ev, picker) {
            $(this).val(
                picker.startDate.format("DD/MM/YYYY") +
                    " - " +
                    picker.endDate.format("DD/MM/YYYY")
            );
            filterTable();
        });

        $(".bookingrange").on("cancel.daterangepicker", function (ev, picker) {
            $(this).val("");
            filterTable();
        });

        $(".status-filter, .payment-filter").on("change", function () {
            filterTable();
        });

        function filterTable() {
            let selectedStatuses = $(".status-filter:checked")
                .map(function () {
                    return $(this).val().toLowerCase();
                })
                .get();

            let selectedPayments = $(".payment-filter:checked")
                .map(function () {
                    return $(this).val().toLowerCase();
                })
                .get();

            let dateRange = $(".bookingrange").val();
            let startDate = null,
                endDate = null;

            if (dateRange) {
                let dates = dateRange.split(" - ");
                startDate = moment(dates[0], "DD/MM/YYYY");
                endDate = moment(dates[1], "DD/MM/YYYY");
            }

            $("#earningTable tbody tr").each(function () {
                let rowPayment = $(this)
                    .find("td:nth-child(3) p")
                    .text()
                    .trim()
                    .toLowerCase();
                let rowDateText = $(this)
                    .find("td:nth-child(4) p")
                    .text()
                    .trim();
                let rowStatus = $(this)
                    .find("td:nth-child(5) span")
                    .text()
                    .trim()
                    .toLowerCase();

                let rowDate = moment(rowDateText, "DD/MM/YYYY");

                if (!rowDate.isValid()) {
                    rowDate = moment(rowDateText, "DD MMM YYYY");
                }

                let statusMatch =
                    selectedStatuses.length === 0 ||
                    selectedStatuses.includes(rowStatus);
                let paymentMatch =
                    selectedPayments.length === 0 ||
                    selectedPayments.includes(rowPayment);
                let dateMatch =
                    !startDate ||
                    !endDate ||
                    (rowDate.isValid() &&
                        rowDate.isBetween(startDate, endDate, null, "[]"));

                if (statusMatch && paymentMatch && dateMatch) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    $(document).ready(initEarningsTableActions);

    // ------------------- Initialization -------------------
    function initEarningsTableActions() {
        $(".btn-print").on("click", handlePrint);
        $(".btn-export").on("click", handleExportCSV);
    }

    // ------------------- Print Table -------------------
    function handlePrint() {
        const printContent = $("#earningTable").clone();
        const newWindow = window.open("", "", "width=800,height=600");

        const html = `
            <html>
                <head>
                    <title>Print Table</title>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
                </head>
                <body>
                    <h2 class="text-center">Earnings Report</h2>
                    ${printContent.prop("outerHTML")}
                </body>
            </html>
        `;

        newWindow.document.write(html);
        newWindow.document.close();
        newWindow.print();
    }

    // ------------------- Export CSV -------------------
    function handleExportCSV() {
        const table = $("#earningTable");
        const csvData = [];

        csvData.push(getTableHeaders(table));
        csvData.push(...getTableRows(table));

        const csvContent = "data:text/csv;charset=utf-8," + csvData.join("\n");
        downloadCSV(csvContent, "earnings_report.csv");
    }

    function getTableHeaders(table) {
        const headers = [];
        table.find("thead th").each(function () {
            headers.push($(this).text().trim());
        });
        return headers.join(",");
    }

    function getTableRows(table) {
        const rows = [];
        table.find("tbody tr:visible").each(function () {
            const row = [];
            $(this).find("td").each(function () {
                row.push($(this).text().trim());
            });
            rows.push(row.join(","));
        });
        return rows;
    }

    function downloadCSV(csvContent, filename) {
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.href = encodedUri;
        link.download = filename;
        document.body.appendChild(link);
        link.dispatchEvent(new MouseEvent("click"));
        document.body.removeChild(link);
    }

})();
