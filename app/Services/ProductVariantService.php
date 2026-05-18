<?php

namespace App\Services;

use App\Models\ProductVariant;
use App\Exceptions\BaseException;
use App\Enums\ErrorCode;

class ProductVariantService
{
    public function store(array $data)
    {
        $exists = ProductVariant::where('product_id', $data['product_id'])
            ->where('color_id', $data['color_id'])
            ->exists();

        if ($exists) {
            throw new BaseException(ErrorCode::ALREADY_EXISTS);
        }

        return ProductVariant::create($data);
    }

    public function delete($id)
    {
        $variant = ProductVariant::find($id);

        if (!$variant) {
            throw new BaseException(ErrorCode::NOT_FOUND);
        }

        $variant->delete();

        return true;
    }

    public function update(
        $id,
        array $data
    ) {

        $variant =
            ProductVariant::find($id);


        if (!$variant) {

            throw new BaseException(
                ErrorCode::VARIANT_NOT_FOUND
            );
        }

        $exists = ProductVariant::where(

            'product_id',
            $variant->product_id
        )

            ->where(
                'color_id',
                $data['color_id']
            )

            ->where(
                'id',
                '!=',
                $variant->id
            )

            ->exists();



        if ($exists) {

            throw new BaseException(
                ErrorCode::ALREADY_EXISTS
            );
        }

        $hasNoChanges =

            $variant->name === $data['name']

            &&

            $variant->color_id ==
            $data['color_id'];



        if ($hasNoChanges) {

            throw new BaseException(
                ErrorCode::NO_CHANGES_DETECTED
            );
        }

        $variant->update([

            'name' =>
                $data['name'],

            'color_id' =>
                $data['color_id'],
        ]);



        return $variant->fresh();
    }
}