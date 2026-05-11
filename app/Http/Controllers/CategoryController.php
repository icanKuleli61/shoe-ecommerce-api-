<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Http\Requests\StoreCategoryRequest;
class CategoryController extends Controller
{
    protected CategoryService $service;

    public function __construct(
        CategoryService $service
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
        StoreCategoryRequest $request
    ) {

        $category =
            $this->service->store(
                $request->validated()
            );



        return response()->json([

            'success' => true,

            'data' => $category
        ]);
    }


    public function destroy($id)
    {

        $this->service->delete($id);



        return response()->json([

            'success' => true,

            'message' =>
                'Kategori silindi'
        ]);
    }
}