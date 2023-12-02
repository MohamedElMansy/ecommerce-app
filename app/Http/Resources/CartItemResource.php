<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $product = Product::find($this->product_id);
     
        return [
            'product_id' => $this->product_id,
            'en_name' => $product->en_name,
            'ar_name' => $product->ar_name,
            'description' => $product->description,
            'price' => $product->price,
            'quantity' => $this->quantity,
        ];
    }
}
