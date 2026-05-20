<?php


namespace App\Services;

use App\Exceptions\BaseException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Enums\ErrorCode;
use Illuminate\Support\Facades\DB;

use App\Models\Address;

class AuthService
{


    public function login(array $data)
    {
        $user = $this->findUserByEmail(
            $data['email']
        );

        $this->checkPassword(
            $data['password'],
            $user->password
        );

        if (!$user->is_active) {

            throw new BaseException(
                ErrorCode::ACCOUNT_DISABLED
            );
        }
        return [

            'token' =>

                $this->generateToken($user),

            'user' => $user
        ];
    }
    public function register(array $data)
    {
        try {

            $user = $this->createUser($data);

            return [

                'step' => 'user_created',

                'user' => $user

            ];

        } catch (\Exception $e) {

            return [

                'error' => $e->getMessage(),

                'line' => $e->getLine(),

                'file' => $e->getFile()

            ];
        }
    }

    private function findUserByEmail($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new BaseException(ErrorCode::INVALID_CREDENTIALS);
        }

        return $user;
    }

    private function checkPassword($plainPassword, $hashedPassword)
    {
        $isValid = Hash::check(
            $plainPassword,
            $hashedPassword
        );

        if (!$isValid) {
            throw new BaseException(ErrorCode::INVALID_CREDENTIALS);
        }

    }

    private function generateToken(User $user)
    {

        return JWTAuth::fromUser($user);
    }

    private function createUser(array $data)
    {

        \Log::info($data);

        return User::create([

            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],

            'email' => $data['email'],

            'gender' => $data['gender'] ?? null,

            'phone' => $data['phone'] ?? null,

            'password' => Hash::make($data['password']),
        ]);
    }
}