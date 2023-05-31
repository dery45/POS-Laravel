<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request) {
        $orders = new Order();
        if($request->start_date) {
            $orders = $orders->where('created_at', '>=', $request->start_date);
        }
        if($request->end_date) {
            $orders = $orders->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }
        $orders = $orders->with(['items', 'payments', 'customer'])->latest()->paginate(10);

        $total = $orders->map(function($i) {
            return $i->total();
        })->sum();
        $receivedAmount = $orders->map(function($i) {
            return $i->receivedAmount();
        })->sum();

        return view('orders.index', compact('orders', 'total', 'receivedAmount'));
    }

    /*public function store(OrderStoreRequest $request)
    {
        $order = Order::create([
            'customer_id' => $request->customer_id,
            'user_id' => $request->user()->id,
        ]);

        $cart = $request->user()->cart()->get();
        foreach ($cart as $item) {
            if ($item->pivot->quantity >= $item->minimum_low) {
                $price = $item->pivot->quantity * $item->low_price;
            } else {
                $price = $item->pivot->quantity * $item->price;
            }
            $order->items()->create([
                'price' => $price,
                'quantity' => $item->pivot->quantity,
                'payment_method' => $request->payment_method,
                'product_id' => $item->id,
            ]);
            $item->quantity = $item->quantity - $item->pivot->quantity;
            $item->save();
        }
        $request->user()->cart()->detach();
        $order->payments()->create([
            'amount' => $request->amount,
            'user_id' => $request->user()->id,
        ]);
        return 'success';
    }*/
    public function store(OrderStoreRequest $request)
    {
    $order = Order::create([
        'customer_id' => $request->customer_id,
        'user_id' => $request->user()->id,
    ]);

    // Iterate through order items and save payment method
    foreach ($request->items as $item) {
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
            'amount' => $item['amount'],
            'payment_method' => $item['payment_method'],
        ]);

            // Update the item quantity
            $product = Product::find($item['product_id']);
            $product->quantity -= $item['quantity'];
            $product->save();
        }

    $request->user()->cart()->detach();
    $order->payments()->create([
        'amount' => $request->amount,
        'user_id' => $request->user()->id,
    ]);

    return 'success';
    }
    }

/*
    $cart = $request->user()->cart()->get();
    foreach ($cart as $item) {
        if ($item->pivot->quantity >= $item->minimum_low) {
            $price = $item->pivot->quantity * $item->low_price;
        } else {
            $price = $item->pivot->quantity * $item->price;
        }

        $order->items()->create([
            'amount' => $price,
            'quantity' => $item->pivot->quantity,
            'product_id' => $item->id,
        ]);

        

        $item->quantity = $item->quantity - $item->pivot->quantity;
        $item->save();
    }
*/