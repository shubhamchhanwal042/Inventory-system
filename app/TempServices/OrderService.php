<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder($data)
    {
        return DB::transaction(function () use ($data) {

            $order = Order::create([
                'customer_name' => $data['customer_name'],
                'warehouse_id' => $data['warehouse_id']
            ]);

            $total = 0;

            foreach ($data['items'] as $item) {

                $product = Product::findOrFail($item['product_id']);

                $stock = Stock::where('product_id', $product->id)
                    ->where('warehouse_id', $data['warehouse_id'])
                    ->lockForUpdate()
                    ->first();

                    if (!$stock || $stock->quantity < $item['qty']) {
                        throw new \Exception("Insufficient stock for product: ".$product->name);
                    }

                $subtotal = $product->base_price * $item['qty'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'price' => $product->base_price,
                    'subtotal' => $subtotal
                ]);

                $total += $subtotal;
            }

            $order->update([
                'total_amount' => $total
            ]);

            return $order;
        });
    }
}