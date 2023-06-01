<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'user_id',
        'proof_image',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getCustomerName()
    {
        if($this->customer) {
            return $this->customer->first_name . ' ' . $this->customer->last_name;
        }
        return 'Working Customer';
    }



    /*
    public function total()
    {
        return $this->items->map(function ($i){
            return $i->price;
        })->sum();
    }
    */
    /*
    public function total()
    {
        return $this->items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
    }
    */
    public function total()
    {
        return $this->items->sum(function ($item) {
            if ($item->quantity >= $item->product->minimum_low) {
                return $item->product->low_price * $item->quantity;
            }
            return $item->product->price * $item->quantity;
        });
    }


    public function paymentMethod()
    {
        $orderItem = $this->items()->first();
    
        if ($orderItem) {
            return $orderItem->payment_method;
        }
    
        return 'N/A';
    }

    public function userName()
    {
        return $this->belongsTo(User::class, 'user_id')->value('name') ?? 'N/A';
    }


    public function formattedTotal()
    {
        return number_format($this->total(), 2);
    }

    public function receivedAmount()
    {
        return $this->payments->map(function ($i){
            return $i->amount;
        })->sum();
    }

    public function formattedReceivedAmount()
    {
        return number_format($this->receivedAmount(), 2);
    }
}
