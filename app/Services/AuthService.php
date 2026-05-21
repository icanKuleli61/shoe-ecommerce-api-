<?php


namespace App\Services;

use App\Exceptions\BaseException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Enums\ErrorCode;
use Illuminate\Support\Facades\DB;
use App\Services\AddressService;
use App\Models\Address;

class AuthService
{


    protected AddressService $addressService;


    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

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
        return DB::transaction(function () use ($data) {

            $user = $this->createUser($data);

            $this->addressService->store(
                $user->id,
                $data
            );

            return $this->generateToken($user);
        });
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