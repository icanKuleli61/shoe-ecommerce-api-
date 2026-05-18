<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\BannerService;

use App\Http\Resources\BannerResource;

class BannerController extends Controller
{
    protected BannerService $service;

    public function __construct(
        BannerService $service
    ) {

        $this->service = $service;
    }


    public function index()
    {
        $banners =

            $this->service
                ->index();

        return response()->json([

            'success' => true,

            'data' =>

                BannerResource::collection(
                    $banners
                )
        ]);
    }

    public function store(Request $request)
    {
        $banner =

            $this->service
                ->store($request);

        return response()->json([

            'success' => true,

            'data' =>

                new BannerResource(
                    $banner
                )
        ]);
    }

    public function destroy($id)
    {
        $this->service
            ->destroy($id);

        return response()->json([

            'success' => true,

            'message' =>

                'Banner silindi'
        ]);
    }
}