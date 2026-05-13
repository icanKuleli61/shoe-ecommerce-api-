<?php

namespace App\Services;

use App\Enums\ErrorCode;
use App\Exceptions\BaseException;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantSize;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    public function store(array $data)
    {
        try {

            return DB::transaction(function () use ($data) {

                $product = Product::create([

                    'name' => $data['name'],

                    'description' => $data['description'] ?? null,

                    'category_id' => $data['category_id'],

                    'brand_id' => $data['brand_id'],

                    'gender' => $data['gender'],

                    'slug' => Str::slug($data['name']) . '-' . uniqid(),
                ]);

                foreach ($data['variants'] as $variant) {

                    $productVariant = ProductVariant::create([

                        'product_id' => $product->id,
                        'name' => $variant['name'],

                        'color_id' => $variant['color_id'],
                    ]);

                    $seenSizes = [];

                    foreach ($variant['sizes'] as $size) {

                        if (
                            in_array(
                                $size['size'],
                                $seenSizes
                            )
                        ) {

                            throw new \Exception(
                                'Aynı varyantta aynı numara tekrar edemez'
                            );
                        }

                        $seenSizes[] = $size['size'];

                        VariantSize::create([

                            'variant_id' =>
                                $productVariant->id,

                            'size' =>
                                $size['size'],

                            'stock' =>
                                $size['stock'],

                            'price' =>
                                $size['price'],
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | VARYANT GÖRSELLERİ
                    |--------------------------------------------------------------------------
                    */

                    if (isset($variant['images'])) {

                        foreach (
                            $variant['images']
                            as $index => $image
                        ) {

                            $path = $image->store(
                                'products',
                                'public'
                            );

                            ProductImage::create([

                                'variant_id' =>
                                    $productVariant->id,

                                'image_path' =>
                                    $path,

                                'is_main' =>
                                    $index === 0,

                                'order' =>
                                    $index,
                            ]);
                        }
                    }



                }

                return $product->load(
                    'variants.sizes',
                    'variants.color',
                    'category',
                    'brand'
                );
            });

        } catch (\Exception $e) {

            throw new \Exception(
                'Ürün oluşturulurken hata oluştu: '
                . $e->getMessage()
            );
        }
    }

    public function index()
    {
        return Product::with([
            'variants.sizes',
            'variants.images'
        ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->paginate(12);
    }

    public function show($slug)
    {
        return Product::with([

            'variants.images',
            'variants.color',
            'variants.sizes'

        ])

            ->withAvg('reviews', 'rating')

            ->withCount('reviews')

            ->where('slug', $slug)

            ->firstOrFail();
    }

    public function update($id, array $data)
    {
        $product = Product::find($id);

        if (!$product) {
            throw new BaseException(
                ErrorCode::NOT_FOUND
            );
        }

        $product->fill($data);

        if (!$product->isDirty()) {
            throw new BaseException(
                ErrorCode::NO_CHANGES_DETECTED
            );
        }

        $product->save();

        return $product;
    }

    public function delete($id)
    {
        $product = Product::find($id);

        if (!$product) {
            throw new BaseException(
                ErrorCode::NOT_FOUND
            );
        }

        $product->delete();

        return true;
    }

    public function restore($id)
    {
        $product = Product::withTrashed()
            ->find($id);

        if (!$product) {
            throw new BaseException(
                ErrorCode::NOT_FOUND
            );
        }

        if (!$product->trashed()) {
            throw new BaseException(
                ErrorCode::ALREADY_ACTIVE
            );
        }

        $product->restore();

        return $product;
    }

    public function filter(array $filters)
    {
        $query = $this->baseQuery();

        $this->applySearch($query, $filters);

        $this->applyCategory($query, $filters);

        $this->applyBrand($query, $filters);

        $this->applyGender($query, $filters);

        $this->applyColor($query, $filters);

        $this->applySize($query, $filters);

        $this->applyPrice($query, $filters);

        $this->applySorting($query, $filters);
        if (!empty($filters['size'])) {
            $sizes = explode(
                ',',
                $filters['size']
            );

            $query->whereHas(

                'sizes',

                function ($q) use ($sizes) {

                    $q->whereIn(
                        'size',
                        $sizes
                    );
                }
            );
        }

        return $query->paginate(12);

    }

    private function baseQuery()
    {
        return Product::query()
            ->with([
                'images',
                'variants.sizes',
            ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');
    }

    private function applySearch(
        $query,
        $filters
    ) {

        if (
            !empty($filters['search'])
        ) {

            $search =
                $filters['search'];

            $query->where(

                function ($q) use ($search) {

                    $q->where(
                        'name',
                        'ILIKE',
                        "%{$search}%"
                    )

                        ->orWhereHas(

                            'brand',

                            function ($brandQuery) use ($search) {

                                $brandQuery->where(
                                    'name',
                                    'ILIKE',
                                    "%{$search}%"
                                );
                            }
                        )

                        ->orWhereHas(

                            'category',

                            function ($categoryQuery) use ($search) {

                                $categoryQuery->where(
                                    'name',
                                    'ILIKE',
                                    "%{$search}%"
                                );
                            }
                        );
                }
            );
        }
    }

    private function applyCategory($query, $filters)
    {
        if (!empty($filters['category_id'])) {

            $query->where(
                'category_id',
                $filters['category_id']
            );
        }
    }

    private function applyBrand($query, $filters)
    {
        if (!empty($filters['brand_id'])) {

            $query->where(
                'brand_id',
                $filters['brand_id']
            );
        }
    }

    private function applyGender(
        $query,
        $filters
    ) {

        if (!empty($filters['gender'])) {

            $genders = explode(
                ',',
                $filters['gender']
            );

            $query->whereIn(
                'gender',
                $genders
            );
        }
    }

    private function applyColor($query, $filters)
    {
        if (!empty($filters['color_id'])) {

            $query->whereHas(
                'variants',
                function ($q) use ($filters) {

                    $q->where(
                        'color_id',
                        $filters['color_id']
                    );
                }
            );
        }
    }

    private function applySize(
        $query,
        $filters
    ) {
        if (!empty($filters['size'])) {
            $sizes = explode(
                ',',
                $filters['size']
            );
            $query->whereHas(
                'sizes',
                function ($q) use ($sizes) {
                    $q
                        ->whereIn(
                            'size',
                            $sizes
                        )
                        ->where(
                            'stock',
                            '>',
                            0
                        );
                }
            );
        }
    }

    private function applyPrice(
        $query,
        $filters
    ) {

        if (
            !empty($filters['min_price'])
        ) {

            $query->whereHas(

                'variants.sizes',

                function ($q) use ($filters) {

                    $q->where(
                        'price',
                        '>=',
                        $filters['min_price']
                    );
                }
            );
        }

        if (
            !empty($filters['max_price'])
        ) {

            $query->whereHas(

                'variants.sizes',

                function ($q) use ($filters) {

                    $q->where(
                        'price',
                        '<=',
                        $filters['max_price']
                    );
                }
            );
        }
    }

    private function applySorting(
        $query,
        $filters
    ) {

        if (
            empty($filters['sort'])
        ) {
            return;
        }

        switch ($filters['sort']) {

            case 'price_asc':

                $query

                    ->withMin(
                        'sizes',
                        'price'
                    )

                    ->orderBy(
                        'sizes_min_price',
                        'asc'
                    );

                break;



            case 'price_desc':

                $query

                    ->withMin(
                        'sizes',
                        'price'
                    )

                    ->orderBy(
                        'sizes_min_price',
                        'desc'
                    );

                break;



            case 'newest':

                $query->latest();

                break;



            case 'most_reviewed':

                $query->orderByDesc(
                    'reviews_count'
                );

                break;



            case 'top_rated':

                $query->orderByDesc(
                    'reviews_avg_rating'
                );

                break;
        }
    }
}