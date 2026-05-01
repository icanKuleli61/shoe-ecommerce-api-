<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Neighborhood extends Model
{
    protected $table = 'neighborhoods';

    protected $fillable = ['district_id','name'];

    public function district(){
        return $this->belongsTo(\App\Models\District::class);
    }




}
