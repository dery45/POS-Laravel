<?php

namespace App\Models;
use App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable =[
        'amount',
        'quantity',
        'product_id',
        'order_id',
        'payment_method',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
