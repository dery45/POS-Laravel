@extends('layouts.admin')

@section('content-header', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-3">
                <div class="form-group">
                    <label for="startDate">Tanggal Mulai:</label>
                    <div class="input-group">
                        <input type="date" class="form-control" id="startDate" name="start_date">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="endDate">Tanggal Akhir:</label>
                    <div class="input-group">
                        <input type="date" class="form-control" id="endDate" name="end_date">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info small-box-custom">
                    <div class="inner">
                        <h3 id="totalOrder" style="color: #fff;"></h3>
                        <p style="color: #fff;">Total Order</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag" style="color: #fff;"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success small-box-custom">
                    <div class="inner">
                        <h3 id="totalIncome" style="color: #fff;"></h3>
                        <p style="color: #fff;">Total Barang Terjual</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars" style="color: #fff;"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger small-box-custom">
                    <div class="inner">
                        <h3 id="incomeToday" style="color: #fff;"></h3>
                        <p style="color: #fff;">Total Pendapatan</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph" style="color: #fff;"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning small-box-custom">
                    <div class="inner">
                        <h3 id="totalCustomers" style="color: #fff;"></h3>
                        <p style="color: #fff;">Profit</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add" style="color: #fff;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <canvas id="incomeChart" width="400" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <canvas id="orderQuantityChart" width="400" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <canvas id="paymentStatsChart" width="400" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <table id="topProductsTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-md-6">
                            <div class="form-group">
                                    <label for="productId">Product:</label>
                                    <select class="form-control" id="productId" name="product_id">
                                        <option value="">Select a product</option> <!-- Add an empty option -->
                                    </select>
                            </div>
                        </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <canvas id="stockHistoryChart" width="400" style="max-height: 250px;"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <canvas id="priceHistoryChart" width="400" style="max-height: 250px;"></canvas>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>    
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function () {
        var incomeChart = null;
        var orderQuantityChart = null;
        var stockHistoryChart = null;
        var priceHistoryChart = null;
        var selectedProductId = null;

        // Fetch data on page load
        fetchDataDashboardBox();
        fetchDataIncomeProfit();
        fetchDataOrderQuantity();
        fetchDataPaymentStats();
        fetchTopProducts();
        fetchStockHistory();
        fetchProductList();

        // Fetch data when the user selects a date
        $('#startDate, #endDate').change(function () {
            fetchDataDashboardBox();
            fetchDataIncomeProfit();
            fetchDataOrderQuantity();
            fetchDataPaymentStats();
            fetchTopProducts();
            fetchStockHistory();
            fetchPriceHistory();
        });

          // Fetch data when the user selects a product
        $('#productId').change(function () {
            fetchStockHistory();
            fetchPriceHistory();
        });

        function fetchDataDashboardBox() {
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();
            var url = 'http://127.0.0.1:5551/dashboardbox';

            if (startDate && endDate) {
                url += '?start_date=' + startDate + '&end_date=' + endDate;
            }

            console.log('Fetching dashboard box data from API...');
            console.log('URL:', url);

            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    console.log('API response:', response);
                    updateBoxes(response);
                },
                error: function (xhr, status, error) {
                    console.log('Error fetching dashboard box data from API:', error);
                    alert('Error fetching dashboard box data from API.');
                }
            });
        }

        function fetchDataIncomeProfit() {
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();
            var url = 'http://127.0.0.1:5551/income-profit';

            if (startDate && endDate) {
                url += '?start_date=' + startDate + '&end_date=' + endDate;
            }

            console.log('Fetching income-profit data from API...');
            console.log('URL:', url);

            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    console.log('API response:', response);
                    createOrUpdateChart(response);
                },
                error: function (xhr, status, error) {
                    console.log('Error fetching income-profit data from API:', error);
                    alert('Error fetching income-profit data from API.');
                }
            });
        }

        function updateBoxes(data) {
            var totalOrder = parseInt(data['Total Transaksi']);
            var totalIncome = parseInt(data['Total Barang Terjual']);
            var incomeToday = parseFloat(data['Total Pendapatan']);
            var totalCustomers = parseFloat(data['Profit']);

            $('#totalOrder').text(totalOrder);
            $('#totalIncome').text(totalIncome);
            $('#incomeToday').text(incomeToday.toFixed(2));
            $('#totalCustomers').text(totalCustomers.toFixed(2));
        }

        function createOrUpdateChart(data) {
          var labels = Object.keys(data);
          var incomeValues = Object.values(data).map(function (item) {
              return parseFloat(item.income);
          });
          var profitValues = Object.values(data).map(function (item) {
              return parseFloat(item.profit);
          });

          var ctx = document.getElementById('incomeChart').getContext('2d');

          if (incomeChart) {
              incomeChart.destroy();
          }

          incomeChart = new Chart(ctx, {
              type: 'line',
              data: {
                  labels: labels,
                  datasets: [{
                      label: 'Pendapatan',
                      data: incomeValues,
                      backgroundColor: 'rgba(54, 162, 235, 0.2)',
                      borderColor: 'rgba(54, 162, 235, 1)',
                      fill: true,
                      lineTension: 0.3 // Adjust the tension here (0.1 to 0.5 recommended)
                  }, {
                      label: 'Keuntungan',
                      data: profitValues,
                      backgroundColor: 'rgba(255, 99, 132, 0.2)',
                      borderColor: 'rgba(255, 99, 132, 1)',
                      fill: true,
                      lineTension: 0.3 // Adjust the tension here (0.1 to 0.5 recommended)
                  }]
              },
              options: {
                  responsive: true,
                  maintainAspectRatio: false
              }
          });
      }
       function fetchDataOrderQuantity() {
                var startDate = $('#startDate').val();
                var endDate = $('#endDate').val();
                var url = 'http://127.0.0.1:5551/order-quantity';

                if (startDate && endDate) {
                    url += '?start_date=' + startDate + '&end_date=' + endDate;
                }

                console.log('Fetching order-quantity data from API...');
                console.log('URL:', url);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (response) {
                        console.log('API response:', response);
                        createOrUpdateOrderQuantityChart(response);
                    },
                    error: function (xhr, status, error) {
                        console.log('Error fetching order-quantity data from API:', error);
                        alert('Error fetching order-quantity data from API.');
                    }
                });
            }

            function createOrUpdateOrderQuantityChart(data) {
                var labels = Object.keys(data);
                var orderCountData = Object.values(data).map(item => parseInt(item.order_count));
                var qtyCountData = Object.values(data).map(item => parseInt(item.qty_count));
                var maxCount = Math.max(...orderCountData, ...qtyCountData);
                var barColors = ['rgba(54, 162, 235, 0.8)', 'rgba(255, 99, 132, 0.8)'];

                var ctx = document.getElementById('orderQuantityChart').getContext('2d');

                if (orderQuantityChart) {
                    orderQuantityChart.destroy();
                }

                orderQuantityChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Jumlah Pesanan',
                                data: orderCountData,
                                backgroundColor: barColors[0],
                                borderWidth: 0,
                            },
                            {
                                label: 'Jumlah Barang',
                                data: qtyCountData,
                                backgroundColor: barColors[1],
                                borderWidth: 0,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                stacked: true,
                                grid: {
                                    display: false,
                                },
                            },
                            y: {
                                stacked: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
         
         
         
         
                                },
                                ticks: {
                                    beginAtZero: true,
                                    stepSize: Math.ceil(maxCount / 5),
                                },
                            },
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                            },
                        },
                    },
                });
            }
            function fetchDataPaymentStats() {
                var startDate = $('#startDate').val();
                var endDate = $('#endDate').val();
                var url = 'http://127.0.0.1:5551/payment-stats';

                if (startDate && endDate) {
                    url += '?start_date=' + startDate + '&end_date=' + endDate;
                }

                console.log('Fetching payment stats data from API...');
                console.log('URL:', url);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (response) {
                        console.log('API response:', response);
                        createOrUpdatePaymentStatsChart(response);
                    },
                    error: function (xhr, status, error) {
                        console.log('Error fetching payment stats data from API:', error);
                        alert('Error fetching payment stats data from API.');
                    }
                });
            }

            function createOrUpdatePaymentStatsChart(data) {
            var labels = Object.keys(data);
            var values = Object.values(data).map(item => parseFloat(item.total));
            var percentages = Object.values(data).map(item => parseFloat(item.percent));

            var ctx = document.getElementById('paymentStatsChart').getContext('2d');
            var paymentStatsChart = Chart.getChart(ctx);

            if (paymentStatsChart) {
                paymentStatsChart.data.labels = labels;
                paymentStatsChart.data.datasets[0].data = values;
                paymentStatsChart.data.datasets[0].percentages = percentages;
                paymentStatsChart.update();
            } else {
                paymentStatsChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            percentages: percentages,
                            backgroundColor: ['rgba(54, 162, 235, 0.8)', 'rgba(255, 99, 132, 0.8)'], // Adjust the colors as needed
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        var label = context.label || '';
                                        var value = parseFloat(context.parsed.toFixed(2)).toLocaleString('en-US');
                                        var percentage = parseFloat(context.dataset.percentages[context.dataIndex].toFixed(2)).toLocaleString('en-US');

                                        if (label) {
                                            label += ':\n';
                                        }
                                        label += 'Jumlah: ' + value + '\n';
                                        label += 'Persentase: ' + percentage + '%';
                                        return label;
                                    }
                                }
                            }
                        },
                        interaction: {
                            mode: 'index', // Show tooltip for the nearest data item
                            intersect: false,
                        },
                    }
                });
            }
        }
        function fetchTopProducts() {
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();
            var url = 'http://127.0.0.1:5551/product-quantity';

            if (startDate && endDate) {
                url += '?start_date=' + startDate + '&end_date=' + endDate;
            }

            console.log('Fetching top products data from API...');
            console.log('URL:', url);

            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    console.log('API response:', response);
                    updateTopProductsTable(response);
                },
                error: function (xhr, status, error) {
                    console.log('Error fetching top products data from API:', error);
                    alert('Error fetching top products data from API.');
                }
            });
        }

        function createOrUpdateTopProductsChart(data) {
            var labels = Object.keys(data);
            var quantityValues = Object.values(data).map(function (item) {
                return parseInt(item.qty_count);
            });

            var ctx = document.getElementById('topProductsChart').getContext('2d');

            if (topProductsChart) {
                topProductsChart.destroy();
            }

            topProductsChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: quantityValues,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    var label = context.label || '';
                                    var value = parseFloat(context.parsed.toFixed(2)).toLocaleString('en-US');

                                    if (label) {
                                        label += ':\n';
                                    }
                                    label += 'Quantity: ' + value;
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        function updateTopProductsTable(data) {
            var tableBody = $('#topProductsTable tbody');
            tableBody.empty();

            Object.entries(data).forEach(([product, values]) => {
                var row = $('<tr></tr>');
                var productCell = $('<td></td>').text(product);
                var quantityCell = $('<td></td>').text(parseInt(values.qty_count));

                row.append(productCell, quantityCell);
                tableBody.append(row);
            });
        }
        function fetchStockHistory() {
            var productId = $('#productId').val();
            var url = 'http://127.0.0.1:5551/stock_history/' + productId;

            console.log('Fetching stock history data from API...');
            console.log('URL:', url);

            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    console.log('API response:', response);
                    createOrUpdateStockHistoryChart(response);
                },
                error: function (xhr, status, error) {
                    console.log('Error fetching stock history data from API:', error);
                    alert('Error fetching stock history data from API.');
                }
            });
        }

        function createOrUpdateStockHistoryChart(data) {
            var labels = data.map(function (item) {
                return item.created_at;
            });
            var quantityValues = data.map(function (item) {
                return parseInt(item.quantity);
            });

            var ctx = document.getElementById('stockHistoryChart').getContext('2d');

            if (stockHistoryChart) {
                stockHistoryChart.destroy();
            }

            stockHistoryChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Quantity',
                        data: quantityValues,
                        backgroundColor: 'rgba(75, 192, 192, 0.8)', // Adjust the color as needed
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                            grid: {
                                display: false,
                            },
                        },
                        y: {
                            stacked: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                            },
                        },
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                },
            });
        }

        // Function to fetch stock history data
        function fetchStockHistory() {
            var productId = $('#productId').val();
            var url = 'http://127.0.0.1:5551/stock_history/' + productId;

            console.log('Fetching stock history data from API...');
            console.log('URL:', url);

            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    console.log('API response:', response);
                    createOrUpdateStockHistoryChart(response);
                },
                error: function (xhr, status, error) {
                    console.log('Error fetching stock history data from API:', error);
                    alert('Error fetching stock history data from API.');
                }
            });
        }

        // Function to create or update the stock history chart
        function createOrUpdateStockHistoryChart(data) {
            var labels = data.map(function (item) {
                return item.created_at;
            });
            var quantityValues = data.map(function (item) {
                return parseInt(item.quantity);
            });

            var ctx = document.getElementById('stockHistoryChart').getContext('2d');

            if (stockHistoryChart) {
                stockHistoryChart.destroy();
            }

            stockHistoryChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Quantity',
                        data: quantityValues,
                        backgroundColor: 'rgba(75, 192, 192, 0.8)', // Adjust the color as needed
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                            grid: {
                                display: false,
                            },
                        },
                        y: {
                            stacked: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                            },
                        },
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                },
            });
        }

        // Function to fetch the product list
        function fetchProductList() {
            var url = 'http://127.0.0.1:5551/products';

            console.log('Fetching product list from API...');
            console.log('URL:', url);

            $.ajax({
                url: url,
                type: 'GET',
                success: function (products) {
                    console.log('API response:', products);

                    // Clear existing options in the dropdown
                    $('#productId').empty();

                    // Add the empty option again
                    $('#productId').append($('<option>', {
                        value: '',
                        text: 'Select a product'
                    }));

                    // Populate the dropdown with product data
                    products.forEach(function (product) {
                        $('#productId').append($('<option>', {
                            value: product.id,
                            text: product.name
                        }));
                    });
                },
                error: function (xhr, status, error) {
                    console.log('Error fetching product list from API:', error);
                    alert('Error fetching product list from API.');
                }
            });
        }
         // Function to fetch price history data
         function fetchPriceHistory() {
            var productId = $('#productId').val();
            var url = 'http://127.0.0.1:5551/price_history/' + productId;

            console.log('Fetching price history data from API...');
            console.log('URL:', url);

            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    console.log('API response:', response);
                    createOrUpdatePriceHistoryChart(response);
                },
                error: function (xhr, status, error) {
                    console.log('Error fetching price history data from API:', error);
                    alert('Error fetching price history data from API.');
                }
            });
        }

        // Function to create or update the price history chart
        function createOrUpdatePriceHistoryChart(data) {
            var labels = data.map(function (item) {
                return item.created_at;
            });
            var lowPriceDifferenceValues = data.map(function (item) {
                return parseFloat(item.low_price_difference);
            });
            var stockPriceDifferenceValues = data.map(function (item) {
                return parseFloat(item.stock_price_difference);
            });
            var priceDifferenceValues = data.map(function (item) {
                return parseFloat(item.price_difference);
            });

            var ctx = document.getElementById('priceHistoryChart').getContext('2d');

            if (priceHistoryChart) {
                priceHistoryChart.destroy();
            }

            priceHistoryChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Low Price Difference',
                            data: lowPriceDifferenceValues,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            fill: false,
                            lineTension: 0.3,
                        },
                        {
                            label: 'Stock Price Difference',
                            data: stockPriceDifferenceValues,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            fill: false,
                            lineTension: 0.3,
                        },
                        {
                            label: 'Price Difference',
                            data: priceDifferenceValues,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            fill: false,
                            lineTension: 0.3,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                },
            });
        }
    });
</script>

<style>
    .small-box-custom {
        height: 100px; /* Adjust the height as needed */
        padding: 10px; /* Adjust the padding as needed */
    }
</style>


@endsection