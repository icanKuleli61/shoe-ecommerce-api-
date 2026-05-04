<?php

namespace App\Services;

use App\Models\Order;

class PaymentService
{
    public function pay(Order $order)
    {
        return $this->simulate($order);
    }

    private function simulate(Order $order)
    {
        return [
            'success' => true,
            'transaction_id' => uniqid()
        ];
    }
}