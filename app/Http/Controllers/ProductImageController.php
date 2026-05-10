<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductImageResource;
use Illuminate\Http\Request;
use App\Services\ProductImageService;
use App\Http\Requests\StoreProductImageRequest;

class ProductImageController extends Controller
{

    protected ProductImageService $service;

    public function __construct(ProductImageService $service){

        $this->service = $service;
    }

     public function store(StoreProductImageRequest $request)
    {
        $image = $this->service->store(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'data' => new ProductImageResource($image)
        ]);
    }

}
