<?php

namespace App\Http\Controllers;
use App\Services\LocationService;

use Illuminate\Http\Request;

class LocationController extends Controller
{

    protected LocationService $service;

    public function __construct(LocationService $service){
        $this->service = $service;
    }

    public function cities(){
        return response()->json([
        'success' => true,
        'data' => $this->service->getCities()
        ]);
    }

    public function districts($city_id){
        return response()->json([
        'success' => true,
        'data' => $this->service->getDistricts($city_id)
        ]);
    }

    public function neighborhoods($district_id){
         return response()->json([
        'success' => true,
        'data' => $this->service->getNeighborhoods($district_id)
        ]);
    }

}
