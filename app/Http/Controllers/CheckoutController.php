<?php

namespace App\Http\Controllers;

use App\Services\CheckoutService;

use App\Http\Resources\CheckoutResource;

class CheckoutController extends Controller
{
    public function __construct(

        protected CheckoutService $checkoutService

    ) {
    }


    public function validateCart()
    {
        $data =

            $this->checkoutService
                ->validateCart();

        return response()->json([

            'success' => true,

            'data' => new CheckoutResource(
                $data
            )
        ]);
    }
}