<?php

namespace App\Services;

use App\Models\Address;
use App\Exceptions\BaseException;
use App\Enums\ErrorCode;

class AddressService
{
    public function store($user_id, $data)
    {
        // 🔥 default adres kontrolü
        if (!empty($data['is_default'])) {
            Address::where('user_id', $user_id)
                ->update(['is_default' => false]);
        }

        return Address::create([
            'user_id' => $user_id,
            ...$data
        ]);
    }

    public function index($user_id)
    {
        return Address::with(['city','district','neighborhood'])
            ->where('user_id', $user_id)
            ->get();
    }
}