<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\District;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DistrictsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
{
    $data = json_decode(
        file_get_contents(database_path('data/clean_districts.json')),
        true
    );

    foreach ($data as $cityName => $districts) {

        $city = City::where('name', $cityName)->first();

        if (!$city) {
            echo "Şehir bulunamadı: $cityName\n";
            continue;
        }

        foreach ($districts as $districtName) {
            District::firstOrCreate([
                'city_id' => $city->id,
                'name' => $districtName
            ]);
        }
    }
}
}
