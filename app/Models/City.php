<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';
    protected $fillable = ['plate_code', 'name'];

    public function districts()
    {
      return $this->hasMany(\App\Models\District::class);
    }
}