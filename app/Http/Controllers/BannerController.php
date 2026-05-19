<?php

namespace App\Http\Controllers;

use App\Services\BannerService;

use App\Http\Resources\BannerResource;

use App\Http\Requests\StoreBannerRequest;

use App\Http\Requests\UpdateBannerRequest;

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


    public function adminIndex()
    {
        $banners =

            $this->service
                ->adminIndex();

        return response()->json([

            'success' => true,

            'data' => BannerResource::collection(
                $banners
            )
        ]);
    }


    public function store(
        StoreBannerRequest $request
    ) {

        $banner =

            $this->service
                ->store(
                    $request->validated()
                );

        return response()->json([

            'success' => true,

            'message' =>

                'Banner oluşturuldu',

            'data' =>

                new BannerResource(
                    $banner
                )
        ]);
    }


    public function update(
        UpdateBannerRequest $request,
        $id
    ) {

        $banner =

            $this->service
                ->update(
                    $request->validated(),
                    $id
                );

        return response()->json([

            'success' => true,

            'message' =>

                'Banner güncellendi',

            'data' => new BannerResource(
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