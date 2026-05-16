<?php

namespace App\Http\Controllers;

use App\Services\BannerService;
use Illuminate\Http\Request;

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
        $banners = $this->service->index();

        return response()->json([

            'success' => true,

            'data' => $banners
        ]);
    }



    public function store(Request $request)
    {
        $banner = $this->service->store(
            $request
        );

        return response()->json([

            'success' => true,

            'data' => $banner
        ]);
    }



    public function destroy($id)
    {
        $this->service->destroy($id);

        return response()->json([

            'success' => true,

            'message' =>
                'Banner silindi'
        ]);
    }
}