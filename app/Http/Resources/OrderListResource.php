<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderListResource extends JsonResource
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

            'created_at' => $this->created_at,
        ];
    }
}
