<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Review extends Model
{
    protected $table = 'reviews';

    protected $fillable = [

    'user_id',
    'product_id',
    'rating',
    'comment'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }
}
