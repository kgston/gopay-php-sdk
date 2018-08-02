<?php

namespace Gopay\Utility;

use DateTimeZone;
use Gopay\Enums\InstallmentPlanType;

class FormatterUtils
{
    public static function of($functionName)
    {
        return self::class . "::$functionName";
    }

    public static function getDateTime($dateTime)
    {
        return date_create($dateTime);
    }

    public static function getDateTimeZone($dateTimeZone)
    {
        return new DateTimeZone($dateTimeZone);
    }

    public static function getInstallmentPlanType($installmentPlanType)
    {
        return InstallmentPlanType::fromValue($installmentPlanType);
    }
}
