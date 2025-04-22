// Ensure bookingData exists before proceeding
if (typeof bookingData === "undefined" || !Array.isArray(bookingData)) {
    console.error("Error: bookingData is not defined or is not an array.");
}

// Initialize arrays for chart data
var incomeData = [];
var categories = [];

// Populate incomeData and categories if bookingData exists
if (Array.isArray(bookingData) && bookingData.length > 0) {
    bookingData.forEach((booking) => {
        if (booking.booking_date && booking.vehicle_total_price !== undefined) {
            categories.push(
                new Date(booking.booking_date).toLocaleDateString()
            ); // Corrected date field
            incomeData.push(booking.vehicle_total_price || 0); // Corrected income field
        }
    });
} else {
    console.warn("No booking data available.");
}

// Initialize the chart
var options = {
    series: [{ name: "Income", data: incomeData }],
    chart: { type: "bar", height: 350 },
    plotOptions: {
        bar: { columnWidth: "50%", borderRadius: 5 },
    },
    colors: ["#FFA500"], // Orange for income
    xaxis: { categories: categories },
    yaxis: {
        labels: {
            formatter: (value) => "$" + value.toFixed(2),
        },
    },
    dataLabels: { enabled: false },
    grid: { borderColor: "#f1f1f1" },
};

// Ensure ApexCharts is loaded before initializing
if (typeof ApexCharts !== "undefined") {
    var chart = new ApexCharts(
        document.querySelector("#income_expense_chart"),
        options
    );
    chart.render();
} else {
    console.error("ApexCharts is not loaded.");
}

// Event listener for dropdown filter selection
document.querySelectorAll(".dropdown-item-chat").forEach((item) => {
    item.addEventListener("click", function () {
        let selected = this.textContent.trim();
       

        // Update the dropdown button text
        document.querySelector(
            ".dropdown-filter"
        ).innerHTML = `<i class="ti ti-calendar me-1"></i> ${selected}`;

        updateChartData(selected);
    });
});

document.addEventListener("DOMContentLoaded", function () {
    // Load "This Week" data on page load
    updateChartData("This Week");
});

// Event listener for dropdown filter selection
document.querySelectorAll(".dropdown-item-chat").forEach(item => {
    item.addEventListener("click", function () {
        let selected = this.textContent.trim();
       
        updateChartData(selected);
    });
});

// Function to update chart data based on the selected filter
function updateChartData(filter) {
    let today = new Date();
    let lastWeek = new Date();
    lastWeek.setDate(today.getDate() - 7);
    let previousWeek = new Date();
    previousWeek.setDate(today.getDate() - 14);

    let thisMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    let previousMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);

    let filteredData = bookingData.filter((booking) => {
        let bookingDate = new Date(booking.booking_date);

        if (filter === "This Week") return bookingDate >= lastWeek;
        if (filter === "Last Week")
            return bookingDate < lastWeek && bookingDate >= previousWeek;
        if (filter === "This Month") return bookingDate >= thisMonth;

        return true; // Default case (if no filter matches)
    });

    let previousPeriodData = bookingData.filter((booking) => {
        let bookingDate = new Date(booking.booking_date);

        if (filter === "This Week") return bookingDate < lastWeek && bookingDate >= previousWeek;
        if (filter === "Last Week") return bookingDate < previousWeek && bookingDate >= new Date(previousWeek.setDate(previousWeek.getDate() - 7));
        if (filter === "This Month") return bookingDate < thisMonth && bookingDate >= previousMonth;

        return false;
    });

    if (filteredData.length === 0) {
        console.warn("No data available for the selected filter:", filter);
    }

    let incomeData = filteredData.map(b => b.vehicle_total_price || 0); // Avoid undefined values
    let categories = filteredData.map(b => new Date(b.booking_date).toLocaleDateString() || "N/A");

    // Calculate total income
    let totalIncome = incomeData.reduce((sum, income) => sum + income, 0);

    // Calculate previous period's total income
    let previousTotalIncome = previousPeriodData.map(b => b.vehicle_total_price || 0).reduce((sum, income) => sum + income, 0);

    // Calculate percentage change
    let percentageChange = 0;
    if (previousTotalIncome > 0) {
        percentageChange = ((totalIncome - previousTotalIncome) / previousTotalIncome) * 100;
    }

    // Ensure chart is defined before updating
    if (chart) {
        chart.updateOptions({
            series: [{ name: "Income", data: incomeData }],
            xaxis: { categories: categories },
        });
    } else {
        console.error("Chart is not initialized.");
    }

    // Update the dropdown button text
    document.querySelector(".dropdown-toggle-chat").innerHTML = `<i class="ti ti-calendar me-1"></i> ${filter}`;

    // Update the total income display
    document.querySelector(".income-summary p").textContent = `Income ${filter}`;
    document.querySelector(".income-summary h5").innerHTML = `
        $${totalIncome.toLocaleString()} 
        <span class="${percentageChange >= 0 ? 'text-success' : 'text-danger'} fs-13 fw-semibold">
            ${percentageChange.toFixed(2)}%
        </span>
    `;
}


