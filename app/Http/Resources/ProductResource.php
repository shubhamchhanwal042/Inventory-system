<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    // public function toArray(Request $request): array
    // {
    //     return parent::toArray($request);
    // }

    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'sku'=>$this->sku,
            'category'=>$this->category,
            'price'=>$this->base_price,
            'status'=>$this->status
        ];
    }
}
