"use strict";
document.addEventListener("DOMContentLoaded", function() {
    fetch('/admin/earnings/monthly')
        .then(response => response.text()) // Use text() instead of json() to debug
        .then(text => {
            return JSON.parse(text); // Manually parse if it's valid JSON
        })
        .then(data => {
            let months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            let chartData = Array(12).fill(0);

            data.forEach(item => {
                chartData[item.month - 1] = item.total_income;
            });

            var options = {
                series: [{ name: "Earnings", data: chartData }],
                chart: { type: "area", height: 300 },
                xaxis: { categories: months },
                colors: ["#ff9900"]
            };

            var chart = new ApexCharts(document.querySelector("#expense-analysis"), options);
            chart.render();
        })
        .catch(error => console.error("Fetch error:", error));
});


document.addEventListener("DOMContentLoaded", function () {
    fetch('/admin/earnings/breakdown')
        .then(response => response.json())
        .then(data => {
            let categories = ["Insurance", "Extra Services", "Vehicle Price"];
            let values = [
                data.total_insurance_price,
                data.total_extra_service_price,
                data.vehicle_total_price
            ];
            let colors = ["#7d3289", "#008778", "#ffa633"];

            // Render Pie Chart
            var options = {
                series: values,
                chart: {
                    type: "pie",
                    height: 300,
                    width: 280
                },
                labels: categories,
                colors: colors
            };

            var chart = new ApexCharts(document.querySelector("#project-report"), options);
            chart.render();

            // Render List Below Chart
            let breakdownList = document.getElementById("breakdown-list");
            breakdownList.innerHTML = "";
            values.forEach((value, index) => {
                breakdownList.innerHTML += `
                    <li>
                        <p class="text-gray-9 fs-10 d-flex align-items-center mb-0">
                            <i class="ti ti-point-filled" style="color: ${colors[index]};"></i> ${categories[index]}
                        </p>
                        <span class="fs-10 text-gray-5">$${value.toLocaleString()}</span>
                    </li>`;
            });
        })
        .catch(error => console.error("Error loading earnings breakdown:", error));
});

document.addEventListener("DOMContentLoaded", function () {
    let filterDropdown = document.getElementById("filterDropdown");
    let filterText = document.getElementById("filterText");
    let tableRows = document.querySelectorAll("#earningTable tbody tr");

    // Add event listeners to all filter options
    document.querySelectorAll(".filter-option").forEach(item => {
        item.addEventListener("click", function () {
            let selectedFilter = this.getAttribute("data-filter");

            // Update the filter text in dropdown
            filterText.innerText = this.innerText;

            // Clear previous filter
            tableRows.forEach(row => row.style.display = "");

            // Apply new filter
            applyFilter(selectedFilter);
        });
    });

    function applyFilter(filterType) {
        let now = new Date();
        tableRows.forEach(row => {
            let dateCell = row.querySelector("td:nth-child(4) p").innerText; // Get Date Column (4th Column)
            let parsedDate = new Date(dateCell); // Convert to Date object

            switch (filterType) {
                case "latest":
                    tableRows = Array.from(tableRows).sort((a, b) => {
                        let dateA = new Date(a.querySelector("td:nth-child(4) p").innerText);
                        let dateB = new Date(b.querySelector("td:nth-child(4) p").innerText);
                        return dateB - dateA; // Sort by latest (Descending)
                    });
                    break;

                case "ascending":
                    tableRows = Array.from(tableRows).sort((a, b) => {
                        let amountA = parseFloat(a.querySelector("td:nth-child(2) p").innerText.replace(/[^0-9.-]+/g, ""));
                        let amountB = parseFloat(b.querySelector("td:nth-child(2) p").innerText.replace(/[^0-9.-]+/g, ""));
                        return amountA - amountB; // Ascending order
                    });
                    break;

                case "descending":
                    tableRows = Array.from(tableRows).sort((a, b) => {
                        let amountA = parseFloat(a.querySelector("td:nth-child(2) p").innerText.replace(/[^0-9.-]+/g, ""));
                        let amountB = parseFloat(b.querySelector("td:nth-child(2) p").innerText.replace(/[^0-9.-]+/g, ""));
                        return amountB - amountA; // Descending order
                    });
                    break;

                case "last_month":
                    let lastMonth = new Date();
                    lastMonth.setMonth(lastMonth.getMonth() - 1);
                    if (parsedDate < lastMonth) row.style.display = "none";
                    break;

                case "last_7_days":
                    let last7Days = new Date();
                    last7Days.setDate(last7Days.getDate() - 7);
                    if (parsedDate < last7Days) row.style.display = "none";
                    break;
            }
        });

        // Reorder table rows
        let tbody = document.querySelector("#earningTable tbody");
        tbody.innerHTML = "";
        tableRows.forEach(row => tbody.appendChild(row));
    }
});

