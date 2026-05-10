<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $minPrice = $this->variants
            ->flatMap(fn($v) => $v->sizes)
            ->min('price');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => $minPrice,
            'image' => $this->images->first()?->image_path,

            'is_favorite' => auth()->check()
                ? $this->favorites()
                    ->where('user_id', auth()->id())
                    ->exists()
                : false,
        ];
    }
}
