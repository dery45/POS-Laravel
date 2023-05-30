<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;


class SearchController extends Controller
{
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $results = DB::table('products')
            ->where('name', 'LIKE', '%' . $keyword . '%')
            ->get();

        return view('search.results', ['results' => $results]);
    }

}
