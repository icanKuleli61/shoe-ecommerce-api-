<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,

            'category' => $this->category?->name,
            'brand' => $this->brand?->name,

            'images' => $this->images->map(function ($image) {
                return [
                    'url' => $image->image_path,
                    'is_main' => $image->is_main
                ];
            }),

            'variants' => $this->variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'color_id' => $variant->color_id,
                    'color' => $variant->color?->name,

                    'sizes' => $variant->sizes->map(function ($size) {
                        return [
                            'size' => $size->size,
                            'stock' => $size->stock,
                            'price' => $size->price
                        ];
                    })
                ];
            }),
        ];
    }
}
