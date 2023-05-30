<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'category_product',
        'image',
        'barcode',
        'status',
        'minimum_low',
        'brand',
        'quantity',
        'low_price',
        'stock_price',
        'price',
    ];

    /**
     * Get the category of the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_product');
    }
}
