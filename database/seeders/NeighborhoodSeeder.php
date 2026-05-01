<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\District;
use App\Models\Neighborhood;

class NeighborhoodSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(
            file_get_contents(database_path('data/clean_full.json')),
            true
        );

        foreach ($data as $cityName => $districts) {

            $city = City::where('name', $cityName)->first();

            if (!$city) continue;

            foreach ($districts as $districtName => $neighborhoods) {

                $district = District::where('name', $districtName)
                    ->where('city_id', $city->id)
                    ->first();

                if (!$district) continue;

                foreach ($neighborhoods as $neighborhoodName) {

                    Neighborhood::firstOrCreate([
                        'district_id' => $district->id,
                        'name' => $neighborhoodName
                    ]);
                }
            }
        }
    }
}