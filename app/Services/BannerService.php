<?php

namespace App\Services;

use App\Models\Banner;

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


    public function adminIndex()
    {
        return Banner::orderBy(
            'sort_order'
        )->get();
    }


    public function store(
        array $data
    ) {

        $data['image'] =

            $this->uploadImage(
                $data['image']
            );



        return Banner::create([

            'title' =>

                $data['title']
                ?? null,

            'image' =>

                $data['image'],

            'sort_order' =>

                (Banner::max('sort_order') ?? 0) + 1,

            'is_active' =>

                $data['is_active']
                ?? true,
        ]);
    }


    public function update(
        array $data,
        $id
    ) {

        $banner = Banner::find($id);

        if (!$banner) {

            throw new BaseException(
                ErrorCode::NOT_FOUND
            );
        }


        if (isset($data['sort_order'])) {

            $oldOrder =
                $banner->sort_order;

            $newOrder =
                (int) $data['sort_order'];
            $maxOrder = Banner::count();

            if ($newOrder < 1) {

                $newOrder = 1;
            }

            if ($newOrder > $maxOrder) {

                $newOrder = $maxOrder;
            }


            if ($newOrder > $oldOrder) {

                Banner::where(

                    'sort_order',
                    '>',
                    $oldOrder

                )
                    ->where(

                        'sort_order',
                        '<=',
                        $newOrder

                    )
                    ->decrement(
                        'sort_order'
                    );

            } elseif ($newOrder < $oldOrder) {

                Banner::where(

                    'sort_order',
                    '>=',
                    $newOrder

                )
                    ->where(

                        'sort_order',
                        '<',
                        $oldOrder

                    )
                    ->increment(
                        'sort_order'
                    );
            }
        }

        if (!empty($data['image'])) {

            $this->replaceImage(
                $banner,
                $data['image']
            );
        }


        $banner->update([

            'title' =>

                $data['title']
                ?? $banner->title,

            'sort_order' =>

                $newOrder
                ?? $banner->sort_order,

            'is_active' =>

                $data['is_active']
                ?? $banner->is_active,
        ]);



        return $banner;
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

        Banner::where(

            'sort_order',
            '>',
            $banner->sort_order

        )->decrement(
                'sort_order'
            );


        $banner->delete();
    }


    private function uploadImage(
        $image
    ): string {

        $path = $image->store(
            'banners',
            'public'
        );

        dd([
            'path' => $path,
            'exists' => Storage::disk('public')->exists($path),
            'full_path' => Storage::disk('public')->path($path),
        ]);
    }


    private function replaceImage(
        Banner $banner,
        $image
    ): void {

        if (

            $banner->image

            &&

            Storage::disk('public')
                ->exists($banner->image)
        ) {

            Storage::disk('public')
                ->delete($banner->image);
        }



        $banner->image =

            $this->uploadImage(
                $image
            );



        $banner->save();
    }
}