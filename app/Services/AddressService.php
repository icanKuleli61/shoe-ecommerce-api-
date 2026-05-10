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

        if (!$hasAnyAddress) {
            $data['is_default'] = true;
        }

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
        return Address::with(['city', 'district', 'neighborhood'])
            ->where('user_id', $user_id)
            ->orderByDesc('is_default')
            ->get();
    }

    public function update($user_id, $address_id, $data)
    {
        $address = Address::where('id', $address_id)
            ->where('user_id', $user_id)
            ->first();

        if (!$address) {
            throw new BaseException(ErrorCode::ADDRESS_NOT_FOUND);
        }

        $address->fill($data);

        if (!$address->isDirty()) {
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
        $address = Address::where('id', $address_id)
            ->where('user_id', $user_id)
            ->first();



        if (!$address) {

            throw new BaseException(
                ErrorCode::ADDRESS_NOT_FOUND
            );
        }



        $totalAddress = Address::where(
            'user_id',
            $user_id
        )->count();



        if ($totalAddress <= 1) {

            throw new BaseException(
                ErrorCode::LAST_ADDRESS_CANNOT_DELETE
            );
        }



        $wasDefault = $address->is_default;



        $address->delete();



        if ($wasDefault) {

            $newDefault = Address::where(
                'user_id',
                $user_id
            )->first();



            if ($newDefault) {

                $newDefault->update([
                    'is_default' => true
                ]);
            }
        }



        return true;
    }

    public function makeDefault(
        $user_id,
        $address_id
    ) {
        $address = Address::where(
            'id',
            $address_id
        )
            ->where(
                'user_id',
                $user_id
            )
            ->first();

        if (!$address) {

            throw new BaseException(
                ErrorCode::ADDRESS_NOT_FOUND
            );
        }

        Address::where(
            'user_id',
            $user_id
        )->update([
                    'is_default' => false
                ]);

        $address->update([
            'is_default' => true
        ]);

        return $address;
    }
}