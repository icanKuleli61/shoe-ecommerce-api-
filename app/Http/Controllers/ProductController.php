<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;



class ProductController extends Controller
{
    protected ProductService $service;

    public function __construct(ProductService $service){
        $this->service = $service;
    }


     public function store(StoreProductRequest $request)
    {
        $product = $this->service->store(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product)
        ]);
    }
}
