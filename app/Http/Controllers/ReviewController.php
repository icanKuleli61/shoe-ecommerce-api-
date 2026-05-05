<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReviewResource;
use Illuminate\Http\Request;
use App\Services\ReviewService;

class ReviewController extends Controller
{
    protected ReviewService $service;

    protected function __construct(ReviewService $service){
        $this->service = $service;
    }

    public function store(Request $request)
    {
        $review = $this->service->store(
            $request->all()
        );

        return response()->json([
            'success' => true,
            'data' => new ReviewResource($request)
        ]);
    }

    public function index($productId)
    {
        $reviews = $this->service->getByProduct($productId);

        return response()->json([
            'success' => true,
            'data' => ReviewResource::collection($reviews)
        ]);
    }
}
