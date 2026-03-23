<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{

    protected $fillable=[
        'customer_name',
        'warehouse_id',
        'order_status',
        'total_amount'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function($order){

            $order->order_no = 'ORD-'.time();
        });
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
