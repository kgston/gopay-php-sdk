<?php

namespace Gopay\Resources\Configuration;

use Gopay\Resources\Jsonable;
use Gopay\Utility\Json\JsonSchema;

class RecurringConfiguration
{
    use Jsonable;

    public $recurringType;
    public $chargeWaitPeriod;

    public function __construct($recurringType, $chargeWaitPeriod)
    {
        $this->recurringType = $recurringType;
        $this->chargeWaitPeriod = $chargeWaitPeriod;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(RecurringConfiguration::class);
    }
}
