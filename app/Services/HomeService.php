<?php

namespace App\Services;

use App\Models\Product;

class HomeService
{


    public function bestSellers()
    {
        return Product::with([

            'favorites',

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

            ->latest()

            ->take(8)

            ->get();
    }

    public function newArrivals()
    {
        return Product::with([

            'favorites',

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

            ->latest()

            ->take(8)

            ->get();
    }

    public function discountedProducts()
    {
        return Product::with([

            'favorites',

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

            ->take(8)

            ->get();
    }
}