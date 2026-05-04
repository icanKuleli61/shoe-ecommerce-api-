<?php

namespace App\Services;

use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;

class ProductImageService
{
    public function store(array $data)
    {
        $path = $data['image']->store('products', 'public');

        return ProductImage::create([
            'variant_id' => $data['variant_id'],
            'image_path' => $path,
            'is_main' => $data['is_main'] ?? false,
            'order' => $data['order'] ?? 0,
        ]);
    }
}