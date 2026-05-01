<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $table = 'districts';

    protected $fillable = ['city_id','name'];

    public function city()
    {
    return $this->belongsTo(\App\Models\City::class);
    }

    public function neighborhoods(){
        return $this->hasMany(\App\Models\Neighborhood::class);
    }
    
}
