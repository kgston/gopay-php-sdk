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
    public static function INVALID_AMOUNT() { return self::create(); }
    public static function INVALID_FORMAT() { return self::create(); }
    public static function INVALID_PERMISSIONS() { return self::create(); }
    public static function INVALID_TOKEN_TYPE() { return self::create(); }
    public static function MUST_BE_FUTURE_TIME() { return self::create(); }
    public static function FORBIDDEN_PARAMETER() { return self::create(); }

    // Subscriptions
    public static function NON_SUBSCRIPTION_PAYMENT() { return self::create(); }
    public static function NOT_SUBSCRIPTION_PAYMENT() { return self::create(); }
    public static function SUBSCRIPTION_ALREADY_ENDED() { return self::create(); }
    public static function SUBSCRIPTION_PROCESSING() { return self::create(); }
    public static function INSTALLMENT_ALREADY_SET() { return self::create(); }
    public static function MUST_BE_MONTH_BASE_TO_SET() { return self::create(); }
    public static function CANNOT_CHANGE_CANCELED_SUBSCRIPTION() { return self::create(); }
    public static function CANNOT_SET_AFTER_SUBSCRIPTION_STARTED() { return self::create(); }
    public static function CANNOT_CHANGE_TOKEN() { return self::create(); }
    public static function NEED_AT_LEAST_TWO_CYCLES() { return self::create(); }
}
