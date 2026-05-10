<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FavoriteService;
use App\Http\Resources\ProductListResource;

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
            'data' => ProductListResource::collection($products)
        ]);
    }
}
