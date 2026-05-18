<?php

namespace App\Http\Controllers;

use App\Services\HomeService;

use App\Http\Resources\ProductListResource;

class HomeController extends Controller
{
    public function __construct(

        protected HomeService $service

    ) {
    }


    public function bestSellers()
    {
        $products =

            $this->service
                ->bestSellers();

        return response()->json([

            'success' => true,

            'data' =>

                ProductListResource::collection(
                    $products
                )
        ]);
    }

    public function newArrivals()
    {
        $products =

            $this->service
                ->newArrivals();

        return response()->json([

            'success' => true,

            'data' =>

                ProductListResource::collection(
                    $products
                )
        ]);
    }

    public function discountedProducts()
    {
        $products =

            $this->service
                ->discountedProducts();

        return response()->json([

            'success' => true,

            'data' =>

                ProductListResource::collection(
                    $products
                )
        ]);
    }
}