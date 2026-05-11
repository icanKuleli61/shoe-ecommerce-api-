<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model
{
    use SoftDeletes;
    protected $table = 'products';


    protected $fillable = [
        'name',
        'description',
        'category_id',
        'brand_id',
        'gender',
        'active',
        'view_count',
        'discount_rate',
        'slug'
    ];


    public function variants()
    {
        return $this->hasMany(\App\Models\ProductVariant::class);
    }

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(\App\Models\Brand::class);
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class);
    }

    public function favorites()
    {
        return $this->hasMany(\App\Models\Favorite::class);
    }
    public function images()
    {
        return $this->hasManyThrough(

            \App\Models\ProductImage::class,

            \App\Models\ProductVariant::class,

            'product_id',

            'variant_id',

            'id',

            'id'
        );
    }

    public function sizes()
    {
        return $this->hasManyThrough(

            \App\Models\VariantSize::class,

            \App\Models\ProductVariant::class,

            'product_id',

            'variant_id',

            'id',

            'id'
        );
    }
}
