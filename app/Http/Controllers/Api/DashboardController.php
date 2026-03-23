<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Stock;

class DashboardController extends Controller
{
    public function index()
    {
        $productCount = Product::count();
        $warehouseCount = Warehouse::count();
        $totalStock = Stock::sum('quantity');

        return response()->json([
            'products' => $productCount,
            'warehouses' => $warehouseCount,
            'total_stock' => $totalStock
        ]);
    }
}