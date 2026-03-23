<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    //


    // public function stockSummary()
    // {
    //     $report = DB::table('stocks')
    //         ->join('products', 'stocks.product_id', '=', 'products.id')
    //         ->join('warehouses', 'stocks.warehouse_id', '=', 'warehouses.id')
    //         ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
    //         ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id') // join orders to filter status
    //         ->select(
    //             'products.name as product',
    //             'warehouses.name as warehouse',
    //             'stocks.quantity as current_stock',
    //             DB::raw("COALESCE(SUM(CASE WHEN orders.order_status = 'confirmed' THEN order_items.qty ELSE 0 END), 0) as sold_quantity")
    //         )
    //         ->groupBy('products.name', 'warehouses.name', 'stocks.quantity')
    //         ->get();
    
    //     return response()->json($report);
    // }

    public function stockSummary()
    {
        $report = DB::table('stocks')
            ->join('products','stocks.product_id','=','products.id')
            ->join('warehouses','stocks.warehouse_id','=','warehouses.id')
            ->leftJoin('order_items','products.id','=','order_items.product_id')
            ->leftJoin('orders','order_items.order_id','=','orders.id')
            ->select(
                'products.name as product',
                'warehouses.name as warehouse',
                // 'stocks.opening_stock',
                'stocks.quantity as current_stock',
                DB::raw("COALESCE(SUM(CASE WHEN orders.order_status = 'confirmed' THEN order_items.qty ELSE 0 END), 0) as sold_quantity"),
                DB::raw("COALESCE(SUM(CASE WHEN orders.order_status = 'confirmed' THEN order_items.subtotal ELSE 0 END), 0) as total_sales")
            )
            ->groupBy('products.name','warehouses.name','stocks.quantity')
            ->orderBy('products.name')
            ->get();

        return response()->json($report);
    }

// public function salesReport()
// {
//     $sales = DB::table('orders')
//         ->select(
//             DB::raw('DATE(created_at) as date'),
//             DB::raw('SUM(total_amount) as total_sales')
//         )
//         ->where('order_status','confirmed')
//         ->groupBy(DB::raw('DATE(created_at)'))
//         ->get();

//     return response()->json($sales);
// }

    public function salesReport()
    {
        $sales = DB::table('orders')
            ->where('order_status','confirmed')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total_sales')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date','asc')
            ->get();

        return response()->json($sales);
    }


// public function topProducts()
// {
//     $products = DB::table('order_items')
//         ->join('products','order_items.product_id','=','products.id')
//         ->join('orders','order_items.order_id','=','orders.id')
//         ->select(
//             'products.name',
//             DB::raw('SUM(order_items.qty) as total_sold')
//         )
//         ->groupBy('products.name')
//         ->orderByDesc('total_sold')
//         ->where('orders.order_status','confirmed')
//         ->limit(5)
//         ->get();

//     return response()->json($products);
// }

    public function topProducts()
    {
        $products = DB::table('order_items')
            ->join('products','order_items.product_id','=','products.id')
            ->join('orders','order_items.order_id','=','orders.id')
            ->where('orders.order_status','confirmed')
            ->select(
                'products.name',
                DB::raw('SUM(order_items.qty) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return response()->json($products);
    }
}
