<?php

namespace App\Services;

use App\Models\Color;

class ColorService
{
    public function index()
    {
        return Color::all();
    }

    public function store(array $data)
    {

        return Color::create([

            'name' => $data['name']
        ]);
    }

    public function delete($id)
    {

        $color =
            Color::findOrFail($id);



        $color->delete();



        return true;
    }
}