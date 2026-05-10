<?php

namespace App\Services;

use App\Models\Favorite;
use App\Models\Product;
use App\Exceptions\BaseException;
use App\Enums\ErrorCode;

class FavoriteService
{
    public function toggle($productId): bool
    {
        $userId = $this->getUserId();

        $favorite = $this->findFavorite($userId, $productId);

        if ($favorite) {
            $favorite->delete();
            return false; 
        }

        Favorite::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        return true; 
    }

    private function getUserId(): int
    {
        $userId = auth()->id();

        if (!$userId) {
            throw new BaseException(ErrorCode::UNAUTHORIZED);
        }

        return $userId;
    }

    private function findFavorite($userId, $productId): ?Favorite
    {
        return Favorite::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();
    }

    public function getUserFavorites()
    {
        return Product::withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->whereHas('favorites', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->get();
    }
}