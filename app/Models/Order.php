<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const STATUS_PENDING = 'pending';

    const STATUS_APPROVED = 'approved';

    const STATUS_SUPPLYING = 'supplying';

    const STATUS_PACKAGING = 'packaging';

    const STATUS_SHIPPED = 'shipped';

    const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';

    const STATUS_DELIVERED = 'delivered';

    const STATUS_COMPLETED = 'completed';

    const STATUS_CANCELLED = 'cancelled';



    const PAYMENT_METHOD_CARD = 'card';

    const PAYMENT_METHOD_WALLET = 'wallet';



    const PAYMENT_STATUS_PENDING = 'pending';

    const PAYMENT_STATUS_PAID = 'paid';

    const PAYMENT_STATUS_FAILED = 'failed';



    public static array $statusFlow = [

        'pending' => [
            'approved',
            'cancelled'
        ],

        'approved' => [
            'supplying',
            'cancelled'
        ],

        'supplying' => [
            'packaging',
            'cancelled'
        ],

        'packaging' => [
            'shipped',
            'cancelled'
        ],

        'shipped' => [
            'out_for_delivery'
        ],

        'out_for_delivery' => [
            'delivered'
        ],

        'delivered' => [
            'completed'
        ],

        'completed' => [],

        'cancelled' => [],
    ];



    protected $table = 'orders';



    protected $fillable = [

        'user_id',

        'address_id',

        'subtotal',

        'shipping_price',

        'total_price',

        'payment_method',

        'payment_status',

        'status',

        'full_name',

        'phone',

        'city',

        'district',

        'neighborhood',

        'address_text'
    ];



    protected $casts = [

        'subtotal' => 'float',

        'shipping_price' => 'float',

        'total_price' => 'float',
    ];



    public function isStatus($status)
    {
        return $this->status === $status;
    }



    public function user()
    {
        return $this->belongsTo(
            \App\Models\User::class
        );
    }



    public function address()
    {
        return $this->belongsTo(
            \App\Models\Address::class
        );
    }



    public function items()
    {
        return $this->hasMany(
            \App\Models\OrderItem::class
        );
    }
}