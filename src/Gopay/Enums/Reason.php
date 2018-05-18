<?php

namespace Gopay\Enums;

final class Reason extends TypedEnum
{
    // phpcs:disable
    // SDK specific
    public static function REQUIRES_APP_TOKEN() { return self::create('A store or merchant app token is required during client creation'); }
    public static function REQUIRES_STORE_APP_TOKEN() { return self::create('A store app token is required and has not been included during client creation'); }
    public static function REQUIRES_MERCHANT_APP_TOKEN() { return self::create('A merchant app token is required and has not been included during client creation'); }
    public static function UNSUPPORTED_FEATURE() { return self::create('This feature is currently unsupported by the SDK'); }

    // Generic
    public static function INVALID_AMOUNT() { return self::create('INVALID_AMOUNT'); }
    public static function INVALID_FORMAT() { return self::create('INVALID_FORMAT'); }
    public static function INVALID_PERMISSIONS() { return self::create('INVALID_PERMISSIONS'); }
    public static function INVALID_TOKEN_TYPE() { return self::create('INVALID_TOKEN_TYPE'); }
    public static function INCOHERENT_DATE_RANGE() { return self::create('INCOHERENT_DATE_RANGE'); }

    // Subscriptions
    public static function NON_SUBSCRIPTION_PAYMENT() { return self::create('NON_SUBSCRIPTION_PAYMENT'); }
    public static function NOT_SUBSCRIPTION_PAYMENT() { return self::create('NOT_SUBSCRIPTION_PAYMENT'); }
    public static function SUBSCRIPTION_ALREADY_ENDED() { return self::create('SUBSCRIPTION_ALREADY_ENDED'); }
    public static function SUBSCRIPTION_PROCESSING() { return self::create('SUBSCRIPTION_PROCESSING'); }
    public static function INSTALLMENT_PLAN_NOT_FOUND() { return self::create('INSTALLMENT_PLAN_NOT_FOUND'); }
}
