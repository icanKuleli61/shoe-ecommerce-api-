<?php

namespace App\Http\Controllers;

use App\Services\AdminDashboardService;

class AdminDashboardController extends Controller
{
    protected AdminDashboardService $service;



    public function __construct(
        AdminDashboardService $service
    ) {
        $this->service = $service;
    }



    public function index()
    {
        $data =
            $this->service->index();



        return response()->json([

            'success' => true,

            'data' => $data
        ]);
    }
}