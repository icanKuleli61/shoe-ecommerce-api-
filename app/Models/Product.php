<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
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
}