document.addEventListener("DOMContentLoaded", function () {
    // Load "This Week" data on page load
    updateChartData("This Week");
});

document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.getElementById("incomeTableBody");
    const dropdownItems = document.querySelectorAll(".filter-option");
    const selectedFilter = document.getElementById("selectedFilter");
    const dateRangeInput = document.getElementById("dateRangeFilter");

    let activeSortFilter = "latest"; // Default sorting filter
    let dateRange = null; // Stores selected date range

    // Store original table data (used for reset)
    const originalTableData = Array.from(tableBody.getElementsByTagName("tr"));

    function resetTableData() {
        tableBody.innerHTML = "";
        originalTableData.forEach(row => tableBody.appendChild(row.cloneNode(true)));
    }

    dropdownItems.forEach(item => {
        item.addEventListener("click", function () {
            // Remove active class from previous filter options
            dropdownItems.forEach(el => el.classList.remove("active"));
            this.classList.add("active");

            activeSortFilter = this.getAttribute("data-filter");
            selectedFilter.innerText = this.innerText;

            resetTableData();  // 🔹 Reset table before applying a new filter
            applyFilters();
        });
    });

    // Date Range Picker Initialization (Bootstrap Daterangepicker)
    $(dateRangeInput).daterangepicker({
        autoUpdateInput: false,
        locale: { cancelLabel: 'Clear' }
    });

    $(dateRangeInput).on('apply.daterangepicker', function(ev, picker) {
        resetTableData();  // 🔹 Reset table before applying a new filter

        dateRange = {
            start: picker.startDate.format('YYYY-MM-DD'),
            end: picker.endDate.format('YYYY-MM-DD')
        };
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        applyFilters();
    });

    $(dateRangeInput).on('cancel.daterangepicker', function(ev, picker) {
        resetTableData();  // 🔹 Reset table before applying a new filter

        dateRange = null;
        $(this).val('');
        applyFilters();
    });

    function applyFilters() {
        let rows = Array.from(tableBody.getElementsByTagName("tr"));
    
        // Apply sorting filter
        if (activeSortFilter === "asc") {
            rows.sort((a, b) => parseFloat(a.dataset.total) - parseFloat(b.dataset.total));
        } else if (activeSortFilter === "desc") {
            rows.sort((a, b) => parseFloat(b.dataset.total) - parseFloat(a.dataset.total));
        } else if (activeSortFilter === "latest") {
            rows.sort((a, b) => new Date(b.dataset.date) - new Date(a.dataset.date));
        } else if (activeSortFilter === "lastMonth") {
            const lastMonth = new Date();
            lastMonth.setMonth(lastMonth.getMonth() - 1);
            rows = rows.filter(row => {
                const rowDate = new Date(row.dataset.date);
                return !isNaN(rowDate) && rowDate >= lastMonth;
            });
        } else if (activeSortFilter === "last7days") {
            const last7Days = new Date();
            last7Days.setDate(last7Days.getDate() - 7);
            rows = rows.filter(row => {
                const rowDate = new Date(row.dataset.date);
                return !isNaN(rowDate) && rowDate >= last7Days;
            });
        }
    
        // Apply date range filter if selected
        if (dateRange) {
            rows = rows.filter(row => {
                const rowDate = new Date(row.dataset.date);
                return !isNaN(rowDate) && rowDate >= new Date(dateRange.start) && rowDate <= new Date(dateRange.end);
            });
        }
    
        // Update table
        tableBody.innerHTML = "";
        rows.forEach(row => tableBody.appendChild(row));
    }
    
});


