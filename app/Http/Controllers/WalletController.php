<?php

namespace App\Http\Controllers;

use App\Services\WalletService;
use App\Http\Requests\AddBalanceRequest;

class WalletController extends Controller
{
    protected WalletService $service;



    public function __construct(
        WalletService $service
    ) {
        $this->service = $service;
    }



    public function index()
    {
        $userId = auth()->id();



        $wallet =
            $this->service
                ->getWallet($userId);



        if (!$wallet) {

            $wallet =
                $this->service
                    ->createWallet($userId);
        }



        return response()->json([

            'success' => true,

            'data' => $wallet
        ]);
    }



    public function addBalance(
        AddBalanceRequest $request
    ) {

        $wallet =
            $this->service
                ->addBalance(
                    $request->validated()
                );



        return response()->json([

            'success' => true,

            'message' =>
                'Bakiye başarıyla yüklendi.',

            'data' => $wallet
        ]);
    }
}