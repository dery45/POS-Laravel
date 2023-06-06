<?php
public function dailyRecap(Request $request)
{
    $chosenDate = $request->query('chosen_date');

    // Retrieve the daily capital for the chosen date
    $dailyCapital = DailyCapital::whereDate('created_at', $chosenDate)->first();

    // Retrieve the total cash for the chosen date
    $totalCash = OrderItem::where('payment_method', 'cash')
        ->whereDate('created_at', $chosenDate)
        ->sum('amount');

    // Retrieve the total cashless for the chosen date
    $totalCashless = OrderItem::where('payment_method', 'cashless')
        ->whereDate('created_at', $chosenDate)
        ->sum('amount');

    // Retrieve the list of orders for the chosen date
    $orders = Order::whereDate('created_at', $chosenDate)
        ->with(['items', 'payments', 'customer'])
        ->get();

    return response()->json([
        'dailyCapital' => $dailyCapital,
        'totalCash' => $totalCash,
        'totalCashless' => $totalCashless,
        'orders' => $orders,
    ]);
}
?>