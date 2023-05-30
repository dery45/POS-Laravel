<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class AutocompleteController extends Controller
{
    public function search(Request $request)
    {
        $term = $request->input('term');

        $results = Product::where('name', 'LIKE', "%$term%")->limit(10)->get();

        $formattedResults = [];

        foreach ($results as $product) {
            $formattedResults[] = [
                'id' => $product->id,
                'label' => $product->name,
                'value' => $product->name,
            ];
        }

        return response()->json($formattedResults);
    }
}
