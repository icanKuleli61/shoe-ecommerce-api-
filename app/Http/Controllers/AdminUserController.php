<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdminUserService;

class AdminUserController extends Controller
{
    protected AdminUserService $service;



    public function __construct(
        AdminUserService $service
    ) {
        $this->service = $service;
    }



    public function index(Request $request)
    {
        $users = $this->service
            ->index($request);

        return response()->json([

            'success' => true,

            'data' => $users
        ]);
    }



    public function toggleStatus($id)
    {
        $user = $this->service
            ->toggleStatus($id);

        return response()->json([

            'success' => true,

            'message' =>
                'Kullanıcı durumu güncellendi.',

            'data' => $user
        ]);
    }
}