<div class="row">
    <div class="col-md-12">
        <h4>Order Details</h4>
        <table class="table">
            <tr>
                <td><strong>Kode Transaksi:</strong></td>
                <td>{{ $order->id }}</td>
            </tr>
            <tr>
                <td><strong>Operator:</strong></td>
                <td>{{ $order->userName() }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal:</strong></td>
                <td>{{ $order->created_at }}</td>
            </tr>
        </table>
    </div>
    <div class="col-md-12">
        <h4>Order Items</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jumlah Barang</th>
                    <th>Metode Pembayaran</th>
                    <th>Biaya</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->payment_method }}</td>
                        <td>{{ $item->amount }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
