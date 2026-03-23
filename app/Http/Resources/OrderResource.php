<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'order_no'=>$this->order_no,
            'customer'=>$this->customer_name,
            'warehouse'=>$this->warehouse->name,
            'status'=>$this->order_status,
            'total'=>$this->total_amount,
            'items'=>$this->items
        ];
    }

    
}
