<?php
namespace App\Enums;


enum ErrorCode: string
{
    case CITY_NOT_FOUND = 'CITY_NOT_FOUND';
    case INVALID_ROUTE_PARAM = 'INVALID_ROUTE_PARAM';

    case ADDRESS_NOT_FOUND = 'ADDRESS_NOT_FOUND';

    case VALIDATION_ERROR = 'VALIDATION_ERROR';

    public function message(): string
    {
        return match($this) {
            self::CITY_NOT_FOUND => 'Şehir bulunamadı',
            self::INVALID_ROUTE_PARAM => 'Geçersiz parametre',
            self::VALIDATION_ERROR => 'Validation hatası',
            self::ADDRESS_NOT_FOUND => 'Kullıcıya ait adres bulunamadı'
        };
    }

    public function status(): int
    {
        return match($this) {
            self::CITY_NOT_FOUND => 404,
             self::INVALID_ROUTE_PARAM => 400,
             self::VALIDATION_ERROR => 422,
             self::ADDRESS_NOT_FOUND => 404

        };
    }
}