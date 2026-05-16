<?php

namespace App\Services;

use App\Models\CartItem;

use App\Exceptions\BaseException;
use App\Enums\ErrorCode;

class CheckoutService
{
    public function validateCart(): array
    {
        $userId = auth()->id();

        if (!$userId) {

            throw new BaseException(
                ErrorCode::UNAUTHORIZED
            );
        }

        $cartItems = $this->getCartItems(
            $userId
        );

        $this->ensureCartNotEmpty(
            $cartItems
        );

        $subtotal = $this->calculateSubtotal(
            $cartItems
        );

        return [

            'items' => $cartItems,

            'subtotal' =>
                round($subtotal, 2),

            'shipping' => 0,

            'total' =>
                round($subtotal, 2),
        ];
    }


    protected function getCartItems(
        int $userId
    )
    {
        return CartItem::with([

            'variant.product',
            'variant.color',
            'variant.images',
            'size'

        ])
            ->where(
                'user_id',
                $userId
            )
            ->get();
    }


    protected function ensureCartNotEmpty(
        $cartItems
    ): void {

        if ($cartItems->isEmpty()) {

            throw new BaseException(
                ErrorCode::EMPTY_CART
            );
        }
    }


    protected function calculateSubtotal(
        $cartItems
    ): float {

        $subtotal = 0;

        foreach ($cartItems as $item) {

            $this->validateCartItem(
                $item
            );

            $subtotal +=

                $item->price *

                $item->quantity;
        }

        return $subtotal;
    }


    protected function validateCartItem(
        $item
    ): void {

        $this->ensureProductExists(
            $item
        );

        $this->ensureSizeExists(
            $item
        );

        $this->ensureStockAvailable(
            $item
        );

        $this->syncPrice(
            $item
        );
    }


    protected function ensureProductExists(
        $item
    ): void {

        if (

            !$item->variant ||

            !$item->variant->product

        ) {

            throw new BaseException(
                ErrorCode::PRODUCT_NOT_FOUND
            );
        }
    }


    protected function ensureSizeExists(
        $item
    ): void {

        if (!$item->size) {

            throw new BaseException(
                ErrorCode::NOT_FOUND
            );
        }
    }


    protected function ensureStockAvailable(
        $item
    ): void {

        if (

            $item->size->stock <

            $item->quantity

        ) {

            throw new BaseException(
                ErrorCode::INSUFFICIENT_STOCK
            );
        }
    }


    protected function syncPrice(
        $item
    ): void {

        if (

            $item->price !=

            $item->size->price

        ) {

            $item->price =
                $item->size->price;

            $item->save();
        }
    }
}