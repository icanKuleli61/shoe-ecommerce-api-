<?php

namespace App\Services;

use Throwable;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;

use App\Enums\ErrorCode;
use App\Exceptions\BaseException;

class AdminDashboardService
{
    public function index()
    {
        $latestOrders = Order::latest()

            ->take(5)

            ->get()

            ->map(function ($order) {

                $order->order_no =
                    'SNK-' .
                    $order->created_at->format('Y') .
                    '-' .
                    str_pad($order->id, 5, '0', STR_PAD_LEFT);

                return $order;
            });



        $monthlyRevenue = Order::whereMonth(
            'created_at',
            now()->month
        )->sum('total_price');



        return [

            'total_products' =>
                Product::count(),

            'total_orders' =>
                Order::count(),

            'total_users' =>
                User::count(),

            'total_revenue' =>
                $monthlyRevenue,

            'latest_orders' =>
                $latestOrders
        ];
    }
}