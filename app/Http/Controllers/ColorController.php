<?php

namespace App\Http\Controllers;

use App\Services\ColorService;
use App\Http\Requests\StoreColorRequest;

class ColorController extends Controller
{
    protected ColorService $service;

    public function __construct(
        ColorService $service
    ) {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json([

            'success' => true,

            'data' =>
                $this->service->index()
        ]);
    }

    public function store(
        StoreColorRequest $request
    ) {

        $color =
            $this->service->store(
                $request->validated()
            );



        return response()->json([

            'success' => true,

            'data' => $color
        ]);
    }


    public function destroy($id)
    {

        $this->service->delete($id);



        return response()->json([

            'success' => true,

            'message' =>
                'Renk silindi'
        ]);
    }
}