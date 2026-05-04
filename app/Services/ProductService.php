<?php

namespace App\Services;

use App\Enums\ErrorCode;
use App\Exceptions\BaseException;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantSize;
use Error;
use Illuminate\Support\Facades\DB;

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
                    'slug' => \Str::slug($data['name']) . '-' . uniqid(),
                ]);

                foreach ($data['variants'] as $variant) {

                    $productVariant = ProductVariant::create([
                        'product_id' => $product->id,
                        'color_id' => $variant['color_id'],
                    ]);

                    $seenSizes = [];

                    foreach ($variant['sizes'] as $size) {

                        if (in_array($size['size'], $seenSizes)) {
                            throw new \Exception('Aynı varyantta aynı numara tekrar edemez');
                        }

                        $seenSizes[] = $size['size'];

                        VariantSize::create([
                            'variant_id' => $productVariant->id,
                            'size' => $size['size'],
                            'stock' => $size['stock'],
                            'price' => $size['price'],
                        ]);
                    }
                }

                return  $product->load(
                    'variants.sizes',
                    'variants.color',
                    'category',
                    'brand'
                );
            });

        } catch (\Exception $e) {

            throw new \Exception('Ürün oluşturulurken hata oluştu: ' . $e->getMessage());
        }

        
    }

    public function index()
    {
        return Product::with([
            'variants.sizes',
            'images'
        ])->get();
    }

    public function show($slug)
    {
        return Product::with([
            'images',
            'variants.color',
            'variants.sizes'
        ])
        ->where('slug', $slug)
        ->firstOrFail();
    }

    public function update($id, array $data){

        $product = Product::find($id);

        if($product){
            throw new BaseException(ErrorCode::NOT_FOUND);
        }
        $product->fill($data);

        if(!$product->isDirty()){
            throw new BaseException(ErrorCode::NO_CHANGES_DETECTED);
        }
        $product->save();

        return $product;
    }

    public function delete($id){
        $product = Product::find($id);

        if (!$product) {
        throw new BaseException(ErrorCode::NOT_FOUND);
        }
          
        $product->delete(); // soft delete

        return true;
    }

    public function restore($id)
    {
        $product = Product::withTrashed()->find($id);

        if (!$product) {
            throw new BaseException(ErrorCode::NOT_FOUND);
        }

        if (!$product->trashed()) {
            throw new BaseException(ErrorCode::ALREADY_ACTIVE);
        }

        $product->restore();

        return $product;
    }

    

}