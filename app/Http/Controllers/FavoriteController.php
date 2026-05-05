<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FavoriteService;

class FavoriteController extends Controller
{
    protected FavoriteService $service;

    public function __construct(FavoriteService $service){
        $this->service = $service;
    }

    public function toggle($productId)
    {
        $result = $this->service->toggle($productId);

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function index()
    {
        $products = $this->service->getUserFavorites();

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products)
        ]);
    }
}
