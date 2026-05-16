<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{

    protected $table = 'order_items';

    protected $fillable = [

        'order_id',
        'variant_id',
        'size_id',
        'product_name',
        'variant_name',
        'size_value',
        'quantity',
        'price'

    ];

    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class);
    }

    public function variant()
    {
        return $this->belongsTo(\App\Models\ProductVariant::class, 'variant_id');
    }

    public function size()
    {
        return $this->belongsTo(\App\Models\VariantSize::class, 'size_id');
    }

    public function product()
    {
        return $this->belongsTo(
            \App\Models\Product::class
        );
    }


}
