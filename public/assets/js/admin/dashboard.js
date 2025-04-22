document.addEventListener('DOMContentLoaded', function() {
    var options = {
        chart: {
    type: 'heatmap',
    height: 400,
    events: {
      mounted: function(ctx, config) {
        // Apply circular shape to all heatmap cells after render
        const heatmapCells = document.querySelectorAll('.apexcharts-heatmap-rect');
        heatmapCells.forEach(cell => {
          cell.setAttribute('rx', '50%'); // Circular shape
          cell.setAttribute('ry', '50%');
        });
      }
    }
  },
      plotOptions: {
        heatmap: {
          enableShades: false,
          colorScale: {
            ranges: [{
              from: 0,
              to: 0,
              color: '#E7F1F3'
            }, {
              from: 1,
              to: 20,
              color: '#127384'
            }]
          }
        }
      },
      dataLabels: { enabled: false },
      xaxis: {
        categories: categories,        
        labels: { rotate: -45 }
      },
      yaxis: {
        categories: times
      },
      series: series
    };

    var chart = new ApexCharts(
      document.querySelector("#statistics_chart"), 
      options
    );
    chart.render();
  });


  //income chart


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
var optionsIncome = {
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
        optionsIncome
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


    // Update the total income display
    document.querySelector(".income-summary p").textContent = `Income ${filter}`;
    document.querySelector(".income-summary h5").innerHTML = `
        $${totalIncome.toLocaleString()} 
        <span class="${percentageChange >= 0 ? 'text-success' : 'text-danger'} fs-13 fw-semibold">
            ${percentageChange.toFixed(2)}%
        </span>
    `;
    
    // Update the dropdown button text
    document.querySelector(".dropdown-toggle-chat").innerHTML = `<i class="ti ti-calendar me-1"></i> ${filter ?? ''}`;
}


document.addEventListener("DOMContentLoaded", function () {
    // Load "This Week" data on page load
    updateChartData("This Week");
});