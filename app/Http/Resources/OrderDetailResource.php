<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'order_no' =>

                $this->generateOrderNumber(),



            'status' =>
                $this->status,

            'status_text' =>
                $this->getStatusText(),

            'status_step' =>
                $this->getStatusStep(),


            'payment_method' =>
                $this->payment_method,

            'payment_method_text' =>

                $this->payment_method === 'wallet'

                ? 'Cüzdan'

                : 'Kart',

            'payment_status' =>
                $this->payment_status,

            'payment_status_text' =>
                $this->getPaymentStatusText(),


            'subtotal' =>
                (float) $this->subtotal,

            'shipping_price' =>
                (float) $this->shipping_price,

            'total_price' =>
                (float) $this->total_price,


            'address' => [

                'full_name' =>
                    $this->full_name,

                'phone' =>
                    $this->phone,

                'city' =>
                    $this->city,

                'district' =>
                    $this->district,

                'neighborhood' =>
                    $this->neighborhood,

                'address_text' =>
                    $this->address_text,
            ],


            'items' =>

                $this->whenLoaded(

                    'items',

                    fn() =>

                    $this->items->map(

                        function ($item) {

                            return [

                                'product_name' =>
                                    $item->product_name,

                                'variant' =>
                                    $item->variant_name,

                                'size' =>
                                    $item->size_value,

                                'quantity' =>
                                    $item->quantity,

                                'price' =>
                                    (float) $item->price,

                                'subtotal' =>

                                    (float) (

                                        $item->price *

                                        $item->quantity
                                    ),
                                'image' =>

                                    optional(

                                        $item
                                            ->variant
                                            ?->images
                                                ?->first()

                                    )->image_path

                                    ? url(
                                        'api/image/' .

                                        optional(

                                            $item
                                                ->variant
                                                ?->images
                                                    ?->first()

                                        )->image_path
                                    )

                                    : null,
                            ];
                        }
                    )
                ),


            'created_at' =>

                $this->created_at
                    ->format('d.m.Y H:i'),
        ];
    }


    protected function generateOrderNumber(): string
    {
        return

            'SNK-' .

            $this->created_at->year .

            '-' .

            str_pad(

                $this->id,

                5,

                '0',

                STR_PAD_LEFT
            );
    }


    protected function getStatusText(): string
    {
        return match ($this->status) {

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
        };
    }


    protected function getPaymentStatusText(): string
    {
        return match ($this->payment_status) {

            'pending' =>
            'Beklemede',

            'paid' =>
            'Ödendi',

            'failed' =>
            'Başarısız',

            default =>
            'Bilinmiyor'
        };
    }


    protected function getStatusStep(): int
    {
        return match ($this->status) {

            'pending' => 1,

            'approved' => 2,

            'supplying' => 3,

            'packaging' => 4,

            'shipped' => 5,

            'out_for_delivery' => 6,

            'delivered' => 7,

            'completed' => 8,

            'cancelled' => 0,

            default => 0
        };
    }
}