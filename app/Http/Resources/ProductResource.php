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
   public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,

            'category' => $this->category?->name,
            'brand' => $this->brand?->name,

            'variants' => $this->variants->map(function ($variant) {
                return [
                    'color' => $variant->color?->name,
                    'sizes' => $variant->sizes->map(function ($size) {
                        return [
                            'size' => $size->size,
                            'stock' => $size->stock,
                            'price' => $size->price,
                        ];
                    })
                ];
            })
        ];
    }
}
