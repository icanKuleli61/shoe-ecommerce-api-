<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckoutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'items' => CartResource::collection(
                $this['items']
            ),

            'summary' => [

                'subtotal' => (float)
                    $this['subtotal'],

                'shipping' => (float)
                    $this['shipping'],

                'total' => (float)
                    $this['total'],
            ],

            'meta' => [

                'items_count' =>
                    count($this['items']),

                'currency' => 'TRY',
            ]
        ];
    }
}