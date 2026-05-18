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
        $userId =
            $this->getUserId();

        $favorite =

            $this->findFavorite(

                $userId,

                $productId
            );

        if ($favorite) {

            $favorite->delete();

            return false;
        }

        Favorite::create([

            'user_id' =>
                $userId,

            'product_id' =>
                $productId,
        ]);

        return true;
    }

    public function index()
    {
        return Product::with([

            'brand',

            'variants.images',

            'variants.sizes'

        ])
            ->withAvg(

                'reviews',

                'rating'
            )

            ->withCount(
                'reviews'
            )

            ->whereHas(

                'favorites',

                function ($query) {

                    $query->where(

                        'user_id',

                        auth()->id()
                    );
                }
            )

            ->latest()

            ->paginate(10);
    }

    private function getUserId(): int
    {
        $userId =
            auth()->id();

        if (!$userId) {

            throw new BaseException(

                ErrorCode::UNAUTHORIZED
            );
        }

        return $userId;
    }

    private function findFavorite(

        $userId,

        $productId

    ): ?Favorite {

        return Favorite::where(

            'user_id',

            $userId
        )
            ->where(

                'product_id',

                $productId
            )
            ->first();
    }
}