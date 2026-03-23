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
    $order = $event->order;

    foreach ($order->items as $item) {
        $stock = Stock::where('product_id', $item->product_id)
                      ->where('warehouse_id', $order->warehouse_id)
                      ->lockForUpdate()
                      ->first();

        if ($stock) {
            $stock->decrement('quantity', $item->qty);
        }
    }
}
}
