<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderListResource;
use App\Http\Requests\UpdateOrderStatusRequest;

class OrderController extends Controller
{
    protected OrderService $service;

    public function __construct(OrderService $service){
        $this->service = $service;
    }

    public function store(StoreOrderRequest $request)
    {
        $order = $this->service->createFromCart(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order)
        ]);
    }


    public function index()
    {
        $orders = $this->service->index();

        return response()->json([
            'success' => true,
            'data' => OrderListResource::collection($orders)
        ]);
    }

    public function show($id)
    {
        $order = $this->service->show($id);

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order)
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, $id)
    {
        $order = $this->service->updateStatus(
            $id,
            $request->validated()['status']
        );

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order)
        ]);
    }


    public function pay($id)
    {
        $order = $this->service->pay($id);

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order)
        ]);
    }
}
