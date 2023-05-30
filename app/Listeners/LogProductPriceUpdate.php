<?php

namespace App\Listeners;

use App\Events\ProductUpdated;
use App\Models\PriceHistory;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogProductPriceUpdate implements ShouldQueue
{
    public function handle(ProductUpdated $event)
    {
        $product = $event->product;

        // Create a new price history entry
        PriceHistory::create([
            'low_price' => $product->low_price,
            'stock_price' => $product->stock_price,
            'price' => $product->price,
            'product_id' => $product->id,
        ]);
    }
}

