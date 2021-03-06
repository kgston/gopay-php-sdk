<?php

namespace Gopay\Enums;

final class PaymentType extends TypedEnum
{
    // phpcs:disable
    public static function CARD() { return self::create(); }
    public static function QR_SCAN() { return self::create(); }
    public static function KONBINI() { return self::create(); }
    public static function APPLE_PAY() { return self::create(); }
}
