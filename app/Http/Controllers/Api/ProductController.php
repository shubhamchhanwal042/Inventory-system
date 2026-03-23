<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{

    public function index()
    {
        $products = Cache::remember('products_list', 60, function () {
            return Product::where('status','active')->get();
        });
    
        return response()->json($products);
    }

    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'name'=>'required',
    //         'category'=>'required',
    //         'base_price'=>'required|numeric'
    //     ]);

    //     $product = Product::create($data);

    //     return response()->json($product,201);
    // }

    public function store(Request $request)
{
    $data = $request->validate([
        'name'=>'required',
        'category'=>'required',
        'base_price'=>'required|numeric'
    ]);

    $product = Product::create($data);

    return response()->json([
        'success' => true,
        'message' => 'Product created successfully!',
        'data' => $product
    ], 201);
}
    public function show($id)
    {
        return Product::findOrFail($id);
    }

    public function update(Request $request,$id)
    {
        $product = Product::findOrFail($id);

        $product->update($request->all());

        return response()->json($product);
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return response()->json(['message'=>'Product deleted']);
    }

    
}