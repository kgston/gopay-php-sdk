<?php

namespace Gopay\Enums;

final class InstallmentPlanType extends TypedEnum
{
    // phpcs:disable
    public static function REVOLVING() { return self::create('revolving'); }
    public static function FIXED_CYCLES() { return self::create('fixed_cycles'); }
    public static function FIXED_CYCLE_AMOUNT() { return self::create('fixed_cycle_amount'); }
}
