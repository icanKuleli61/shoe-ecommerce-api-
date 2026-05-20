<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    protected AuthService $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    public function login(LoginRequest $request)
    {
        $data = $this->service->login(
            $request->validated()
        );

        return response()->json([

            'success' => true,

            'message' => 'Giriş başarılı.',

            'token' => $data['token'],

            'user' => $data['user']
        ]);
    }

    public function register(RegisterRequest $request)
    {
        return response()->json([

            'validated' => $request->validated()

        ]);
    }



}
