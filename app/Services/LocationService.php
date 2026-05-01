<?php

namespace App\Services;

use App\Exceptions\BaseException;
use App\Enums\ErrorCode;
use App\Models\City;
use App\Models\District;
use App\Models\Neighborhood;

class LocationService{

    public function getCities(){
        return City::select('id','name')->get();
    }

    public function getDistricts($city_id)
    {
        $city = City::find($city_id);

        if(!$city){
            throw new BaseException(ErrorCode::CITY_NOT_FOUND);
        }
        return District::where('city_id', $city_id)
            ->select('id','name')
            ->get();
    }

    public function getNeighborhoods($district_id)
    {
        $district = District::find($district_id);
        if(!$district){
            throw new BaseException(ErrorCode::CITY_NOT_FOUND);
        }
        
        return Neighborhood::where('district_id', $district_id)
            ->select('id','name')
            ->get();
    }

}