<?php

namespace App\Http\Controllers;

use App\Services\OrderService;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;

use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderListResource;
use App\Http\Resources\OrderDetailResource;

class OrderController extends Controller
{
    public function __construct(

        protected OrderService $service

    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(
        StoreOrderRequest $request
    ) {

        $order =

            $this->service
                ->createFromCart(

                    $request->validated()

                );

        return response()->json([

            'success' => true,

            'message' =>
                'Sipariş başarıyla oluşturuldu.',

            'data' =>
                new OrderResource(
                    $order
                )

        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $orders =
            $this->service->index();

        return response()->json([

            'success' => true,

            'data' =>

                OrderListResource::collection(
                    $orders
                )
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $order =
            $this->service->show($id);

        return response()->json([

            'success' => true,

            'data' =>
                new OrderResource(
                    $order
                )
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS
    |--------------------------------------------------------------------------
    */

    public function updateStatus(

        UpdateOrderStatusRequest $request,

        $id

    ) {

        $order =

            $this->service
                ->updateStatus(

                    $id,

                    $request->validated()['status']
                );

        return response()->json([

            'success' => true,

            'message' =>
                'Sipariş durumu güncellendi.',

            'data' =>
                new OrderResource(
                    $order
                )
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | CANCEL
    |--------------------------------------------------------------------------
    */

    public function cancel($id)
    {
        $order =
            $this->service
                ->cancel($id);

        return response()->json([

            'success' => true,

            'message' =>
                'Sipariş iptal edildi.',

            'data' =>
                new OrderResource(
                    $order
                )
        ]);
    }

    public function detail($id)
    {
        $order =
            $this->service
                ->show($id);

        return response()->json([

            'success' => true,

            'data' =>

                new OrderDetailResource(
                    $order
                )
        ]);
    }
}