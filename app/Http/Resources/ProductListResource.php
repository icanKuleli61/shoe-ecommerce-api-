<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $variant =

            $this->variants
                ->first();

        $image =

            $variant?->images
                ?->first();

        $minPrice =

            $this->variants
                ->flatMap(

                    fn($variant) =>

                    $variant->sizes
                )

                ->min('price');

        return [

            'id' =>

                $this->id,

            'variant_id' =>

                $variant?->id,

            'name' =>

                $this->name,

            'slug' =>

                $this->slug,

            'price' =>

                $minPrice,

            'image' =>

                $image?->image_path

                    ? asset(
                        'storage/' .
                        $image->image_path
                    )

                    : null,

            'reviews_avg_rating' =>

                round(
                    $this->reviews_avg_rating ?? 0,
                    1
                ),

            'reviews_count' =>

                $this->reviews_count ?? 0,

            'is_favorite' =>

                auth()->check()

                    ? $this->favorites
                        ->contains(
                            'user_id',
                            auth()->id()
                        )

                    : false,
        ];
    }
}