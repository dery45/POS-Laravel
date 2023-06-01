<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\DailyCapital;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with(['items', 'payments'])->get();

        $today = Carbon::today();
        $dailyCapital = DailyCapital::whereDate('created_at', $today)->first();

        $capitalValue = $dailyCapital ? $dailyCapital->capital : 0;

        $cashin=Payment::whereDate('created_at', $today)
                ->whereIn('order_id', function ($query) {
                    $query->select('id')
                        ->from('order_items')
                        ->where('payment_method', 'cash');
                })
                ->sum('amount');
        $cashlessin=Payment::whereDate('created_at', $today)
                ->whereIn('order_id', function ($query) {
                    $query->select('id')
                        ->from('order_items')
                        ->where('payment_method', 'cashless');
                })
                ->sum('amount');

        if ($request->wantsJson()) {
            return response(
                $request->user()->cart()->get()
            );
        }
        return view('cart.index',[
            'capitalValue' => $capitalValue,
            'cashIn'=>$cashin,
            'cashlessIn'=>$cashlessin,
            'pendapatan' => $orders->where('created_at', '>=', date('Y-m-d').' 00:00:00')->map(function($i) {
                if($i->receivedAmount() > $i->total()) {
                    return $i->total();
                }
                return $i->receivedAmount();
            })->sum()+$capitalValue,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'barcode' => 'required|exists:products,barcode',
        ]);
        $barcode = $request->barcode;

        $product = Product::where('barcode', $barcode)->first();
        $cart = $request->user()->cart()->where('barcode', $barcode)->first();
        if ($cart) {
            // check product quantity
            if ($product->quantity <= $cart->pivot->quantity) {
                return response([
                    'message' => 'Product available only: ' . $product->quantity,
                ], 400);
            }
            // update only quantity
            $cart->pivot->quantity = $cart->pivot->quantity + 1;
            $cart->pivot->save();
        } else {
            if ($product->quantity < 1) {
                return response([
                    'message' => 'Product out of stock',
                ], 400);
            }
            $request->user()->cart()->attach($product->id, ['quantity' => 1]);
        }

        return response('', 204);
    }

    public function changeQty(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::find($request->product_id);
        $cart = $request->user()->cart()->where('id', $request->product_id)->first();

        if ($cart) {
            // check product quantity
            if ($product->quantity < $request->quantity) {
                return response([
                    'message' => 'Product available only: ' . $product->quantity,
                ], 400);
            }
            $cart->pivot->quantity = $request->quantity;
            $cart->pivot->save();
        }

        return response([
            'success' => true
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id'
        ]);
        $request->user()->cart()->detach($request->product_id);

        return response('', 204);
    }

    public function empty(Request $request)
    {
        $request->user()->cart()->detach();

        return response('', 204);
    }
    public function modal(Request $request)
    {
        // Validasi input
        $request->validate([
            'capital' => 'required|numeric'
        ]);

        // Simpan data ke tabel daily_capital
        $dailyCapital = DailyCapital::create([
            'capital'=>$request->capital,
            'user_id'=>$request->user()->id
        ]);

        // Tambahkan logika atau tindakan lain yang diperlukan setelah menyimpan data

        if (!$dailyCapital) {
            return redirect()->back()->with('error', 'Gagal input modal harian');
        }
        return redirect()->back()->with('success', 'Data modal harian berhasil disimpan.');
    }
}
