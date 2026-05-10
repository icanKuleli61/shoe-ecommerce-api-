<?php

namespace App\Services;

use App\Models\User;
use App\Exceptions\BaseException;
use App\Enums\ErrorCode;
use Illuminate\Support\Facades\Hash;

class UserService
{


    public function getProfile()
    {
        $userId = $this->getUserId();

        return User::with('addresses')
            ->find($userId);
    }

    public function updateProfile(array $data)
    {
        $user = $this->getAuthUser();

        $this->updateUser($user, $data);

        return $user->fresh();
    }

    public function changePassword(array $data)
    {
        $user = $this->getAuthUser();

        $this->checkCurrentPassword(
            $data['current_password'],
            $user->password
        );

        $this->updatePassword(
            $user,
            $data['new_password']
        );
    }

    private function getUserId()
    {
        $userId = auth()->id();

        if (!$userId) {
            throw new BaseException(ErrorCode::UNAUTHORIZED);
        }

        return $userId;
    }

    private function getAuthUser()
    {
        $user = auth()->user();

        if (!$user) {
            throw new BaseException(ErrorCode::UNAUTHORIZED);
        }

        return $user;
    }

    private function updateUser(User $user, array $data)
    {
        $user->update([

            'first_name' => $data['first_name'] ?? $user->first_name,

            'last_name' => $data['last_name'] ?? $user->last_name,

            'email' => $data['email'] ?? $user->email,

            'phone' => $data['phone'] ?? $user->phone,

            'gender' => $data['gender'] ?? $user->gender,
        ]);
    }

    private function checkCurrentPassword(
        $plainPassword,
        $hashedPassword
    ) {
        $isValid = Hash::check(
            $plainPassword,
            $hashedPassword
        );

        if (!$isValid) {
            throw new BaseException(
                ErrorCode::INVALID_CREDENTIALS
            );
        }
    }

    private function updatePassword(
        User $user,
        $newPassword
    ) {
        $user->update([
            'password' => Hash::make($newPassword)
        ]);
    }

    public function verifyPassword(array $data)
    {
        $user = $this->getAuthUser();



        $isValid = Hash::check(
            $data['password'],
            $user->password
        );

        if (!$isValid) {

            throw new BaseException(
                ErrorCode::INVALID_CREDENTIALS
            );
        }
    }
}