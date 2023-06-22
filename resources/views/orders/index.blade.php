@extends('layouts.admin')

@section('title', 'Orders List')
@section('content-header', 'Order List')
@section('content-actions')
    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#rekapHarianModal">Rekap Harian</a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-0"></div>
                <div class="col-md-5">Filter Tanggal:
                    <form action="{{ route('orders.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" />
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" />
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-primary" type="submit">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Nama Kasir</th>
                    <th>Total</th>
                    <th>Uang Diterima</th>
                    <th>Kembalian</th>
                    <th>Metode</th>
                    <th>Tgl Transaksi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->userName() }}</td>
                    <td>{{ config('settings.currency_symbol') }} {{ $order->formattedTotal() }}</td>
                    <td>{{ config('settings.currency_symbol') }} {{ $order->formattedReceivedAmount() }}</td>
                    <td>{{ config('settings.currency_symbol') }} {{ number_format($order->receivedAmount() - $order->total(), 2) }}</td>
                    <td>{{ $order->paymentMethod() }}</td>
                    <td>{{ $order->created_at }}</td>
                    <td>
                        <button class="btn btn-primary btn-order-details" data-order-id="{{ $order->id }}" data-toggle="modal" data-target="#orderDetailsModal">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                        <a href="{{ route('orders.print', ['order' => $order->id]) }}" class="btn btn-secondary" target="_blank">
                            <i class="fas fa-print"></i> Print
                        </a>
                        @hasrole('superadmin')
                        <button class="btn btn-danger btn-delete" data-url="{{ route('orders.destroy', $order) }}">
                            <i class="fas fa-trash"></i>
                        </button>
                        @endhasrole
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th>{{ config('settings.currency_symbol') }} {{ number_format($total, 2) }}</th>
                    <th>{{ config('settings.currency_symbol') }} {{ number_format($receivedAmount, 2) }}</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        {{ $orders->render() }}
         <!-- Add pagination links -->
        {{ $orders->links() }}
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Order details will be dynamically loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Daily Recap Modal -->

<!-- Daily Recap Modal -->
<div class="modal fade" id="rekapHarianModal" tabindex="-1" role="dialog" aria-labelledby="rekapHarianModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rekapHarianModalLabel">Daily Recap</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <h5>Tanggal: <span id="rekapDate"></span></h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <h6>Modal: {{ config('settings.currency_symbol') }} <span id="rekapCapital"></span></h6>
                    </div>
                    <div class="col-6">
                        <h6>Total Cash: {{ config('settings.currency_symbol') }} <span id="rekapTotalCash"></span></h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <h6>Transaksi Cash: {{ config('settings.currency_symbol') }} <span id="rekapCashTransaction"></span></h6>
                    </div>
                    <div class="col-6">
                        <h6>Transaksi Cashless: {{ config('settings.currency_symbol') }} <span id="rekapCashlessTransaction"></span></h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h6>Total: {{ config('settings.currency_symbol') }} <span id="rekapTotal"></span></h6>
                    </div>
                </div>
                <hr>
                <form id="rekapHarianForm">
                    <div class="form-group">
                        <label for="dateInput">Date</label>
                        <input type="date" class="form-control" id="dateInput" name="date">
                    </div>
                </form>
                <div id="rekapHarianResult"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="rekapHarianBtn">Get Recap</button>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery library -->
<script src="https://code.jquery.com/jquery-3.7.0.js" integrity="sha256-JlqSTELeR4TLqP0OG9dxM7yDPqX1ox/HfgiSLBj8+kM=" crossorigin="anonymous"></script>

<!-- Custom JavaScript code -->
<script>
    $(document).ready(function() {
        // AJAX request for daily recap
        $('#rekapHarianBtn').on('click', function() {
            var date = $('#dateInput').val();

            if (!date) {
                alert('Please select a date');
                return;
            }

            // Make an AJAX request to fetch the daily recap data
            $.ajax({
                url: 'http://127.0.0.1:5550/rekap/' + date,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    // Update the recap data in the modal
                    $('#rekapDate').text(response.rekap.date);
                    $('#rekapCapital').text(response.rekap.capital);
                    $('#rekapTotalCash').text(response.rekap.total_cash);
                    $('#rekapCashTransaction').text(response.rekap.cash_transaction);
                    $('#rekapCashlessTransaction').text(response.rekap.cashless_transaction);
                    $('#rekapTotal').text(response.rekap.total);

                    // Build the HTML content for the recap data
                    var html = '<h5> Detail: </h5>';
                    html += '<table class="table">';
                    html += '<thead><tr><th>Id</th><th>Nama Kasir</th><th>Total</th><th>Uang Diterima</th><th>Kembalian</th><th>Metode Pembayaran</th><th>Bukti Pembayaraan</th></tr></thead>';
                    html += '<tbody>';

                    // Loop through the detail objects and append rows to the table
                    for (var key in response.detail) {
                        var detail = response.detail[key];
                        html += '<tr>';
                        html += '<td>' + detail.id + '</td>';
                        html += '<td>' + detail.operator + '</td>';
                        html += '<td> {{ config('settings.currency_symbol') }} ' + detail.total + '</td>';
                        html += '<td> {{ config('settings.currency_symbol') }} ' + detail.received_amount + '</td>';
                        html += '<td> {{ config('settings.currency_symbol') }} ' + detail.change + '</td>';
                        html += '<td>' + detail.method + '</td>';
                        html += '<td>' + (detail.proof ? 'Ada' : 'Tidak Ada') + '</td>';
                        html += '</tr>';
                    }

                    html += '</tbody>';
                    html += '</table>';

                    // Display the recap data in the modal
                    $('#rekapHarianResult').html(html);
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        });

        // AJAX request for order details
        $(document).on('click', '.btn-order-details', function() {
            var orderId = $(this).data('order-id');

            // Make an AJAX request to fetch order details
            $.ajax({
                url: '{{ route("orders.details", ":id") }}'.replace(':id', orderId),
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    // Update the modal body with the fetched data
                    $('#orderDetailsModal .modal-body').html(response);
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        });

        // AJAX request for order deletion
        $(document).on('click', '.btn-delete', function() {
            var $this = $(this);

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: 'Are you sure?',
                text: 'The product will be permanently deleted!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: $this.data('url'),
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            $this.closest('tr').fadeOut(500, function() {
                                $(this).remove();
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
