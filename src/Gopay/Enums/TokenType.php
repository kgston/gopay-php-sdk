<?php

namespace Gopay\Enums;

final class TokenType extends TypedEnum
{
    // phpcs:disable
    public static function ONE_TIME() { return self::create('one_time'); }
    public static function RECURRING() { return self::create('recurring'); }
    public static function SUBSCRIPTION() { return self::create('subscription'); }
}
