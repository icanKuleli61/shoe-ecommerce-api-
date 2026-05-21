<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Models\VariantSize;
use App\Models\WalletTransaction;

use Illuminate\Support\Facades\DB;

use App\Exceptions\BaseException;
use App\Enums\ErrorCode;

class OrderService
{
    /*
    |--------------------------------------------------------------------------
    | SİPARİŞ OLUŞTUR
    |--------------------------------------------------------------------------
    */

    public function createFromCart(
        array $data
    ): Order {

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION DIŞI OKUMA İŞLEMLERİ
        |--------------------------------------------------------------------------
        */

        $cartItems = $this->getUserCart();

        $address = $this->getUserAddress(
            $data['address_id']
        );

        $subtotal = $this->calculateSubtotal(
            $cartItems
        );

        $shippingPrice = $this->calculateShipping(
            $subtotal
        );

        $totalPrice =
            $subtotal + $shippingPrice;

        $this->validateStock(
            $cartItems
        );

        $this->validatePaymentMethod(
            $data['payment_method']
        );

        /*
        |--------------------------------------------------------------------------
        | SADECE KRİTİK DB YAZMALARI TRANSACTION İÇİNDE
        |--------------------------------------------------------------------------
        */

        return DB::transaction(function () use ($data, $address, $subtotal, $shippingPrice, $totalPrice, $cartItems) {

            /*
            |--------------------------------------------------------------------------
            | ORDER
            |--------------------------------------------------------------------------
            */

            $order = $this->createOrder(

                data: $data,

                address: $address,

                subtotal: $subtotal,

                shippingPrice: $shippingPrice,

                totalPrice: $totalPrice
            );

            /*
            |--------------------------------------------------------------------------
            | ORDER ITEMS
            |--------------------------------------------------------------------------
            */

            $this->createOrderItems(

                order: $order,

                cartItems: $cartItems
            );

            /*
            |--------------------------------------------------------------------------
            | PAYMENT
            |--------------------------------------------------------------------------
            */

            $this->processPayment(
                $order
            );

            /*
            |--------------------------------------------------------------------------
            | STOCK
            |--------------------------------------------------------------------------
            */

            $this->decreaseStocks(
                $cartItems
            );

            /*
            |--------------------------------------------------------------------------
            | CLEAR CART
            |--------------------------------------------------------------------------
            */

            $this->clearCart();

            return $order->load(
                'items'
            );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | USER CART
    |--------------------------------------------------------------------------
    */

    protected function getUserCart()
    {
        $userId = auth()->id();

        if (!$userId) {

            throw new BaseException(
                ErrorCode::UNAUTHORIZED
            );
        }

        $cartItems = CartItem::with([

            'variant.product',
            'variant.color',
            'size'

        ])
            ->where(
                'user_id',
                $userId
            )
            ->get();

        if ($cartItems->isEmpty()) {

            throw new BaseException(
                ErrorCode::EMPTY_CART
            );
        }

        return $cartItems;
    }

    /*
    |--------------------------------------------------------------------------
    | USER ADDRESS
    |--------------------------------------------------------------------------
    */

    protected function getUserAddress(
        int $addressId
    ): Address {

        $address = Address::with([

            'city',
            'district',
            'neighborhood'

        ])
            ->where(
                'user_id',
                auth()->id()
            )
            ->find($addressId);

        if (!$address) {

            throw new BaseException(
                ErrorCode::NOT_FOUND
            );
        }

        return $address;
    }

    /*
    |--------------------------------------------------------------------------
    | SUBTOTAL
    |--------------------------------------------------------------------------
    */

    protected function calculateSubtotal(
        $cartItems
    ): float {

        return $cartItems->sum(

            fn($item) =>

            $item->price *

            $item->quantity
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SHIPPING
    |--------------------------------------------------------------------------
    */

    protected function calculateShipping(
        float $subtotal
    ): float {

        return $subtotal >= 3000
            ? 0
            : 99;
    }

    /*
    |--------------------------------------------------------------------------
    | STOCK VALIDATION
    |--------------------------------------------------------------------------
    */

    protected function validateStock(
        $cartItems
    ): void {

        foreach ($cartItems as $item) {

            $size = VariantSize::find(
                $item->size_id
            );

            if (!$size) {

                throw new BaseException(
                    ErrorCode::NOT_FOUND
                );
            }

            if (

                $size->stock <
                $item->quantity

            ) {

                throw new BaseException(
                    ErrorCode::INSUFFICIENT_STOCK
                );
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PAYMENT METHOD VALIDATION
    |--------------------------------------------------------------------------
    */

    protected function validatePaymentMethod(
        string $paymentMethod
    ): void {

        $allowedMethods = [

            Order::PAYMENT_METHOD_CARD,

            Order::PAYMENT_METHOD_WALLET
        ];

        if (

            !in_array(
                $paymentMethod,
                $allowedMethods
            )

        ) {

            throw new BaseException(
                ErrorCode::PAYMENT_FAILED
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE ORDER
    |--------------------------------------------------------------------------
    */

    protected function createOrder(

        array $data,

        Address $address,

        float $subtotal,

        float $shippingPrice,

        float $totalPrice

    ): Order {

        return Order::create([

            'user_id' =>
                auth()->id(),

            'address_id' =>
                $address->id,

            'subtotal' =>
                $subtotal,

            'shipping_price' =>
                $shippingPrice,

            'total_price' =>
                $totalPrice,

            'payment_method' =>
                $data['payment_method'],

            'payment_status' =>
                Order::PAYMENT_STATUS_PENDING,

            'status' =>
                Order::STATUS_PENDING,

            'full_name' =>
                $address->full_name,

            'phone' =>
                $address->phone,

            'city' =>
                $address->city->name,

            'district' =>
                $address->district->name,

            'neighborhood' =>
                $address->neighborhood->name,

            'address_text' =>
                $address->address,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE ORDER ITEMS
    |--------------------------------------------------------------------------
    */

    protected function createOrderItems(

        Order $order,

        $cartItems

    ): void {

        $items = [];

        $now = now();

        foreach ($cartItems as $item) {

            $items[] = [

                'order_id' =>
                    $order->id,

                'variant_id' =>
                    $item->variant_id,

                'size_id' =>
                    $item->size_id,

                'product_name' =>

                    $item->variant
                        ?->product
                            ?->name

                    ?? 'Ürün',

                'variant_name' =>

                    $item->variant
                        ?->color
                            ?->name,

                'size_value' =>

                    (string) (
                        $item->size
                                ?->size
                    ),

                'quantity' =>
                    $item->quantity,

                'price' =>
                    $item->price,

                'created_at' =>
                    $now,

                'updated_at' =>
                    $now,
            ];
        }

        OrderItem::insert($items);
    }

    /*
    |--------------------------------------------------------------------------
    | PROCESS PAYMENT
    |--------------------------------------------------------------------------
    */

    protected function processPayment(
        Order $order
    ): void {

        if (

            $order->payment_method ===
            Order::PAYMENT_METHOD_CARD

        ) {

            $this->markOrderAsPaid(
                $order
            );

            return;
        }

        $this->processWalletPayment(
            $order
        );
    }

    /*
    |--------------------------------------------------------------------------
    | WALLET PAYMENT
    |--------------------------------------------------------------------------
    */

    protected function processWalletPayment(
        Order $order
    ): void {

        $wallet = auth()
            ->user()
            ->wallet;

        if (!$wallet) {

            throw new BaseException(
                ErrorCode::NOT_FOUND
            );
        }

        if (

            $wallet->balance <
            $order->total_price

        ) {

            throw new BaseException(
                ErrorCode::INSUFFICIENT_BALANCE
            );
        }

        /*
        |--------------------------------------------------------------------------
        | WALLET LOCK
        |--------------------------------------------------------------------------
        */

        $wallet = $wallet->lockForUpdate()->first();

        $wallet->balance -=
            $order->total_price;

        $wallet->save();

        WalletTransaction::create([

            'wallet_id' =>
                $wallet->id,

            'type' =>
                'payment',

            'amount' =>
                -$order->total_price,

            'current_balance' =>
                $wallet->balance,

            'description' =>
                'Sipariş ödemesi yapıldı.',

            'reference_type' =>
                'order',

            'reference_id' =>
                $order->id
        ]);

        $this->markOrderAsPaid(
            $order
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MARK AS PAID
    |--------------------------------------------------------------------------
    */

    protected function markOrderAsPaid(
        Order $order
    ): void {

        $order->payment_status =
            Order::PAYMENT_STATUS_PAID;

        $order->save();
    }

    /*
    |--------------------------------------------------------------------------
    | DECREASE STOCKS
    |--------------------------------------------------------------------------
    */

    protected function decreaseStocks(
        $cartItems
    ): void {

        foreach ($cartItems as $item) {

            /*
            |--------------------------------------------------------------------------
            | ROW LOCK
            |--------------------------------------------------------------------------
            */

            $variantSize = VariantSize::lockForUpdate()
                ->find($item->size_id);

            if (!$variantSize) {

                throw new BaseException(
                    ErrorCode::NOT_FOUND
                );
            }

            /*
            |--------------------------------------------------------------------------
            | STOCK CHECK
            |--------------------------------------------------------------------------
            */

            if (

                $variantSize->stock <
                $item->quantity

            ) {

                throw new BaseException(
                    ErrorCode::INSUFFICIENT_STOCK
                );
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE STOCK
            |--------------------------------------------------------------------------
            */

            $variantSize->stock -=
                $item->quantity;

            $variantSize->save();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CLEAR CART
    |--------------------------------------------------------------------------
    */

    protected function clearCart(): void
    {
        CartItem::where(

            'user_id',
            auth()->id()

        )->delete();
    }
}