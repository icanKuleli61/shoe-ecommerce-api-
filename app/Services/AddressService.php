<?php

namespace App\Services;

use App\Models\Address;
use App\Exceptions\BaseException;
use App\Enums\ErrorCode;

class AddressService
{
    public function store($user_id, $data)
    {
        $hasAnyAddress = Address::where('user_id', $user_id)->exists();

        // 🔥 ilk adres ise otomatik default
        if (!$hasAnyAddress) {
            $data['is_default'] = true;
        }

        // 🔥 kullanıcı özellikle default yaptıysa
        if (($data['is_default'] ?? false) === true) {
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
            ->orderByDesc('is_default') 
            ->get();
    }

    public function update($user_id, $address_id, $data)
    {
        // 🔥 adres var mı + kullanıcıya ait mi
        $address = Address::where('id', $address_id)
            ->where('user_id', $user_id)
            ->first();

        if (!$address) {
            throw new BaseException(ErrorCode::ADDRESS_NOT_FOUND);
        }

        $address->fill($data);

        if(!$address->isDirty()){
            throw new BaseException(ErrorCode::NO_CHANGES_DETECTED);
        }

        if (($data['is_default'] ?? false) === true) {
            Address::where('user_id', $user_id)
                ->update(['is_default' => false]);
        }

        $address->save();
        return $address;
    }

    public function delete($user_id, $address_id)
    {
        // 🔥 adres var mı + kullanıcıya ait mi
        $address = Address::where('id', $address_id)
            ->where('user_id', $user_id)
            ->first();

        if (!$address) {
            throw new BaseException(ErrorCode::ADDRESS_NOT_FOUND);
        }

        // 🔥 sil
        $address->delete();

        return true;
    }
}