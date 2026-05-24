<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $variant =
            $this->variants
                ->first();

        $image =
            $variant?->images
                ->first();

        return [

            'id' =>
                $this->id,

            'name' =>
                $this->name,

            'slug' =>
                $this->slug,

            'price' =>

                $this->variants
                    ->flatMap(

                        fn($variant) =>

                        $variant->sizes
                    )

                    ->min('price'),

            'image' =>

                $image?->image_path

                ? url(
                    'api/image/' .
                    $image->image_path
                )

                : null,

            'brand' => [

                'id' =>
                    $this->brand?->id,

                'name' =>
                    $this->brand?->name,
            ],

            'is_favorite' =>
                true
        ];
    }
}