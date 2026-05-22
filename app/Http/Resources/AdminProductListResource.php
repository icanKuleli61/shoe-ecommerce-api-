<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminProductListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $firstVariant =
            $this->variants->first();

        $firstImage =
            $firstVariant?->images->first();

        $totalStock =
            $this->variants
                ->flatMap(fn($variant)
                    => $variant->sizes)
                ->sum('stock');

        return [

            'id' => $this->id,

            'name' => $this->name,

            'slug' => $this->slug,

            'image' => $firstImage
                ? url(
                    'api/image/' .
                    $firstImage->image_path
                )
                : null,
            'category' =>
                $this->category?->name,

            'brand' =>
                $this->brand?->name,

            'variant_count' =>
                $this->variants->count(),

            'total_stock' =>
                $totalStock,

            'status' =>
                $this->deleted_at
                ? false
                : true,

            'created_at' =>
                $this->created_at
                    ->format('d.m.Y'),
        ];
    }
}