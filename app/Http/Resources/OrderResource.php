<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'total_price' => $this->total_price,
            'status' => $this->status,

            'status_text' => match($this->status) {
                'pending' => 'Beklemede',
                'paid' => 'Ödendi',
                'shipped' => 'Kargoya verildi',
                'completed' => 'Teslim edildi',
                'cancelled' => 'İptal edildi',
            },

            'address' => [
                'full_name' => $this->full_name,
                'phone' => $this->phone,
                'city' => $this->city,
                'district' => $this->district,
                'neighborhood' => $this->neighborhood,
                'address_text' => $this->address_text,
            ],

            'items' => $this->items->map(function ($item) {
                return [
                    'product_name' => $item->product_name,
                    'variant' => $item->variant_name,
                    'size' => $item->size_value,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ];
            }),
        ];
    }
}
