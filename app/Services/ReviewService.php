<?php

namespace App\Services;

use App\Models\Review;
use App\Models\OrderItem;
use App\Exceptions\BaseException;
use App\Enums\ErrorCode;

class ReviewService
{
    public function store(array $data)
    {
        $userId = $this->getUserId();

        $this->checkPurchased($userId, $data['product_id']);

        $this->checkAlreadyReviewed($userId, $data['product_id']);

        return $this->createReview($userId, $data);
    }

    private function getUserId()
    {
        $userId = auth()->id();

        if (!$userId) {
            throw new BaseException(ErrorCode::UNAUTHORIZED);
        }

        return $userId;
    }

    private function checkPurchased($userId, $productId)
    {
        $hasPurchased = OrderItem::whereHas('order', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('product_id', $productId)
            ->exists();

        if (!$hasPurchased) {
            throw new BaseException(ErrorCode::FORBIDDEN);
        }
    }

    private function checkAlreadyReviewed($userId, $productId)
    {
        $exists = Review::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {
            throw new BaseException(ErrorCode::ALREADY_EXISTS);
        }
    }

    private function createReview($userId, array $data)
    {
        return Review::create([
            'user_id' => $userId,
            'product_id' => $data['product_id'],
            'rating' => $data['rating'],
            'comment' => $data['comment'],
        ]);
    }

    public function getByProduct($productId)
    {
        return Review::with('user')
            ->where('product_id', $productId)
            ->latest()
            ->get();
    }

    public function getAverageRating($productId)
    {
        return Review::where('product_id', $productId)
            ->avg('rating') ?? 0;
    }

    public function getReviewCount($productId)
    {
        return Review::where('product_id', $productId)
            ->count();
    }

    public function getRatingDistribution($productId)
    {
        return Review::where('product_id', $productId)
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');
    }
}