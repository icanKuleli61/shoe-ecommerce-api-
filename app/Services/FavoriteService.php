<?php

namespace App\Services;

use App\Models\Favorite;
use App\Exceptions\BaseException;
use App\Enums\ErrorCode;

class FavoriteService
{
    public function toggle($productId)
    {
        $userId = $this->getUserId();

        $favorite = $this->findFavorite($userId, $productId);

        if ($favorite) {
            return $this->remove($favorite);
        }

        return $this->add($userId, $productId);
    }

    private function getUserId()
    {
        $userId = auth()->id();

        if (!$userId) {
            throw new BaseException(ErrorCode::UNAUTHORIZED);
        }

        return $userId;
    }

    private function findFavorite($userId, $productId)
    {
        return Favorite::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();
    }

    private function add($userId, $productId)
    {
        return Favorite::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);
    }

    private function remove($favorite)
    {
        $favorite->delete();

        return true;
    }

    public function getUserFavorites()
    {
        return \App\Models\Product::withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->whereHas('favorites', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->get();
    }
}