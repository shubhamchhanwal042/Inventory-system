<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stock;

class StockController extends Controller
{
    // List all stocks with product & warehouse
    public function index()
    {
        $stocks = Stock::with(['product','warehouse'])->get();

        return response()->json([
            'data' => $stocks
        ]);
    }

    // Add / Update stock
    public function store(Request $request)
    {
        $request->validate([
            'product_id'=>'required|exists:products,id',
            'warehouse_id'=>'required|exists:warehouses,id',
            'quantity'=>'required|numeric|min:0'
        ]);

        $stock = Stock::updateOrCreate(
            [
                'product_id'=>$request->product_id,
                'warehouse_id'=>$request->warehouse_id
            ],
            [
                'quantity'=>$request->quantity
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Stock added successfully!',
            'data' => $stock
        ], 201);
    }
}