<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\WalletTransaction;

use App\Enums\ErrorCode;
use App\Exceptions\BaseException;

class WalletService
{
    public function getWallet($userId)
    {
        return Wallet::with([

            'transactions' => function ($query) {

                $query->latest();
            }

        ])
            ->where('user_id', $userId)
            ->first();
    }



    public function createWallet($userId)
    {
        return Wallet::create([

            'user_id' => $userId,

            'balance' => 0
        ]);
    }



    public function addBalance(
        array $data
    ) {

        if ($data['amount'] <= 0) {

            throw new BaseException(
                ErrorCode::INVALID_AMOUNT
            );
        }



        $userId = auth()->id();



        $wallet =
            $this->getWallet($userId);



        if (!$wallet) {

            $wallet =
                $this->createWallet($userId);
        }



        $newBalance =
            $wallet->balance +
            $data['amount'];



        $wallet->update([

            'balance' => $newBalance
        ]);



        WalletTransaction::create([

            'wallet_id' =>
                $wallet->id,

            'type' =>
                'deposit',

            'amount' =>
                $data['amount'],

            'current_balance' =>
                $newBalance,

            'description' =>
                $data['description'],

            'reference_type' =>
                $data['reference_type']
                ?? null,

            'reference_id' =>
                $data['reference_id']
                ?? null
        ]);



        return $this->getWallet(
            $userId
        );
    }
}