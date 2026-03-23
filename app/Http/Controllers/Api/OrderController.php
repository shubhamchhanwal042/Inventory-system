<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;
use App\Models\Order; // ✅ Correctuse App\Events\OrderConfirmed;
use App\Models\Stock;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\DB;
use App\Events\OrderConfirmed; // ✅ Correct

class OrderController extends Controller
{

    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    // public function store(Request $request)
    // {

    //     $data = $request->validate([
    //         'customer_name'=>'required',
    //         'warehouse_id'=>'required',
    //         'items'=>'required|array'
    //     ]);

    //     $order = $this->orderService->createOrder($data);

    //     return response()->json($order,201);
    // }

    public function store(Request $request)
{
    $data = $request->validate([
        'customer_name'=>'required',
        'warehouse_id'=>'required',
        'items'=>'required|array'
    ]);

    try {
        $order = $this->orderService->createOrder($data);
        return response()->json([
            'success' => true,
            'data' => $order
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
    }
}


 // Confirm Order
public function confirm($id)
{
    try {
        DB::transaction(function() use ($id) {
            $order = Order::with('items')->findOrFail($id);

            if ($order->order_status !== 'pending') {
                throw new \Exception('Only pending orders can be confirmed');
            }

            $order->update(['order_status' => 'confirmed']);

            // Fire event to deduct stock
            event(new OrderConfirmed($order));
        });

        return response()->json([
            'success' => true,
            'message' => 'Order confirmed and stock deducted'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
    }
}

// Cancel Order
public function cancel($id)
{
    try {
        // Define variable outside
        $order = null;

        DB::transaction(function () use ($id, &$order) {

            $order = Order::with('items')->findOrFail($id);

            // Restore stock only if order was confirmed
            if($order->order_status === 'confirmed') {
                foreach($order->items as $item){
                    $stock = Stock::where('product_id', $item->product_id)
                                  ->where('warehouse_id', $order->warehouse_id)
                                  ->lockForUpdate()
                                  ->first();

                    if($stock) {
                        $stock->quantity += $item->qty;
                        $stock->save();
                    }
                }
            }

            $order->update(['order_status' => 'cancelled']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled' . ($order->order_status === 'confirmed' ? ' and stock restored' : '')
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
    }
}
    // public function index(Request $request)
    //     {

    //         $query = Order::with(['items','warehouse']);

    //         if($request->status){
    //             $query->where('order_status',$request->status);
    //         }

    //         if($request->start_date && $request->end_date){
    //             $query->whereBetween('created_at',[
    //                 $request->start_date,
    //                 $request->end_date
    //             ]);
    //         }

    //         $orders = $query->paginate(10);

    //         return OrderResource::collection($orders);
    //     }

    public function index(Request $request)
{
    $query = \App\Models\Order::with(['items', 'warehouse']);

    // Filter by status
    if ($request->has('status') && $request->status != '') {
        $query->where('order_status', $request->status);
    }

    // Filter by date range
    if ($request->has('start_date') && $request->has('end_date') 
        && $request->start_date && $request->end_date) {
        $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
    }

    // Pagination (optional: default 10 per page)
    $orders = $query->orderBy('created_at', 'desc')->paginate(5);

    // Transform orders for frontend
    $orders->getCollection()->transform(function($order){
        return [
            'id' => $order->id,
            'order_no' => $order->id,
            'customer' => $order->customer_name,
            'warehouse' => $order->warehouse->name ?? '',
            'status' => ucfirst($order->order_status),
            'total' => $order->total_amount,
            'items' => $order->items->map(function($item){
                return [
                    'product_name' => $item->product->name ?? 'Unknown',
                    'qty' => $item->qty
                ];
            })
        ];
    });

    return response()->json($orders);
}

        public function show($id)
            {
                $order = Order::with('items.product','warehouse')->findOrFail($id);

                return new OrderResource($order);
            }
}
