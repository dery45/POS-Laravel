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
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function () {
        var incomeChart = null;
        var orderQuantityChart = null;

        // Fetch data on page load
        fetchDataDashboardBox();
        fetchDataIncomeProfit();
        fetchDataOrderQuantity();

        // Fetch data when the user selects a date
        $('#startDate, #endDate').change(function () {
            fetchDataDashboardBox();
            fetchDataIncomeProfit();
            fetchDataOrderQuantity();
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
    });
</script>

<style>
    .small-box-custom {
        height: 100px; /* Adjust the height as needed */
        padding: 10px; /* Adjust the padding as needed */
    }
</style>


@endsection
