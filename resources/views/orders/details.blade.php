<div class="row">
    <div class="col-md-12">
        <h4>Keterangan Transaksi</h4>
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
            <tr>                   
            @if ($order->proof_image)
                    <td><strong>Bukti Transaksi:</strong></td>
                    <td><img src="{{ asset('proof_images/' . $order->proof_image) }}" alt="Proof Image" class="img-fluid"></td>
            @endif
            </tr>
        </table>
    </div>
    <div class="col-md-12">
        <h4>Keterangan Item</h4>
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
  <!-- Transaction Proof -->
  <div class="col-md-12">
        <form action="{{ route('orders.uploadProof', $order->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="proof_image">Upload Bukti Transaksi:</label>
                <input type="file" name="proof_image" id="proof_image" class="form-control-file" onchange="previewImage(event)">
                <div class="image-preview mt-2"></div>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        const imagePreview = document.querySelector('.image-preview');

        reader.onload = function() {
            const image = document.createElement('img');
            image.src = reader.result;
            image.classList.add('img-fluid');
            imagePreview.innerHTML = '';
            imagePreview.appendChild(image);
        }

        if (event.target.files && event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>

    
