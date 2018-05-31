<?php

namespace Gopay\Enums;

final class Field extends TypedEnum
{
    // phpcs:disable
    public static function AMOUNT() { return self::create(); }
    public static function CAPTURE_AT() { return self::create(); }
    public static function INITIAL_AMOUNT() { return self::create(); }
    public static function REASON() { return self::create(); }
    public static function SUBSEQUENT_CYCLES_START() { return self::create(); }
    public static function TYPE() { return self::create(); }
}
