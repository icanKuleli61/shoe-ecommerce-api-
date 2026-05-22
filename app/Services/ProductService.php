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

        } catch (\Throwable $e) {

            dd($e->getMessage());
        }
    }

    public function index()
    {
        return Product::with([

            'favorites',

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

            'favorites',

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

                'favorites',

                'variants.images',

                'variants.sizes',
            ])

            ->withAvg(
                'reviews',
                'rating'
            )

            ->withCount(
                'reviews'
            );
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

            $categories = explode(
                ',',
                $filters['category_id']
            );

            $query->whereHas(
                'category',
                function ($q) use ($categories) {

                    $q->whereIn(
                        'name',
                        $categories
                    );
                }
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

                'variants.sizes',

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


    public function adminIndex()
    {
        return Product::withTrashed()

            ->with([

                'category',

                'brand',

                'variants.images',

                'variants.sizes'

            ])

            ->latest()

            ->paginate(10);
    }

    public function adminFilter(array $filters)
    {
        $query = Product::withTrashed()

            ->with([

                'category',

                'brand',

                'variants.images',

                'variants.sizes'
            ]);

        if (!empty($filters['search'])) {

            $search = $filters['search'];

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
                        );
                }
            );
        }

        if (!empty($filters['category_id'])) {

            $query->where(
                'category_id',
                $filters['category_id']
            );
        }



        if (!empty($filters['status'])) {


            if ($filters['status'] === 'active') {

                $query->whereNull(
                    'deleted_at'
                );
            }


            if ($filters['status'] === 'passive') {

                $query->onlyTrashed();
            }
        }



        return $query
            ->latest()
            ->paginate(10);
    }

    public function adminUpdate(
        $id,
        array $data
    ) {

        $product = Product::with([

            'variants.images',

            'variants.sizes'
        ])

            ->find($id);



        if (!$product) {

            throw new BaseException(
                ErrorCode::NOT_FOUND
            );
        }



        try {

            $this->updateProduct(
                $product,
                $data
            );



            $this->handleDeletes(
                $data
            );



            $this->syncVariants(
                $product,
                $data['variants']
            );



            return $product->fresh([

                'variants.images',

                'variants.sizes',

                'variants.color',

                'category',

                'brand'
            ]);

        } catch (\Throwable $e) {

            dd($e->getMessage());
        }
    }
    private function updateProduct(
        Product $product,
        array $data
    ): void {

        $product->update([

            'name' =>

                $data['name'],

            'slug' =>

                Str::slug(
                    $data['name']
                ) . '-' . $product->id,

            'description' =>

                $data['description']
                ?? null,

            'category_id' =>

                $data['category_id'],

            'brand_id' =>

                $data['brand_id'],

            'gender' =>

                $data['gender'],
        ]);
    }

    private function syncVariants(
        Product $product,
        array $variants
    ): void {

        foreach (
            $variants
            as $variantData
        ) {

            $variant =
                $this->upsertVariant(
                    $product,
                    $variantData
                );

            $this->syncSizes(

                $variant,

                $variantData['sizes']
                ?? []
            );

            $this->syncImages(

                $variant,

                $variantData['images']
                ?? []
            );
        }
    }
    private function upsertVariant(
        Product $product,
        array $variantData
    ): ProductVariant {

        if (
            !empty($variantData['id'])
        ) {

            $variant =
                ProductVariant::where(

                    'id',

                    $variantData['id']

                )

                    ->where(

                        'product_id',

                        $product->id
                    )

                    ->first();



            if (!$variant) {

                throw new BaseException(
                    ErrorCode::NOT_FOUND
                );
            }



            $variant->update([

                'name' =>

                    $variantData['name'],

                'color_id' =>

                    $variantData['color_id'],
            ]);



            return $variant;
        }



        return ProductVariant::create([

            'product_id' =>

                $product->id,

            'name' =>

                $variantData['name'],

            'color_id' =>

                $variantData['color_id'],
        ]);
    }

    private function syncSizes(
        ProductVariant $variant,
        array $sizes
    ): void {

        foreach (
            $sizes
            as $sizeData
        ) {

            $this->upsertSize(
                $variant,
                $sizeData
            );
        }
    }
    private function upsertSize(
        ProductVariant $variant,
        array $sizeData
    ): void {

        if (
            !empty($sizeData['id'])
        ) {

            $size =
                VariantSize::where(

                    'id',

                    $sizeData['id']

                )

                    ->where(

                        'variant_id',

                        $variant->id
                    )

                    ->first();



            if (!$size) {

                throw new BaseException(
                    ErrorCode::NOT_FOUND
                );
            }



            $size->update([

                'size' =>

                    $sizeData['size'],

                'stock' =>

                    $sizeData['stock'],

                'price' =>

                    $sizeData['price'],
            ]);



            return;
        }



        VariantSize::create([

            'variant_id' =>

                $variant->id,

            'size' =>

                $sizeData['size'],

            'stock' =>

                $sizeData['stock'],

            'price' =>

                $sizeData['price'],
        ]);
    }

    private function syncImages(
        ProductVariant $variant,
        array $images = []
    ): void {

        foreach (
            $images
            as $imageData
        ) {

            $this->upsertImage(
                $variant,
                $imageData
            );
        }
    }
    private function upsertImage(
        ProductVariant $variant,
        array $imageData
    ): void {

        if (
            !empty($imageData['id'])
        ) {

            $image =
                ProductImage::where(

                    'id',

                    $imageData['id']

                )

                    ->where(

                        'variant_id',

                        $variant->id
                    )

                    ->first();



            if (!$image) {

                throw new BaseException(
                    ErrorCode::NOT_FOUND
                );
            }



            $image->update([

                'is_main' =>

                    $imageData['is_main']
                    ?? false,

                'order' =>

                    $imageData['order']
                    ?? 0,
            ]);



            return;
        }



        if (
            empty($imageData['file'])
        ) {

            return;
        }



        $path = $imageData['file']

            ->store(
                'products',
                'public'
            );



        ProductImage::create([

            'variant_id' =>

                $variant->id,

            'image_path' =>

                $path,

            'is_main' =>

                $imageData['is_main']
                ?? false,

            'order' =>

                $imageData['order']
                ?? 0,
        ]);
    }

    private function handleDeletes(
        array $data
    ): void {

        if (
            !empty($data['deleted_images'])
        ) {

            $images = ProductImage::whereIn(

                'id',

                $data['deleted_images']

            )->get();



            foreach (
                $images
                as $image
            ) {

                $imagePath = storage_path(

                    'app/public/' .
                    $image->image_path
                );



                if (
                    file_exists($imagePath)
                ) {

                    unlink($imagePath);
                }



                $image->delete();
            }
        }



        if (
            !empty($data['deleted_sizes'])
        ) {

            VariantSize::whereIn(

                'id',

                $data['deleted_sizes']

            )->delete();
        }



        if (
            !empty($data['deleted_variants'])
        ) {

            foreach (

                $data['deleted_variants']

                as $variantId
            ) {

                $variant = ProductVariant::with([

                    'images',
                    'sizes'

                ])->find($variantId);



                if (!$variant) {

                    continue;
                }



                foreach (
                    $variant->images
                    as $image
                ) {

                    /** @var ProductImage $image */

                    $imagePath = storage_path(

                        'app/public/' .
                        $image->image_path
                    );



                    if (
                        file_exists($imagePath)
                    ) {

                        unlink($imagePath);
                    }



                    $image->delete();
                }



                $variant->sizes()->delete();

                $variant->delete();
            }
        }
    }


    public function adminShow($id)
    {
        $product = Product::with([

            'category',

            'brand',

            'variants' => function ($query) {

                $query->orderBy('id');
            },

            'variants.color',

            'variants.images' => function ($query) {

                $query->orderBy('order');
            },

            'variants.sizes' => function ($query) {

                $query->orderBy('size');
            }
        ])

            ->find($id);



        if (!$product) {

            throw new BaseException(
                ErrorCode::NOT_FOUND
            );
        }



        return $product;
    }
}