<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockHistory;
use App\Models\User;
use Illuminate\Http\Request;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\CapabilityProfile;

use Illuminate\Support\Facades\Response;
use PDF; // Assuming you have a PDF library installed


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

    public function show($id)
    {
        $order = Order::findOrFail($id);

        return view('orders.show', ['order' => $order]);
    }

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

    public function details(Request $request)
    {
        $orderId = $request->route('id');
    
        $order = Order::find($orderId);
    
        if ($order === null) {
            return redirect()->back()->with('error', 'Order not found.');
        }
    
        return view('orders.details', compact('order'));
    }

    public function uploadProof(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        if ($request->hasFile('proof_image')) {
            $image = $request->file('proof_image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('proof_images'), $imageName);

            $order->proof_image = $imageName;
            $order->save();

            return redirect()->back()->with('success', 'Proof image uploaded successfully.');
        }

        return redirect()->back()->with('error', 'No proof image found.');
    }

    private function printReceipt($order)
    {


        $connector = new WindowsPrintConnector("POS-58-Share");
        $printer = new Printer($connector);

        // Set the character encoding and line spacing
        $printer->setLineSpacing(30);
        $user = $order->userName();
        $id = $order->id;
        $date = date('Y-m-d H:i:s');

        // Print header
        $printer->text("--------------------------------\n");
        $printer->text("         SUGE PLASTIK        \n");
        $printer->text("--------------------------------\n");
        $printer->text("Tgl     :   " . str_pad($date, 16, ' ', STR_PAD_LEFT). "\n");
        $printer->text("Kasir   :      " . str_pad($user, 16, ' ', STR_PAD_LEFT). "\n");
        $printer->text("Id      :      " . str_pad($id, 16, ' ', STR_PAD_LEFT). "\n");
        $printer->text("--------------------------------\n");

        // Print order items
        foreach ($order->items as $item) {
            $productName = $item->product->name;
            $quantity = $item->quantity;
            $method = $item->payment_method;
            $price = $item->amount / $quantity;
            $subtotal = $item->amount;   

            $printer->text($productName. "\n");
            // $printer->text(str_pad($quantity, 5, ' ', STR_PAD_LEFT));
            $printer->text(number_format($price)." "."x"." ".$quantity. str_pad(number_format($subtotal), 16, ' ', STR_PAD_LEFT). "\n");
            // $printer->text(str_pad(number_format($subtotal), 31, ' ', STR_PAD_LEFT));
            $printer->text("\n");

            // echo ("--------------------------------<br>");
            // echo ($productName. "<br>");
            // echo number_format($price)." "."x"." ".$quantity. str_pad(number_format($subtotal), 16, ' ', STR_PAD_LEFT). "\n";

        }

        // Print total amount and payment information
        $printer->text("--------------------------------\n");
        $printer->text("Total:         " . str_pad(number_format($order->total()), 16, ' ', STR_PAD_LEFT) . "\n");
        $printer->text("Metode:        " . str_pad($method, 16, ' ', STR_PAD_LEFT). "\n");
        $printer->text("Uang Diterima: " . str_pad(number_format($order->receivedAmount()), 16, ' ', STR_PAD_LEFT) . "\n");
        $printer->text("Kembalian:     " . str_pad(number_format($order->receivedAmount() - $order->total()), 16, ' ', STR_PAD_LEFT) . "\n");


        // Print footer
        $printer->text("--------------------------------\n");
        $printer->text("          Terima Kasih..        \n");
        $printer->text("     Info: 08882976524/Suge     \n");
        $printer->text("--------------------------------\n");


        // Cut the receipt
        $printer->cut();

        // Close the printer
        $printer->close();
    }

    
    public function print(Order $order)
    {
        $receipt = $this->printReceipt($order);
    }

    public function destroy(Order $order)
    {
        $order->delete();
    
        return response()->json(['message' => 'Order deleted successfully']);
    }
      

}



