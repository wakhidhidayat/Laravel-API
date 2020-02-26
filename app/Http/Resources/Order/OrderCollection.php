<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Resources\Json\Resource;

class OrderCollection extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        foreach($this->products as $product) {
            $name = $product->name;   
        }
        return [
            'id' => $this->id,
            'product' => $name,
            'quantity' => $this->quantity,
            'totalPrice' => 'Rp. '.$this->total_price,
            'status' => $this->status,
            'orderCreated' => $this->created_at->diffForHumans()
        ];
    }
}
