"use strict";
const chartDataElement = document.getElementById('chart-data');


const times = JSON.parse(chartDataElement.dataset.times);
const bookingDate = JSON.parse(chartDataElement.dataset.bookingDate);
const series = JSON.parse(chartDataElement.dataset.series);
const dates = JSON.parse(chartDataElement.dataset.dates);
const bookingData = JSON.parse(chartDataElement.dataset.bookings);

document.addEventListener('DOMContentLoaded', function() {
    var options = {
        chart: {
    type: 'heatmap',
    height: 400,
    events: {
      mounted: function(ctx, config) {
        const heatmapCells = document.querySelectorAll('.apexcharts-heatmap-rect');
        heatmapCells.forEach(cell => {
          cell.setAttribute('rx', '50%');
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

if (typeof bookingData === "undefined" || !Array.isArray(bookingData)) {
    console.error("Error: bookingData is not defined or is not an array.");
}

var incomeData = [];
var categories = [];

if (Array.isArray(bookingData) && bookingData.length > 0) {
    bookingData.forEach((booking) => {
        if (booking.booking_date && booking.vehicle_total_price !== undefined) {
            categories.push(
                new Date(booking.booking_date).toLocaleDateString()
            ); 
            incomeData.push(booking.vehicle_total_price || 0); 
        }
    });
} else {
    console.warn("No booking data available.");
}

var optionsIncome = {
    series: [{ name: "Income", data: incomeData }],
    chart: { type: "bar", height: 280 },
    plotOptions: {
        bar: { columnWidth: "50%", borderRadius: 5 },
    },
    colors: ["#FFA500"], 
    xaxis: { categories: categories },
    yaxis: {
        labels: {
            formatter: (value) => "$" + value.toFixed(2),
        },
    },
    dataLabels: { enabled: false },
    grid: { borderColor: "#f1f1f1" },
};

if (typeof ApexCharts !== "undefined") {
    var chart = new ApexCharts(
        document.querySelector("#income_expense_chart"),
        optionsIncome
    );
    chart.render();
} else {
    console.error("ApexCharts is not loaded.");
}

document.querySelectorAll(".dropdown-item-chat").forEach((item) => {
    item.addEventListener("click", function () {
        let selected = this.textContent.trim();
       

        document.querySelector(
            ".dropdown-filter"
        ).innerHTML = `<i class="ti ti-calendar me-1"></i> ${selected}`;

        updateChartData(selected);
    });
});

document.addEventListener("DOMContentLoaded", function () {
   
    updateChartData("This Week");
});


document.querySelectorAll(".dropdown-item-chat").forEach(item => {
    item.addEventListener("click", function () {
        let selected = this.textContent.trim();
       
        updateChartData(selected);
    });
});


function updateChartData(filter) {
    let today = new Date();
    let dayOfWeek = today.getDay();

    let startOfWeek = new Date(today);
    startOfWeek.setDate(today.getDate() - dayOfWeek);
    let endOfWeek = new Date(startOfWeek);
    endOfWeek.setDate(startOfWeek.getDate() + 6);

    let startOfLastWeek = new Date(startOfWeek);
    startOfLastWeek.setDate(startOfLastWeek.getDate() - 7);
    let endOfLastWeek = new Date(startOfLastWeek);
    endOfLastWeek.setDate(startOfLastWeek.getDate() + 6);

    let thisMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    let previousMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);

    const formatDate = (date) => {
        return `${date.getDate()} ${date.toLocaleString('default', { month: 'short' })}`;
    };

    const generateDateRange = (start, end) => {
        let dateArray = [];
        let currentDate = new Date(start);
        while (currentDate <= end) {
            dateArray.push(formatDate(new Date(currentDate)));
            currentDate.setDate(currentDate.getDate() + 1);
        }
        return dateArray;
    };

    let filteredData = bookingData.filter((booking) => {
        let bookingDate = new Date(booking.booking_date);

        if (filter === "This Week") return bookingDate >= startOfWeek && bookingDate <= endOfWeek;
        if (filter === "Last Week") return bookingDate >= startOfLastWeek && bookingDate <= endOfLastWeek;
        if (filter === "This Month") return bookingDate >= thisMonth;

        return true;
    });

    let groupedData = {};
    filteredData.forEach((booking) => {
        let date = formatDate(new Date(booking.booking_date));
        if (!groupedData[date]) {
            groupedData[date] = 0;
        }
        groupedData[date] += booking.vehicle_total_price || 0;
    });

    let dateRange = [];
    if (filter === "This Week") dateRange = generateDateRange(startOfWeek, endOfWeek);
    if (filter === "Last Week") dateRange = generateDateRange(startOfLastWeek, endOfLastWeek);
    if (filter === "This Month") {
        let endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        dateRange = generateDateRange(thisMonth, endOfMonth);
    }

    let categories = dateRange;
    let incomeData = categories.map(date => groupedData[date] ?? 0);

    if (chart) {
        chart.updateOptions({
            series: [{ name: "Income", data: incomeData }],
            xaxis: { categories: categories },
        });
    } else {
        console.error("Chart is not initialized.");
    }

    const incomeText = document.querySelector(".income-summary p");
    const incomeAmount = document.querySelector(".income-summary h5");
    if (incomeText) {
        incomeText.textContent = `Income ${filter}`;
    }

    const totalIncome = incomeData.reduce((sum, income) => sum + income, 0);

    if (incomeAmount) {
        incomeAmount.innerHTML = `
            $${totalIncome.toLocaleString()} 
            <span class="fs-13 fw-semibold">0%</span>
        `;
    }
    
    const dropdownToggleChat = document.querySelector(".dropdown-toggle-chat");
    if (dropdownToggleChat) {
        dropdownToggleChat.innerHTML = `<i class="ti ti-calendar me-1"></i> ${filter ?? ''}`;
    }
}


document.addEventListener("DOMContentLoaded", function () {
    updateChartData("This Week");
});