<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $image =
            $this->variant?->images?->first();

        return [

            'id' => $this->id,

            'product_name' =>
                $this->variant?->product?->name,

            'slug' =>
                $this->variant?->product?->slug,

            'variant_name' =>
                $this->variant?->color?->name,

            'variant_id' =>
                $this->variant_id,

            'size_id' =>
                $this->size_id,

            'size' =>
                $this->size?->size,

            'price' =>
                (float) $this->price,

            'quantity' =>
                $this->quantity,

            'subtotal' =>
                (float) (
                    $this->price *
                    $this->quantity
                ),

            'stock' =>
                $this->size?->stock,

            'image' =>
                $image?->url,
        ];
    }
}