document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.getElementById("incomeTable").querySelector("tbody");
    const carCheckboxes = document.querySelectorAll(".car-checkbox");
    const dateRangeInput = document.getElementById("dateRangeFilter");
    const statusCheckboxes = document.querySelectorAll(".status-checkbox");

    let selectedCars = new Set();
    let dateRange = null;
    let selectedStatuses = new Set();

    // Store original table data for resetting
    const originalTableData = Array.from(tableBody.getElementsByTagName("tr")).map(row => row.cloneNode(true));

    function resetTableData() {
        tableBody.innerHTML = "";
        originalTableData.forEach(row => tableBody.appendChild(row.cloneNode(true)));
    }

    function applyFilters() {
        resetTableData(); // Reset the table before applying new filters

        let rows = Array.from(tableBody.getElementsByTagName("tr"));

        // 🔹 Car Filter
        if (selectedCars.size > 0) {
            rows = rows.filter(row => {
                let carNameInput = row.querySelector(".car-name");
                let carName = carNameInput ? carNameInput.value.trim() : "";
                return selectedCars.has(carName);
            });
        }

        // 🔹 Date Range Filter
        if (dateRange) {
            rows = rows.filter(row => {
                let rowDate = new Date(row.dataset.date); // Ensure `data-date` is set in `YYYY-MM-DD` format in HTML
                return rowDate >= new Date(dateRange.start) && rowDate <= new Date(dateRange.end);
            });
        }

        // 🔹 Payment Status Filter
        if (selectedStatuses.size > 0) {
            rows = rows.filter(row => {
                let statusElement = row.querySelector(".payment-status");
                let statusText = statusElement ? statusElement.textContent.trim().toLowerCase() : "";
                return selectedStatuses.has(statusText);
            });
        }

        // Update table
        tableBody.innerHTML = "";
        rows.forEach(row => tableBody.appendChild(row));
    }

    // 🔹 Car Selection Event
    carCheckboxes.forEach(checkbox => {
        checkbox.addEventListener("change", function () {
            if (this.checked) {
                selectedCars.add(this.value);
            } else {
                selectedCars.delete(this.value);
            }
            applyFilters();
        });
    });

    // 🔹 Status Selection Event
    statusCheckboxes.forEach(checkbox => {
        checkbox.addEventListener("change", function () {
            if (this.checked) {
                selectedStatuses.add(this.value.toLowerCase());
            } else {
                selectedStatuses.delete(this.value.toLowerCase());
            }
            applyFilters();
        });
    });

    // 🔹 Date Range Picker Initialization
    $(dateRangeInput).daterangepicker({
        autoUpdateInput: false,
        locale: { cancelLabel: 'Clear' }
    });

    $(dateRangeInput).on('apply.daterangepicker', function(ev, picker) {
        dateRange = {
            start: picker.startDate.format('YYYY-MM-DD'),
            end: picker.endDate.format('YYYY-MM-DD')
        };
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        applyFilters();
    });

    $(dateRangeInput).on('cancel.daterangepicker', function(ev, picker) {
        dateRange = null;
        $(this).val('');
        applyFilters();
    });
});


document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.querySelector(".top-search-group input");
    const tableBody = document.getElementById("incomeTable").querySelector("tbody");

    searchInput.addEventListener("keyup", function () {
        let searchText = this.value.toLowerCase();
        let rows = tableBody.getElementsByTagName("tr");

        for (let row of rows) {
            let cells = row.getElementsByTagName("td");
            let match = false;

            for (let cell of cells) {
                if (cell.textContent.toLowerCase().includes(searchText)) {
                    match = true;
                    break;
                }
            }

            row.style.display = match ? "" : "none";
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.getElementById("incomeTable").querySelector("tbody");

    // Print Functionality
    document.getElementById("printButton").addEventListener("click", function () {
        let printWindow = window.open("", "", "width=900,height=700");
        printWindow.document.write("<html><head><title>Print Table</title>");
        printWindow.document.write("<style>table {width: 100%; border-collapse: collapse;} th, td {border: 1px solid black; padding: 8px; text-align: left;} </style>");
        printWindow.document.write("</head><body>");
        printWindow.document.write("<h2>Printed Data</h2>");
        printWindow.document.write("<table>" + document.getElementById("incomeTable").innerHTML + "</table>");
        printWindow.document.write("</body></html>");
        printWindow.document.close();
        printWindow.print();
    });

    // Export Functionality (CSV)
    document.getElementById("exportButton").addEventListener("click", function () {
        let rows = Array.from(tableBody.getElementsByTagName("tr"));
        let csvContent = "data:text/csv;charset=utf-8,";

        rows.forEach(row => {
            let cells = row.querySelectorAll("td");
            let rowData = Array.from(cells).map(cell => `"${cell.innerText.trim()}"`).join(",");
            csvContent += rowData + "\n";
        });

        let encodedUri = encodeURI(csvContent);
        let link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "income_table.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
});












