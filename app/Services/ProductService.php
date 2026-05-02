<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantSize;
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
                'slug' => \Str::slug($data['name']),
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

            return $product;
        });

    } catch (\Exception $e) {

        throw new \Exception('Ürün oluşturulurken hata oluştu: ' . $e->getMessage());
    }
}
}