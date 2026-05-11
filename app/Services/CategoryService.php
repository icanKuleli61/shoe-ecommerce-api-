<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function index()
    {
        return Category::where(
            'is_active',
            true
        )->get();
    }


    public function store(array $data)
    {

        return Category::create([

            'name' => $data['name'],

            'slug' => \Str::slug(
                $data['name']
            ),

            'is_active' => true
        ]);
    }

    public function delete($id)
    {

        $category =
            Category::findOrFail($id);



        $category->delete();



        return true;
    }
}