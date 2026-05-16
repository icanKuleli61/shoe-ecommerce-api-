<?php

namespace App\Services;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\BaseException;
use App\Enums\ErrorCode;

class BannerService
{
    public function index()
    {
        return Banner::where(

            'is_active',
            true

        )
            ->orderBy('sort_order')
            ->get();
    }



    public function store(
        Request $request
    ) {

        $this->validateStore(
            $request
        );



        $imagePath = $this->uploadImage(
            $request
        );



        return Banner::create([

            'title' =>
                $request->title,

            'image' =>
                $imagePath,

            'sort_order' =>
                $request->sort_order ?? 0,

            'is_active' =>
                $request->is_active ?? true,
        ]);
    }



    public function destroy($id)
    {
        $banner = Banner::find($id);

        if (!$banner) {

            throw new BaseException(
                ErrorCode::NOT_FOUND
            );
        }



        if (

            $banner->image

            &&

            Storage::disk('public')
                ->exists($banner->image)
        ) {

            Storage::disk('public')
                ->delete($banner->image);
        }



        $banner->delete();
    }



    private function validateStore(
        Request $request
    ) {

        $request->validate([

            'title' => [

                'nullable',
                'string'
            ],

            'image' => [

                'required',
                'image'
            ],

            'sort_order' => [

                'nullable',
                'integer'
            ],

            'is_active' => [

                'nullable',
                'boolean'
            ]
        ]);
    }



    private function uploadImage(
        Request $request
    ) {

        return $request
            ->file('image')
            ->store(
                'banners',
                'public'
            );
    }
}