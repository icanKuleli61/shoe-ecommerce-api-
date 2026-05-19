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

            'user_email' =>

                $this->user?->email,

            'order_no' =>

                'SNK-' .

                $this->created_at->year .

                '-' .

                str_pad(
                    $this->id,
                    5,
                    '0',
                    STR_PAD_LEFT
                ),

            'total_price' =>

                (float) $this->total_price,



            'status' =>

                $this->status,

            'status_text' => match ($this->status) {

                'pending' =>
                'Sipariş alındı',

                'approved' =>
                'Sipariş onaylandı',

                'supplying' =>
                'Ürünler tedarik ediliyor',

                'packaging' =>
                'Ürünler paketleniyor',

                'shipped' =>
                'Kargoya verildi',

                'out_for_delivery' =>
                'Dağıtıma çıktı',

                'delivered' =>
                'Teslim edildi',

                'completed' =>
                'Sipariş tamamlandı',

                'cancelled' =>
                'Sipariş iptal edildi',

                default =>
                'Bilinmiyor'
            },

            'created_at' =>

                $this->created_at
                    ->format('d.m.Y H:i'),

            'items_count' =>

                $this->items_count,

            'images' =>

                $this->items
                    ->take(3)
                    ->map(function ($item) {

                        return optional(

                            $item
                                ->variant
                                ?->images
                                    ?->first()

                        )->image_path;
                    })
                    ->filter()
                    ->values(),
        ];
    }
}