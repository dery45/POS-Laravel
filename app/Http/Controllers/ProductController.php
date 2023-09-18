<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Category;
use App\Events\ProductUpdated;
use App\Models\PriceHistory;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{
    public function homeIndex()
    {
        $products = Product::all();

        return view('home', ['products' => $products]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products = new Product();
        if ($request->search) {
            $products = $products->where('name', 'LIKE', "%{$request->search}%");
        }
        $products = $products->latest()->paginate(10);
        if (request()->wantsJson()) {
            return ProductResource::collection($products);
        }
        return view('products.index')->with('products', $products);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all(); // Retrieve all categories
        return view('products.create')->with('categories', $categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductStoreRequest $request)
    {
        $image_path = '';

        if ($request->hasFile('image')) {
            $image_path = $request->file('image')->store('products', 'public');
        }

        $categoryProduct = $request->category_product ?: null;

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $image_path,
            'barcode' => $request->barcode,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'status' => $request->status,
            'category_product' => $categoryProduct,
            'minimum_low' => $request->minimum_low,
            'brand' => $request->brand,
            'low_price' => $request->low_price,
            'stock_price' => $request->stock_price,
        ]);

        if (!$product) {
            return redirect()->back()->with('error', 'Sorry, there was a problem while creating the product.');
        }
        $priceHistory = new PriceHistory();
        $priceHistory->low_price = $product->low_price;
        $priceHistory->stock_price = $product->stock_price;
        $priceHistory->price = $product->price;
        $priceHistory->fk_product_id = $product->id;
        $priceHistory->save();
        $stockHistory = new StockHistory();
        $stockHistory->fk_product_id = $product->id;
        $stockHistory->quantity = $product->quantity;
        $stockHistory->created_at = now();
        $stockHistory->save();
        return redirect()->route('products.index')->with('success', 'Success, your product has been created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $category_products = Category::all();
        return view('products.edit', compact('product', 'categories', 'category_products'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $oldPrice = $product->price;
        $oldLow_Price = $product->low_price;
        $oldStock_Price = $product->stock_price;
        $oldQuantity = $product->quantity;
    
        $updatedData = [
            'name' => $request->name,
            'description' => $request->description,
            'barcode' => $request->barcode,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'status' => $request->status,
            'category_product' => $request->category_product ?: null,
            'minimum_low' => $request->minimum_low,
            'brand' => $request->brand,
            'low_price' => $request->low_price,
            'stock_price' => $request->stock_price,
        ];
    
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::delete($product->image);
            }
            // Store image
            $image_path = $request->file('image')->store('products', 'public');
            $updatedData['image'] = $image_path;
        }
    
        $product->update($updatedData);
    
        // Check if the price has been changed
        if ($oldPrice != $product->price) {
            // Create a new entry in price_histories table
            $priceHistory = new PriceHistory();
            $priceHistory->low_price = $product->low_price;
            $priceHistory->stock_price = $product->stock_price;
            $priceHistory->price = $product->price;
            $priceHistory->fk_product_id = $product->id;
            $priceHistory->save();
        }

        // Check if the price has been changed
        if ($oldStock_Price != $product->stock_price) {
            // Create a new entry in price_histories table
            $priceHistory = new PriceHistory();
            $priceHistory->low_price = $product->low_price;
            $priceHistory->stock_price = $product->stock_price;
            $priceHistory->price = $product->price;
            $priceHistory->fk_product_id = $product->id;
            $priceHistory->save();
        }

        if ($oldLow_Price != $product->low_price) {
            // Create a new entry in price_histories table
            $priceHistory = new PriceHistory();
            $priceHistory->low_price = $product->low_price;
            $priceHistory->stock_price = $product->stock_price;
            $priceHistory->price = $product->price;
            $priceHistory->fk_product_id = $product->id;
            $priceHistory->save();
        }

         // Check if the quantity has been changed
         if ($oldQuantity != $product->quantity) {
            // Create a new entry in the stock_history table
            $stockHistory = new StockHistory();
            $stockHistory->fk_product_id = $product->id;
            $stockHistory->quantity = $product->quantity;
            $stockHistory->created_at = now();
            $stockHistory->save();
        }

        event(new ProductUpdated($product));
    
        return redirect()->route('products.index')->with('success', 'Success, your product has been updated.');
    }
    
    public function stockupdate(ProductUpdateRequest $request, Product $product){
        $updatedData = [
            'name' => $request->name,
            'description' => $request->description,
            'barcode' => $request->barcode,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'status' => $request->status,
            'category_product' => $request->category_product ?: null,
            'minimum_low' => $request->minimum_low,
            'brand' => $request->brand,
            'low_price' => $request->low_price,
            'stock_price' => $request->stock_price,
        ];
        $product->update($updatedData);
        $stockHistory = new StockHistory();
        $stockHistory->fk_product_id = $product->id;
        $stockHistory->quantity = $product->quantity;
        $stockHistory->created_at = now();
        $stockHistory->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
   public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::delete($product->image);
        }
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Success, your product has been deleted.');
    }

    

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csvFile' => 'required|mimes:csv,txt',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
    
        $file = $request->file('csvFile');
        $filePath = $file->getPathname();
    
        // Detect the delimiter used in the CSV file
        $handle = fopen($filePath, 'r');
        $delimiter = $this->detectCsvDelimiter($handle);
        fclose($handle);
    
        // Parse the CSV file and import the products
        if (($handle = fopen($filePath, 'r')) !== false) {
            // Skip the header row
            $header = fgetcsv($handle, 0, $delimiter);
    
            while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
                // Process each row of data
                $name = $data[0];
                $barcode = $data[1] ?? null;
                $price = $data[2] ?? null;
                $quantity = $data[3] ?? null;
                $description = $data[4] ?? null;
                $minimumLow = $data[5] ?? null;
                $brand = $data[6] ?? null;
                $lowPrice = $data[7] ?? null;
                $stockPrice = $data[8] ?? null;
    
                // Create a new product using the parsed data
                $product = new Product();
                $product->name = $name;
                $product->barcode = $barcode;
                $product->price = $price;
                $product->quantity = $quantity;
                $product->description = $description;
                $product->minimum_low = $minimumLow;
                $product->brand = $brand;
                $product->low_price = $lowPrice;
                $product->stock_price = $stockPrice;
    
                // Save the product
                $product->save();

                //import to prices_histories and stock_histories
                $priceHistory = new PriceHistory();
                $priceHistory->low_price = $product->low_price;
                $priceHistory->stock_price = $product->stock_price;
                $priceHistory->price = $product->price;
                $priceHistory->fk_product_id = $product->id;
                $priceHistory->save();
                $stockHistory = new StockHistory();
                $stockHistory->fk_product_id = $product->id;
                $stockHistory->quantity = $product->quantity;
                $stockHistory->created_at = now();
                $stockHistory->save();
            }
    
            fclose($handle);
        }
    
        return redirect()->route('products.index')->with('success', 'Success, products imported.');
    }
    
    private function detectCsvDelimiter($handle)
    {
        $line = fgets($handle);
        $delimiters = array(',', ';', '\t');
        $count = array_fill_keys($delimiters, 0);
    
        foreach ($delimiters as $delimiter) {
            $count[$delimiter] = count(str_getcsv($line, $delimiter));
        }
    
        return array_search(max($count), $count);
    }
    

}
