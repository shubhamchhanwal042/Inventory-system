<?php

namespace App\Listeners;

use App\Events\OrderConfirmed;
use App\Models\Stock;

class DeductStock
{
    // public function handle($event)
    // {
    //     $order = $event->order;

    //     foreach ($order->items as $item) {

    //         $stock = Stock::where('product_id',$item->product_id)
    //             ->where('warehouse_id',$order->warehouse_id)
    //             ->first();

    //         $stock->decrement('quantity',$item->qty);
    //     }
    // }

    public function handle(OrderConfirmed $event)
    {
        \Log::info('DeductStock triggered for order ' . $event->order->id);

        $order = $event->order;

        foreach ($order->items as $item) {
            $stock = Stock::where('product_id', $item->product_id)
                          ->where('warehouse_id', $order->warehouse_id)
                          ->first();
            if ($stock) {
                $stock->decrement('quantity', $item->qty);
                \Log::info("Product {$item->product_id} stock deducted by {$item->qty}");
            }
        }
    }
}
