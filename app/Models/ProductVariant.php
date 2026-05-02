<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
     protected $fillable = [
        'product_id',
        'color_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function sizes()
    {
        return $this->hasMany(VariantSize::class, 'variant_id');
    }
}
