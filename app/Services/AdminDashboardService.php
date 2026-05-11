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
            ->get();



        $monthlyRevenue = Order::whereMonth(
            'created_at',
            now()->month
        )
            ->sum('total_price');



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