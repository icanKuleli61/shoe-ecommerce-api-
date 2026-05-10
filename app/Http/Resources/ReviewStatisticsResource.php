<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewStatisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'average_rating' => $this['average_rating'],

            'reviews_count' => $this['reviews_count'],

            'rating_distribution' => $this['rating_distribution'],
        ];
    }
}
