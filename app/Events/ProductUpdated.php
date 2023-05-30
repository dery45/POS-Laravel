<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductUpdated
{
    use Dispatchable, SerializesModels;

    public $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }
}

