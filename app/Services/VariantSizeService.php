<?php

namespace App\Services;

use App\Models\VariantSize;
use App\Exceptions\BaseException;
use App\Enums\ErrorCode;

class VariantSizeService
{
    public function update($id, array $data)
    {
        $size = VariantSize::find($id);

        if (!$size) {
            throw new BaseException(ErrorCode::NOT_FOUND);
        }

        $size->fill($data);

        if (!$size->isDirty()) {
            throw new BaseException(ErrorCode::NO_CHANGES_DETECTED);
        }

        $size->save();

        return $size;
    }


    public function store(array $data)
    {
        $exists = VariantSize::where('variant_id', $data['variant_id'])
            ->where('size', $data['size'])
            ->exists();

        if ($exists) {
            throw new BaseException(ErrorCode::ALREADY_EXISTS);
        }

        return VariantSize::create($data);
    }

    public function delete($id)
    {
        $size = VariantSize::findOrFail($id);

        $size->delete();

        return true;
    }
}