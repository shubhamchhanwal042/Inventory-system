<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Events\OrderConfirmed;
use App\Models\Stock;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\DB;


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
    public function handle($event)
    {
        $order = $event->order;

        foreach ($order->items as $item) {

            $stock = Stock::where('product_id',$item->product_id)
                ->where('warehouse_id',$order->warehouse_id)
                ->first();

            $stock->decrement('quantity',$item->qty);
        }
    }

    public function cancel($id)
    {
        try {
            DB::transaction(function () use ($id) {
    
                $order = Order::with('items')->findOrFail($id);
    
                foreach($order->items as $item){
                    $stock = Stock::where('product_id',$item->product_id)
                        ->where('warehouse_id',$order->warehouse_id)
                        ->lockForUpdate()
                        ->first();
    
                    $stock->quantity += $item->qty;
                    $stock->save();
                }
    
                $order->update(['order_status'=>'cancelled']);
            });
    
            return response()->json([
                'success' => true,
                'message' => 'Order cancelled and stock restored'
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    public function confirm($id)
    {
        try {
            DB::transaction(function () use ($id) {
    
                $order = Order::with('items')->findOrFail($id);
    
                if ($order->order_status != 'pending') {
                    throw new \Exception('Only pending orders can be confirmed');
                }
    
                foreach ($order->items as $item) {
                    $stock = Stock::where('product_id',$item->product_id)
                        ->where('warehouse_id',$order->warehouse_id)
                        ->lockForUpdate()
                        ->first();
    
                    if (!$stock || $stock->quantity < $item->qty) {
                        throw new \Exception('Insufficient stock');
                    }
    
                    $stock->quantity -= $item->qty;
                    $stock->save();
                }
    
                $order->update(['order_status' => 'confirmed']);
            });
    
            return response()->json([
                'success' => true,
                'message' => 'Order confirmed'
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function index(Request $request)
        {

            $query = Order::with(['items','warehouse']);

            if($request->status){
                $query->where('order_status',$request->status);
            }

            if($request->start_date && $request->end_date){
                $query->whereBetween('created_at',[
                    $request->start_date,
                    $request->end_date
                ]);
            }

            $orders = $query->paginate(10);

            return OrderResource::collection($orders);
        }

        public function show($id)
            {
                $order = Order::with('items.product','warehouse')->findOrFail($id);

                return new OrderResource($order);
            }
}
