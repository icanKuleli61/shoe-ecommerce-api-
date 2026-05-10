<?php

namespace App\Http\Controllers;

use App\Services\UserService;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\VerifyPasswordRequest;

class UserController extends Controller
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function profile()
    {
        $profile = $this->service->getProfile();

        return response()->json([
            'success' => true,
            'data' => $profile
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $this->service->updateProfile(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function changePassword(
        ChangePasswordRequest $request
    ) {
        $this->service->changePassword(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Şifre başarıyla güncellendi.'
        ]);
    }

    public function verifyPassword(
        VerifyPasswordRequest $request
    ) {
        $this->service->verifyPassword(
            $request->validated()
        );

        return response()->json([

            'success' => true,
            'message' => 'Şifre doğru.'
        ]);
    }
}