<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductVariantService;
use App\Http\Requests\StoreProductVariantRequest;
use App\Http\Resources\ProductVariantResource;


class ProductVariantController extends Controller
{
    protected ProductVariantService $service;

    public function __construct(ProductVariantService $service){

        $this->service = $service;
    }

    public function store(StoreProductVariantRequest $request)
    {
        $variant = $this->service->store(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'data' => new ProductVariantResource($variant)
        ]);
    }

    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Variant silindi'
        ]);
    }



}
