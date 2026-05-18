<?php

namespace App\Http\Controllers;

use App\Services\FavoriteService;

use App\Http\Resources\FavoriteResource;

class FavoriteController extends Controller
{
    public function __construct(

        protected FavoriteService $service

    ) {
    }

    public function toggle($productId)
    {
        $result =

            $this->service
                ->toggle($productId);

        return response()->json([

            'success' => true,

            'data' =>
                $result
        ]);
    }

    public function index()
    {
        $favorites =

            $this->service
                ->index();

        return response()->json([

            'success' => true,

            'data' =>

                FavoriteResource::collection(
                    $favorites
                ),

            'pagination' => [

                'current_page' =>
                    $favorites->currentPage(),

                'last_page' =>
                    $favorites->lastPage(),

                'per_page' =>
                    $favorites->perPage(),

                'total' =>
                    $favorites->total(),

                'has_more_pages' =>
                    $favorites->hasMorePages()
            ]
        ]);
    }
}