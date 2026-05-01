<?php

namespace App\Http\Controllers;

use App\Services\AddressService;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Resources\AddressResource;

class AddressController extends Controller
{
    protected AddressService $service;

    public function __construct(AddressService $service)
    {
        $this->service = $service;
    }

    // 🔥 POST /addresses
    public function store(StoreAddressRequest $request)
    {
        $user_id = 1; // şimdilik sabit (auth yok)

        $address = $this->service->store(
            $user_id,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'data' => new AddressResource($address)
        ]);
    }

    // 🔥 GET /addresses
    public function index()
    {
        $user_id = 1;

        $addresses = $this->service->index($user_id);

        return response()->json([
            'success' => true,
            'data' => AddressResource::collection($addresses)
        ]);
    }
}