<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    protected $table = 'orders';

    protected $fillable = [

        'user_id',
        'total_price',
        'status',
        'full_name',
        'phone',
        'city',
        'district',
        'neighborhood',
        'address_text'
    ];

    public function isStatus($status)
    {
        return $this->status === $status;
    }

    public function user(){
        return $this->belongsTo(\App\Models\User::class);
    }

    public function items(){
        return $this->hasMany(\App\Models\OrderItem::class);
    }

}
