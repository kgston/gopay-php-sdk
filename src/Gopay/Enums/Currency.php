<?php

namespace Gopay\Enums;

final class Currency extends TypedEnum
{
    // phpcs:disable
    public static function JPY() { return self::create('JPY'); }
    public static function USD() { return self::create('USD'); }
    public static function EUR() { return self::create('EUR'); }
    public static function CNY() { return self::create('CNY'); }
    public static function TWD() { return self::create('TWD'); }
    public static function SGD() { return self::create('SGD'); }
}
