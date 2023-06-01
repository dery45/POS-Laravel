@extends('layouts.admin')

@section('title', 'Orders List')
@section('content-header', 'Order List')
@section('content-actions')
    <a href="{{ route('cart.index') }}" class="btn btn-primary">Open POS</a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-7"></div>
            <div class="col-md-5">
                <form action="{{ route('orders.index') }}">
                    <div class="row">
                        <div class="col-md-5">
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" />
                        </div>
                        <div class="col-md-5">
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
                    <td>{{ config('settings.currency_symbol') }} {{ number_format($order->total() - $order->receivedAmount(), 2) }}</td>
                    <td>{{ $order->paymentMethod() }}</td>
                    <td>{{ $order->created_at }}</td>
                    <td>
                        <button class="btn btn-primary btn-order-details" data-order-id="{{ $order->id }}" data-toggle="modal" data-target="#orderDetailsModal">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                        <a href="#" class="btn btn-secondary">
                            <i class="fas fa-print"></i> Print
                        </a>
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


<script src="https://code.jquery.com/jquery-3.7.0.js" integrity="sha256-JlqSTELeR4TLqP0OG9dxM7yDPqX1ox/HfgiSLBj8+kM=" crossorigin="anonymous"></script>
<script>
    $(document).ready(function() {
        $('.btn-order-details').on('click', function() {
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
    });
</script>
@endsection
