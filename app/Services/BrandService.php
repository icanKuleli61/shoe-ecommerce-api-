<?php

namespace App\Services;

use App\Models\Brand;

class BrandService
{
    public function index()
    {
        return Brand::all();
    }

    public function store(array $data)
    {

        return Brand::create([

            'name' => $data['name']
        ]);
    }

    public function delete($id)
    {

        $brand =
            Brand::findOrFail($id);



        $brand->delete();



        return true;
    }
}