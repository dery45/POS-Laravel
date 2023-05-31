<div class="row">
    <div class="col-md-6">
        <h4>Order Details</h4>
        <p><strong>Order ID:</strong> {{ $order->id }}</p>
        <p><strong>Customer Name:</strong> {{ $order->customer->name }}</p>
        <p><strong>Order Total:</strong> {{ config('settings.currency_symbol') }} {{ $order->formattedTotal() }}</p>
        <p><strong>Payment Method:</strong> {{ $order->paymentMethod() }}</p>
        <p><strong>Order Date:</strong> {{ $order->created_at }}</p>
    </div>
    <div class="col-md-6">
        <h4>Order Items</h4>
        <ul>
            @foreach ($order->items as $item)
                <li>{{ $item->product->name }} ({{ $item->quantity }} x {{ config('settings.currency_symbol') }} {{ $item->amount }})</li>
            @endforeach
        </ul>
    </div>
</div>