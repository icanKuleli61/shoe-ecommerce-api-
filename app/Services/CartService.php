<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\VariantSize;
use App\Exceptions\BaseException;
use App\Enums\ErrorCode;

class CartService
{

    public function add(array $data)
    {
        $userId = auth()->id();

        if (!$userId) {
            throw new BaseException(ErrorCode::UNAUTHORIZED);
        }

        // 🔥 aynı ürün sepette var mı?
        $cartItem = CartItem::where('user_id', $userId)
            ->where('variant_id', $data['variant_id'])
            ->where('size_id', $data['size_id'])
            ->first();

        if ($cartItem) {
            // ✔ varsa quantity artır
            $cartItem->quantity += $data['quantity'];
            $cartItem->save();

            return $cartItem;
        }

        // 🔥 size var mı?
        $size = VariantSize::find($data['size_id']);

        if (!$size) {
            throw new BaseException(ErrorCode::NOT_FOUND);
        }

        // 🔥 stok kontrol (opsiyonel ama önerilir)
        if ($size->stock < $data['quantity']) {
            throw new BaseException(ErrorCode::INSUFFICIENT_STOCK);
        }

        // ✔ yeni kayıt
        return CartItem::create([
            'user_id' => $userId,
            'variant_id' => $data['variant_id'],
            'size_id' => $data['size_id'],
            'quantity' => $data['quantity'],
            'price' => $size->price, // 🔥 snapshot
        ]);
    }

    /**
     * 🧾 Sepeti getir
     */
    public function get()
    {
        $userId = auth()->id();

        return CartItem::with([
            'variant.color',
            'size'
        ])
        ->where('user_id', $userId)
        ->get();
    }

    /**
     * 🔄 Sepet item güncelle
     */
    public function update($id, array $data)
    {
        $cartItem = CartItem::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$cartItem) {
            throw new BaseException(ErrorCode::NOT_FOUND);
        }

        $cartItem->quantity = $data['quantity'];
        $cartItem->save();

        return $cartItem;
    }

    /**
     * ❌ Sepetten sil
     */
    public function remove($id)
    {
        $cartItem = CartItem::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$cartItem) {
            throw new BaseException(ErrorCode::NOT_FOUND);
        }

        $cartItem->delete();

        return true;
    }

    /**
     * 🧹 Sepeti temizle
     */
    public function clear()
    {
        CartItem::where('user_id', auth()->id())->delete();

        return true;
    }
}