<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'addresses';


    protected $fillable = [

        'user_id',
        'full_name',
        'phone',
        'city_id',
        'district_id',
        'neighborhood_id',
        'address',
        'title',
        'is_default'

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }

    public function orders()
    {
        return $this->hasMany(
            \App\Models\Order::class
        );
    }
}
