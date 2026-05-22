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

            'brand_id' => $this->brand_id,

            'category_id' => $this->category_id,

            'gender' => $this->gender,

            'slug' => $this->slug,

            'description' => $this->description,

            'category' => $this->category?->name,

            'brand' => $this->brand?->name,


            'images' => $this->images
                ->sortBy('order')
                ->values()
                ->map(function ($image) {

                    return [

                        'id' => $image->id,

                        'url' => url(
                            'api/image/' .
                            $image->image_path
                        ),

                        'is_main' => $image->is_main,

                        'order' => $image->order
                    ];
                }),


            'variants' => $this->variants->map(function ($variant) {

                return [

                    'id' => $variant->id,

                    'name' => $variant->name,

                    'color_id' => $variant->color_id,

                    'color' => $variant->color?->name,

                    'images' => ProductImageResource::collection(

                        $variant->images
                            ->sortBy('order')
                            ->values()
                    ),

                    'sizes' => $variant->sizes->map(function ($size) {

                        return [

                            'id' => $size->id,

                            'size' => $size->size,

                            'stock' => $size->stock,

                            'price' => $size->price,
                        ];
                    }),
                ];
            }),
        ];
    }
}