<?php

namespace Gopay\Enums;

final class Period extends TypedEnum
{
    // phpcs:disable
    public static function DAILY() { return self::create('daily'); }
    public static function WEEKLY() { return self::create('weekly'); }
    public static function BIWEEKLY() { return self::create('biweekly'); }
    public static function MONTHLY() { return self::create('monthly'); }
    public static function QUARTERLY() { return self::create('quarterly'); }
    public static function SEMIANNUALLY() { return self::create('semiannually'); }
    public static function ANNUALLY() { return self::create('annually'); }
}
