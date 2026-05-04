<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'cart_items';

    protected $fillable = [
        'user_id',
        'variant_id',
        'size_id',
        'quantity',
        'price'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function size()
    {
        return $this->belongsTo(VariantSize::class, 'size_id');
    }
}
