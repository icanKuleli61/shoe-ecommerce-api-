<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductListResource;
use App\Http\Resources\ProductDetailResource;
use App\Http\Requests\UpdateProductRequest;



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


    public function index()
    {
        $products = $this->service->index();

        return response()->json([
            'success' => true,
            'data' => ProductListResource::collection($products)
        ]);
    }

    public function show($slug)
    {
        $product = $this->service->show($slug);

        return response()->json([
            'success' => true,
            'data' => new ProductDetailResource($product)
        ]);
    }

    public function update(UpdateProductRequest $request, $id){


        $product = $this->service->update($id, $request->validated()
        );

        return response()->json([

             'success' => true,
             'data' => new ProductResource($product)
        ]);
    }

    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Ürün silindi'
        ]);
    }

    public function restore($id)
    {
        $product = $this->service->restore($id);

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product)
        ]);
    }

    
}
