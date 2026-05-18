<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductListResource;
use App\Http\Resources\ProductDetailResource;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\FilterProductRequest;
use App\Http\Resources\AdminProductListResource;
use App\Http\Requests\UpdateFullProductRequest;


class ProductController extends Controller
{
    protected ProductService $service;

    public function __construct(ProductService $service)
    {
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

    public function update(UpdateProductRequest $request, $id)
    {


        $product = $this->service->update(
            $id,
            $request->validated()
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

    public function filter(
        FilterProductRequest $request
    ) {

        $products = $this->service->filter(
            $request->validated()
        );

        return ProductListResource::collection(
            $products
        );
    }

    public function adminIndex()
    {
        $products =
            $this->service->adminIndex();

        return response()->json([

            'success' => true,

            'data' => AdminProductListResource::collection(
                $products
            ),

            'meta' => [

                'current_page' =>
                    $products->currentPage(),

                'last_page' =>
                    $products->lastPage(),

                'per_page' =>
                    $products->perPage(),

                'total' =>
                    $products->total(),
            ]
        ]);
    }

    public function adminFilter(
        FilterProductRequest $request
    ) {

        $products =

            $this->service->adminFilter(
                $request->validated()
            );

        return response()->json([

            'success' => true,

            'data' => AdminProductListResource::collection(
                $products
            ),

            'meta' => [

                'current_page' =>
                    $products->currentPage(),

                'last_page' =>
                    $products->lastPage(),

                'per_page' =>
                    $products->perPage(),

                'total' =>
                    $products->total(),
            ]
        ]);
    }

    public function adminUpdate(
        UpdateFullProductRequest $request,
        $id
    ) {

        $product =

            $this->service->adminUpdate(

                $id,

                $request->validated()
            );



        return response()->json([

            'success' => true,

            'message' =>

                'Ürün güncellendi',

            'data' => new ProductDetailResource(
                $product
            )
        ]);
    }

    public function adminShow($id)
    {
        $product =

            $this->service->adminShow($id);

        return response()->json([

            'success' => true,

            'data' => new ProductDetailResource(
                $product
            )
        ]);
    }
}
