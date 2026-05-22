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
    public function createFromCart(array $data): Order
    {
        try {

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

            $order = $this->createOrder(
                $data,
                $address,
                $subtotal,
                $shippingPrice,
                $totalPrice
            );

            $this->createOrderItems(
                $order,
                $cartItems
            );

            $this->processPayment(
                $order
            );

            $this->decreaseStocks(
                $cartItems
            );

            $this->clearCart();

            return $order->load('items');

        } catch (\Throwable $e) {

            logger()->error($e);

            throw $e;
        }
    }

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


    protected function calculateSubtotal(
        $cartItems
    ): float {

        return $cartItems->sum(

            fn($item) =>

            $item->price *

            $item->quantity
        );
    }


    protected function calculateShipping(
        float $subtotal
    ): float {

        return $subtotal >= 3000
            ? 0
            : 99;
    }


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


    protected function createOrderItems(

        Order $order,

        $cartItems

    ): void {

        foreach ($cartItems as $item) {

            OrderItem::create([

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

                    $item->size
                            ?->size,

                'quantity' =>
                    $item->quantity,

                'price' =>
                    $item->price,
            ]);
        }
    }


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

        $wallet->decrement(

            'balance',

            $order->total_price
        );

        WalletTransaction::create([

            'wallet_id' =>
                $wallet->id,

            'type' =>
                'payment',

            'amount' =>
                -$order->total_price,

            'current_balance' =>
                $wallet->fresh()->balance,

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


    protected function markOrderAsPaid(
        Order $order
    ): void {

        $order->payment_status =
            Order::PAYMENT_STATUS_PAID;
        $order->save();
    }


    protected function decreaseStocks(
        $cartItems
    ): void {

        foreach ($cartItems as $item) {

            VariantSize::find(
                $item->size_id
            )?->decrement(

                    'stock',

                    $item->quantity
                );
        }
    }

    protected function clearCart(): void
    {
        CartItem::where(

            'user_id',
            auth()->id()

        )->delete();
    }

    public function show($id): Order
    {
        $order = Order::with([

            'items.variant.images',
            'address',
            'user'

        ])
            ->where(
                'user_id',
                auth()->id()
            )
            ->find($id);

        if (!$order) {

            throw new BaseException(
                ErrorCode::NOT_FOUND
            );
        }

        return $order;
    }
    public function index()
    {
        return Order::with([

            'items.variant.images'

        ])
            ->withCount('items')
            ->where(
                'user_id',
                auth()->id()
            )
            ->latest()
            ->paginate(10);
    }


    protected function validateCancelableOrder(
        Order $order
    ): void {

        $cancelableStatuses = [

            Order::STATUS_PENDING,

            Order::STATUS_APPROVED,

            Order::STATUS_SUPPLYING,

            Order::STATUS_PACKAGING
        ];

        if (

            !in_array(
                $order->status,
                $cancelableStatuses
            )

        ) {

            throw new BaseException(
                ErrorCode::BAD_REQUEST
            );
        }

        if (

            $order->status ===
            Order::STATUS_CANCELLED

        ) {

            throw new BaseException(
                ErrorCode::BAD_REQUEST
            );
        }
    }


    protected function restoreStocks(
        Order $order
    ): void {

        foreach ($order->items as $item) {

            $variant = VariantSize::find(
                $item->size_id
            );

            if ($variant) {

                $variant->stock +=
                    $item->quantity;

                $variant->save();
            }
        }
    }


    protected function refundWallet(Order $order): void
    {
        if ($order->payment_method !== Order::PAYMENT_METHOD_WALLET) {
            return;
        }

        if ($order->payment_status !== Order::PAYMENT_STATUS_PAID) {
            return;
        }

        $wallet = $order->user?->wallet;
        if (!$wallet) {
            return;
        }

        $wallet->increment('balance', $order->total_price);

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => 'refund',
            'amount' => $order->total_price,
            'current_balance' => $wallet->fresh()->balance,
            'description' => 'Sipariş iptal edildi. Ücret iadesi yapıldı.',
            'reference_type' => 'order',
            'reference_id' => $order->id,
        ]);
    }


    protected function markAsCancelled(
        Order $order
    ): void {

        $order->status =
            Order::STATUS_CANCELLED;

        $order->save();
    }


    public function cancel($id): Order
    {
        $order = Order::with('items')
            ->where(
                'user_id',
                auth()->id()
            )
            ->find($id);

        if (!$order) {

            throw new BaseException(
                ErrorCode::NOT_FOUND
            );
        }

        $this->validateCancelableOrder(
            $order
        );

        $this->restoreStocks(
            $order
        );

        $this->refundWallet(
            $order
        );

        $this->markAsCancelled(
            $order
        );

        return $order->fresh();
    }


    public function complete($id): Order
    {
        $order = Order::where(
            'user_id',
            auth()->id()
        )
            ->find($id);

        if (!$order) {

            throw new BaseException(
                ErrorCode::NOT_FOUND
            );
        }

        if (

            $order->status !==
            Order::STATUS_DELIVERED

        ) {

            throw new BaseException(
                ErrorCode::BAD_REQUEST
            );
        }

        $order->status =
            Order::STATUS_COMPLETED;

        $order->save();

        return $order->fresh();

    }

    public function adminIndex(array $filters)
    {
        $query = Order::with([

            'items.variant.images',
            'user'

        ])
            ->withCount('items');


        if (!empty($filters['search'])) {

            $search =
                $filters['search'];



            $query->where(function ($q) use ($search) {


                $q->where(
                    'id',
                    'like',
                    "%{$search}%"
                )

                    ->orWhereHas('user', function ($user) use ($search) {

                        $user->where(

                            'email',
                            'like',
                            "%{$search}%"
                        );
                    });
            });
        }

        if (!empty($filters['status'])) {

            $query->where(

                'status',

                $filters['status']
            );
        }

        return $query
            ->latest()
            ->paginate(20);
    }



    public function adminShow($id): Order
    {
        $order = Order::with([

            'items.variant.images',
            'user'

        ])->find($id);

        if (!$order) {

            throw new BaseException(
                ErrorCode::NOT_FOUND
            );
        }

        return $order;
    }


    public function adminUpdateStatus($id, string $status): Order
    {
        return DB::transaction(function () use ($id, $status) {
            $order = Order::with(['items', 'user.wallet'])
                ->lockForUpdate()
                ->find($id);

            if (!$order) {
                throw new BaseException(ErrorCode::NOT_FOUND);
            }

            if ($order->status === Order::STATUS_CANCELLED) {
                throw new BaseException(ErrorCode::BAD_REQUEST);
            }

            $allowedTransitions = Order::$statusFlow[$order->status] ?? [];

            if (!in_array($status, $allowedTransitions, true)) {
                throw new BaseException(ErrorCode::BAD_REQUEST);
            }

            if ($status === Order::STATUS_CANCELLED) {
                $this->validateAdminCancelableOrder($order);
                $this->restoreStocks($order);
                $this->refundWallet($order);
                $this->markAsCancelled($order);

                return $order->fresh(['items', 'user']);
            }

            $order->status = $status;
            $order->save();

            return $order->fresh(['items', 'user']);
        });
    }
    protected function validateAdminCancelableOrder(Order $order): void
    {
        $cancelable = [
            Order::STATUS_PENDING,
            Order::STATUS_APPROVED,
            Order::STATUS_SUPPLYING,
            Order::STATUS_PACKAGING,
            Order::STATUS_SHIPPED,           // istemiyorsan çıkar
            Order::STATUS_OUT_FOR_DELIVERY,  // istemiyorsan çıkar
        ];

        if (!in_array($order->status, $cancelable, true)) {
            throw new BaseException(ErrorCode::BAD_REQUEST);
        }
    }
}