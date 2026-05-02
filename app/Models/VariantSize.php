<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantSize extends Model
{
    protected $fillable = [
        'variant_id',
        'size',
        'stock',
        'price'
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

}
