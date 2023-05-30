<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;



class ProductController extends Controller
{
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

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $image_path,
            'barcode' => $request->barcode,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'status' => $request->status,
            'category_product' => $request->category_product,
            'minimum_low' => $request->minimum_low,
            'brand' => $request->brand,
            'low_price' => $request->low_price,
            'stock_price' => $request->stock_price,
        ]);
        

        if (!$product) {
            return redirect()->back()->with('error', 'Sorry, there a problem while creating product.');
        }
        return redirect()->route('products.index')->with('success', 'Success, you product have been created.');
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
        $product->name = $request->name;
        $product->description = $request->description;
        $product->barcode = $request->barcode;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
        $product->status = $request->status;
        $product->category_product = $request->category_product;
        $product->minimum_low = $request->minimum_low;
        $product->brand = $request->brand;
        $product->low_price = $request->low_price;
        $product->stock_price = $request->stock_price;

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::delete($product->image);
            }
            // Store image
            $image_path = $request->file('image')->store('products', 'public');
            // Save to Database
            $product->image = $image_path;
        }

        if (!$product->save()) {
            return redirect()->back()->with('error', 'Sorry, there\'re a problem while updating product.');
        }
        return redirect()->route('products.index')->with('success', 'Success, your product have been updated.');
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
    
        return response()->json([
            'success' => true
        ]);
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

        // Parse the CSV file and import the products
        if (($handle = fopen($filePath, 'r')) !== false) {
            // Skip the header row
            $header = fgetcsv($handle);

            while (($data = fgetcsv($handle)) !== false) {
                // Process each row of data
                $name = $data[0];
                $barcode = $data[1];
                $price = $data[2];
                $quantity = $data[3];

                // Create a new product using the parsed data
                $product = new Product();
                $product->name = $name;
                $product->barcode = $barcode;
                $product->price = $price;
                $product->quantity = $quantity;
                // Set other fields if needed

                // Save the product
                $product->save();
            }

            fclose($handle);
        }

        return redirect()->route('products.index')->with('success', 'CSV import successful.');
    }
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Add foreign key relationship with categories table
            $table->foreign('category_product')->references('id')->on('categories')->onDelete('cascade');
    
            $table->decimal('minimum_low', 10, 2);
            $table->string('brand')->nullable();
            $table->decimal('low_price', 10, 2)->nullable();
            $table->decimal('stock_price', 10, 2)->nullable();
    
            // Modify existing columns, if needed
            $table->text('description')->nullable()->change();
            $table->decimal('price', 10, 2)->change();
        });
    }
    
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop foreign key relationship
            $table->dropForeign(['category_product']);
    
            $table->dropColumn([
                'minimum_low',
                'brand',
                'low_price',
                'stock_price',
            ]);
    
            // Restore modified columns, if needed
            $table->string('description')->nullable(false)->change();
            $table->decimal('price', 10, 2)->change();
        });
    }


}
