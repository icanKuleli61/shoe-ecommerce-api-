<?php

namespace App\Http\Controllers;

use App\Services\BrandService;
use App\Http\Requests\StoreBrandRequest;


class BrandController extends Controller
{
    protected BrandService $service;

    public function __construct(
        BrandService $service
    ) {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json([

            'success' => true,

            'data' =>
                $this->service->index()
        ]);
    }

    public function store(
        StoreBrandRequest $request
    ) {

        $brand =
            $this->service->store(
                $request->validated()
            );



        return response()->json([

            'success' => true,

            'data' => $brand
        ]);
    }

    public function destroy($id)
    {

        $this->service->delete($id);



        return response()->json([

            'success' => true,

            'message' =>
                'Marka silindi'
        ]);
    }
}