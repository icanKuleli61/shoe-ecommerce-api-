<?php

namespace App\Enums;

enum ErrorCode: string
{
    // AUTH
    case UNAUTHORIZED = 'UNAUTHORIZED';
    case INVALID_CREDENTIALS = 'INVALID_CREDENTIALS';
    case FORBIDDEN = 'FORBIDDEN';

    // VALIDATION
    case VALIDATION_ERROR = 'VALIDATION_ERROR';
    case INVALID_ROUTE_PARAM = 'INVALID_ROUTE_PARAM';
    case NO_CHANGES_DETECTED = 'NO_CHANGES_DETECTED';

    // LOCATION
    case CITY_NOT_FOUND = 'CITY_NOT_FOUND';
    case DISTRICT_NOT_FOUND = 'DISTRICT_NOT_FOUND';
    case NEIGHBORHOOD_NOT_FOUND = 'NEIGHBORHOOD_NOT_FOUND';

    // ADDRESS
    case ADDRESS_NOT_FOUND = 'ADDRESS_NOT_FOUND';
    case LAST_ADDRESS_CANNOT_DELETE = 'LAST_ADDRESS_CANNOT_DELETE';

    // PRODUCT
    case PRODUCT_NOT_FOUND = 'PRODUCT_NOT_FOUND';
    case VARIANT_NOT_FOUND = 'VARIANT_NOT_FOUND';
    case SIZE_NOT_FOUND = 'SIZE_NOT_FOUND';
    case IMAGE_NOT_FOUND = 'IMAGE_NOT_FOUND';
    case ALREADY_ACTIVE = 'ALREADY_ACTIVE';

    // CART
    case CART_EMPTY = 'CART_EMPTY';
    case CART_ITEM_NOT_FOUND = 'CART_ITEM_NOT_FOUND';
    case INSUFFICIENT_STOCK = 'INSUFFICIENT_STOCK';

    // ORDER
    case ORDER_NOT_FOUND = 'ORDER_NOT_FOUND';
    case INVALID_STATUS_TRANSITION = 'INVALID_STATUS_TRANSITION';
    case PAYMENT_FAILED = 'PAYMENT_FAILED';

    // REVIEW
    case ALREADY_EXISTS = 'ALREADY_EXISTS';

    // GENERAL
    case NOT_FOUND = 'NOT_FOUND';
    case INTERNAL_SERVER_ERROR = 'INTERNAL_SERVER_ERROR';


    case INVALID_AMOUNT = 'INVALID_AMOUNT';
    
    case SOMETHING_WENT_WRONG  = 'SOMETHING_WENT_WRONG';


    public function message(): string
    {
        return match ($this) {

                // AUTH
            self::UNAUTHORIZED =>
            'Yetkisiz işlem.',

            self::INVALID_CREDENTIALS =>
            'Email veya şifre hatalı.',

            self::FORBIDDEN =>
            'Bu işlem için yetkiniz yok.',

                // VALIDATION
            self::VALIDATION_ERROR =>
            'Validation hatası.',

            self::INVALID_ROUTE_PARAM =>
            'Geçersiz route parametresi.',

            self::NO_CHANGES_DETECTED =>
            'Herhangi bir değişiklik yapılmadı.',

                // LOCATION
            self::CITY_NOT_FOUND =>
            'Şehir bulunamadı.',

            self::DISTRICT_NOT_FOUND =>
            'İlçe bulunamadı.',

            self::NEIGHBORHOOD_NOT_FOUND =>
            'Mahalle bulunamadı.',

                // ADDRESS
            self::ADDRESS_NOT_FOUND =>
            'Kullanıcıya ait adres bulunamadı.',

            self::LAST_ADDRESS_CANNOT_DELETE =>
            'Son adres silinemez.',

                // PRODUCT
            self::PRODUCT_NOT_FOUND =>
            'Ürün bulunamadı.',

            self::VARIANT_NOT_FOUND =>
            'Ürün varyantı bulunamadı.',

            self::SIZE_NOT_FOUND =>
            'Ürün bedeni bulunamadı.',

            self::IMAGE_NOT_FOUND =>
            'Ürün görseli bulunamadı.',

            self::ALREADY_ACTIVE =>
            'Ürün zaten aktif.',

                // CART
            self::CART_EMPTY =>
            'Sepet boş.',

            self::CART_ITEM_NOT_FOUND =>
            'Sepet ürünü bulunamadı.',

            self::INSUFFICIENT_STOCK =>
            'Yetersiz stok.',

                // ORDER
            self::ORDER_NOT_FOUND =>
            'Sipariş bulunamadı.',

            self::INVALID_STATUS_TRANSITION =>
            'Geçersiz sipariş durumu geçişi.',

            self::PAYMENT_FAILED =>
            'Ödeme başarısız oldu.',

                // REVIEW
            self::ALREADY_EXISTS =>
            'Kayıt zaten mevcut.',

                // GENERAL
            self::NOT_FOUND =>
            'Kayıt bulunamadı.',

            self::INTERNAL_SERVER_ERROR =>
            'Sunucu hatası oluştu.',

            self::INVALID_AMOUNT => 'Geçersiz bakiye miktarı',

            self::SOMETHING_WENT_WRONG => 'Bir hata oluştu.',
        };
    }

    public function status(): int
    {
        return match ($this) {

                // AUTH
            self::UNAUTHORIZED => 401,
            self::INVALID_CREDENTIALS => 401,
            self::FORBIDDEN => 403,

                // VALIDATION
            self::VALIDATION_ERROR => 422,
            self::INVALID_ROUTE_PARAM => 400,
            self::NO_CHANGES_DETECTED => 422,

                // LOCATION
            self::CITY_NOT_FOUND => 404,
            self::DISTRICT_NOT_FOUND => 404,
            self::NEIGHBORHOOD_NOT_FOUND => 404,

                // ADDRESS
            self::ADDRESS_NOT_FOUND => 404,
            self::LAST_ADDRESS_CANNOT_DELETE =>404,


                // PRODUCT
            self::PRODUCT_NOT_FOUND => 404,
            self::VARIANT_NOT_FOUND => 404,
            self::SIZE_NOT_FOUND => 404,
            self::IMAGE_NOT_FOUND => 404,
            self::ALREADY_ACTIVE => 409,

                // CART
            self::CART_EMPTY => 400,
            self::CART_ITEM_NOT_FOUND => 404,
            self::INSUFFICIENT_STOCK => 409,

                // ORDER
            self::ORDER_NOT_FOUND => 404,
            self::INVALID_STATUS_TRANSITION => 409,
            self::PAYMENT_FAILED => 400,

                // REVIEW
            self::ALREADY_EXISTS => 409,

                // GENERAL
            self::NOT_FOUND => 404,
            self::INTERNAL_SERVER_ERROR => 500,

            self::INVALID_AMOUNT => 422,
            self::SOMETHING_WENT_WRONG => 422,
        };
    }
}