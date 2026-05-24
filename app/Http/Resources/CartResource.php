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

        $stock =
            $this->size?->stock ?? 0;

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


            'stock' => $stock,

            'stock_status' =>

                $stock <= 0

                ? 'out_of_stock'

                : (
                    $stock <= 3

                    ? 'low_stock'

                    : 'in_stock'
                ),

            'stock_text' =>

                $stock <= 0

                ? 'Tükendi'

                : (
                    $stock <= 3

                    ? 'Son ' .
                    $stock .
                    ' ürün'

                    : 'Stokta mevcut'
                ),

            'max_quantity' => $stock,

            'is_available' =>
                $stock > 0,



            'image' => $image
                ? url(
                    'api/image/' .
                    $image->image_path
                )
                : null,
        ];
    }
}