$(document).ready(function () {
    $(".bookingrange").daterangepicker({
        autoUpdateInput: false,
        locale: { cancelLabel: "Clear" }
    });

    $(".bookingrange").on("apply.daterangepicker", function (ev, picker) {
        $(this).val(picker.startDate.format("DD/MM/YYYY") + " - " + picker.endDate.format("DD/MM/YYYY"));
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
                if (rowDate.isBefore(startDate) || rowDate.isAfter(endDate)) {
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
            cancelLabel: 'Clear'
        }
    });

    $(".bookingrange").on("apply.daterangepicker", function (ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        filterTable();
    });

    $(".bookingrange").on("cancel.daterangepicker", function (ev, picker) {
        $(this).val('');
        filterTable();
    });

    $(".status-filter, .payment-filter").on("change", function () {
        filterTable();
    });

    function filterTable() {
        let selectedStatuses = $(".status-filter:checked").map(function () {
            return $(this).val().toLowerCase();
        }).get();

        let selectedPayments = $(".payment-filter:checked").map(function () {
            return $(this).val().toLowerCase();
        }).get();

        let dateRange = $(".bookingrange").val();
        let startDate = null, endDate = null;

        if (dateRange) {
            let dates = dateRange.split(" - ");
            startDate = moment(dates[0], "DD/MM/YYYY");
            endDate = moment(dates[1], "DD/MM/YYYY");
        }

        $("#earningTable tbody tr").each(function () {
            let rowPayment = $(this).find("td:nth-child(3) p").text().trim().toLowerCase();
            let rowDateText = $(this).find("td:nth-child(4) p").text().trim();
            let rowStatus = $(this).find("td:nth-child(5) span").text().trim().toLowerCase();

            let rowDate = moment(rowDateText, "DD/MM/YYYY");

            if (!rowDate.isValid()) {
                rowDate = moment(rowDateText, "DD MMM YYYY");
            }


            let statusMatch = selectedStatuses.length === 0 || selectedStatuses.includes(rowStatus);
            let paymentMatch = selectedPayments.length === 0 || selectedPayments.includes(rowPayment);
            let dateMatch = (!startDate || !endDate) || (rowDate.isValid() && rowDate.isBetween(startDate, endDate, null, '[]'));

            if (statusMatch && paymentMatch && dateMatch) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
});

$(document).ready(function () {
    $(".btn-print").on("click", function () {
        let printContent = $("#earningTable").clone();
        let newWindow = window.open("", "", "width=800,height=600");
        newWindow.document.write(`
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
        `);
        newWindow.document.close();
        newWindow.print();
    });

    // Export table data as CSV
    $(".btn-export").on("click", function () {
        let table = $("#earningTable");
        let csvData = [];
        let headers = [];

        // Get table headers
        table.find("thead th").each(function () {
            headers.push($(this).text().trim());
        });
        csvData.push(headers.join(",")); // Add headers to CSV

        // Get visible table rows
        table.find("tbody tr:visible").each(function () {
            let row = [];
            $(this)
                .find("td")
                .each(function () {
                    row.push($(this).text().trim());
                });
            csvData.push(row.join(","));
        });

        let csvContent = "data:text/csv;charset=utf-8," + csvData.join("\n");
        let encodedUri = encodeURI(csvContent);
        let link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "earnings_report.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
});



