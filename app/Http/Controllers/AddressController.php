<?php

namespace App\Http\Controllers;

use App\Services\AddressService;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Resources\AddressResource;
use App\Http\Requests\UpdateAddressRequest;

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

    public function index()
    {
        $user_id = 1;

        $addresses = $this->service->index($user_id);

        return response()->json([
            'success' => true,
            'data' => AddressResource::collection($addresses)
        ]);
    }

    public function update(UpdateAddressRequest $request, $id)
    {
        $user_id = 1; // şimdilik sabit

        $address = $this->service->update(
            $user_id,
            $id,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'data' => new AddressResource($address)
        ]);
    }

    public function destroy($id)
    {
        $user_id = 1;

        $this->service->delete($user_id, $id);

        return response()->json([
            'success' => true,
            'message' => 'Adres silindi'
        ]);
    }

    
}