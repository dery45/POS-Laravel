@extends('layouts.admin')

@section('content-header', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
        <div class="col-3">
          <div class="form-group">
              <label for="startDate">Start Date:</label>
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
              <label for="endDate">End Date:</label>
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
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="totalOrder" style="color: #fff;"></h3>
                        <p style="color: #fff;">Total Order</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag" style="color: #fff;"></i>
                    </div>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="totalIncome" style="color: #fff;"></h3>
                        <p style="color: #fff;">Total Barang Terjual</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars" style="color: #fff;"></i>
                    </div>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="incomeToday" style="color: #fff;"></h3>
                        <p style="color: #fff;">Total Pendapatan</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph" style="color: #fff;"></i>
                    </div>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
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
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
     $(document).ready(function () {
      // Fetch data on page load
      fetchData($('#startDate').val(), $('#endDate').val());

      // Fetch data when the user selects a date
      $('#startDate, #endDate').change(function () {
          var startDate = $('#startDate').val();
          var endDate = $('#endDate').val();
          fetchData(startDate, endDate);
      });

      // Fetch data from API and update boxes
      function fetchData(startDate, endDate) {
          var url = 'http://127.0.0.1:5551/dashboardbox';

          // Append start_date and end_date to the URL only if both values are present
          if (startDate && endDate) {
              url += '?start_date=' + startDate + '&end_date=' + endDate;
          }

          console.log('Fetching data from API...');
          console.log('URL:', url);

          $.ajax({
              url: url,
              type: 'GET',
              success: function (response) {
                  console.log('API response:', response);
                  // Convert string values to numbers
                  var totalOrder = parseInt(response['Total Transaksi']);
                  var totalIncome = parseFloat(response['Total Barang Terjual']);
                  var incomeToday = parseFloat(response['Total Pendapatan']);
                  var totalCustomers = parseFloat(response['Profit']);

                  // Update the boxes with fetched data
                  $('#totalOrder').text(totalOrder);
                  $('#totalIncome').text(totalIncome.toFixed(2));
                  $('#incomeToday').text(incomeToday.toFixed(2));
                  $('#totalCustomers').text(totalCustomers.toFixed(2));
              },
              error: function (xhr, status, error) {
                  console.log('Error fetching data from API:', error);
                  alert('Error fetching data from API.');
              }
          });
      }
  });
    </script>

@endsection
