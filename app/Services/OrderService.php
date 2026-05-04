<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\VariantSize;
use Illuminate\Support\Facades\DB;
use App\Exceptions\BaseException;
use App\Enums\ErrorCode;

class OrderService
{
    public function createFromCart(array $data)
    {
        return DB::transaction(function () use ($data) {

            $cartItems = $this->getUserCart();

            $total = $this->calculateTotal($cartItems);

            $order = $this->createOrder($data, $total);

            $this->createOrderItems($order, $cartItems);

            $this->clearCart();

            return $order->load('items');
        });
    }

    private function getUserCart()
    {
        $userId = auth()->id();

        if (!$userId) {
            throw new BaseException(ErrorCode::UNAUTHORIZED);
        }

        $cartItems = CartItem::with(['variant.color', 'variant.product', 'size'])
            ->where('user_id', $userId)
            ->get();

        if ($cartItems->isEmpty()) {
            throw new BaseException(ErrorCode::CART_EMPTY);
        }

        return $cartItems;
    }

    private function calculateTotal($cartItems)
    {
        return $cartItems->sum(fn($item) => $item->price * $item->quantity);
    }

    private function createOrder(array $data, $total)
    {
        return Order::create([
            'user_id' => auth()->id(),
            'total_price' => $total,
            'status' => Order::STATUS_PENDING,

            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'city' => $data['city'],
            'district' => $data['district'],
            'neighborhood' => $data['neighborhood'],
            'address_text' => $data['address_text'],
        ]);
    }

    private function createOrderItems($order, $cartItems)
    {
        foreach ($cartItems as $item) {

            $size = VariantSize::find($item->size_id);

            if (!$size) {
                throw new BaseException(ErrorCode::NOT_FOUND);
            }

            if ($size->stock < $item->quantity) {
                throw new BaseException(ErrorCode::INSUFFICIENT_STOCK);
            }

            // 🔥 stok düş
            $size->decrement('stock', $item->quantity);

            // 🔥 order item (snapshot)
            OrderItem::create([
                'order_id' => $order->id,
                'variant_id' => $item->variant_id,
                'size_id' => $item->size_id,

                'product_name' => $item->variant->product->name ?? 'Ürün',
                'variant_name' => $item->variant->color->name ?? null,
                'size_value' => $item->size->size ?? null,

                'quantity' => $item->quantity,
                'price' => $item->price,
            ]);
        }
    }

    private function clearCart()
    {
        CartItem::where('user_id', auth()->id())->delete();
    }

    public function index()
    {
        $userId = auth()->id();

        if (!$userId) {
            throw new BaseException(ErrorCode::UNAUTHORIZED);
        }

        return Order::where('user_id', $userId)
            ->latest()
            ->get();
    }
    public function show($id)
    {
        $userId = auth()->id();

        if (!$userId) {
            throw new BaseException(ErrorCode::UNAUTHORIZED);
        }

        $order = Order::with(['items'])
            ->where('user_id', $userId)
            ->find($id);

        if (!$order) {
            throw new BaseException(ErrorCode::NOT_FOUND);
        }

        return $order;
    }

    public function updateStatus($id, $status)
    {
        $order = Order::find($id);

        if (!$order) {
            throw new BaseException(ErrorCode::NOT_FOUND);
        }

        $this->checkStatusTransition($order->status, $status);

        $order->status = $status;
        $order->save();

        return $order;
    }

    private function checkStatusTransition($current, $new)
    {
        $allowed = [
            Order::STATUS_PENDING => [
                Order::STATUS_PAID,
                Order::STATUS_CANCELLED
            ],

            Order::STATUS_PAID => [
                Order::STATUS_SHIPPED,
                Order::STATUS_CANCELLED
            ],

            Order::STATUS_SHIPPED => [
                Order::STATUS_COMPLETED
            ],

            Order::STATUS_COMPLETED => [],

            Order::STATUS_CANCELLED => [],
        ];

        if (!in_array($new, $allowed[$current] ?? [])) {
            throw new BaseException(ErrorCode::INVALID_STATUS_TRANSITION);
        }
    }

    public function pay($id)
    {
        $order = Order::find($id);

        if (!$order) {
            throw new BaseException(ErrorCode::NOT_FOUND);
        }

        if ($order->status !== Order::STATUS_PENDING) {
            throw new BaseException(ErrorCode::INVALID_STATUS_TRANSITION);
        }

        $paymentResult = app(\App\Services\PaymentService::class)->pay($order);

        if (!$paymentResult['success']) {
            throw new BaseException(ErrorCode::PAYMENT_FAILED);
        }

        $order->status = Order::STATUS_PAID;
        $order->save();

        return $order;
    }
}