<?php

namespace App\Http\Controllers;

use App\Models\DailyCapital;
use Illuminate\Http\Request;

class CapitalController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'capital' => 'required|numeric'
        ]);

        // Simpan data ke tabel daily_capital
        $dailyCapital = new DailyCapital();
        $dailyCapital->capital = $request->input('capital');
        $dailyCapital->user_id = $request->user();
        $dailyCapital->save();

        // Tambahkan logika atau tindakan lain yang diperlukan setelah menyimpan data

        // Redirect atau memberikan respon yang sesuai
        return redirect()->back()->with('success', 'Data modal harian berhasil disimpan.');
    }
}
