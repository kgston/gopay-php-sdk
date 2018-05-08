<?php

namespace Gopay\Resources\Configuration;

use Gopay\Resources\Jsonable;
use Gopay\Utility\Json\JsonSchema;

class TransferSchedule
{
    use Jsonable;

    public $waitPeriod;
    public $period;
    public $dayOfWeek;
    public $weekOfMonth;
    public $dayOfMonth;

    public function __construct($waitPeriod, $period, $dayOfWeek, $weekOfMonth, $dayOfMonth)
    {
        $this->waitPeriod = $waitPeriod;
        $this->period = $period;
        $this->dayOfWeek = $dayOfWeek;
        $this->weekOfMonth = $weekOfMonth;
        $this->dayOfMonth = $dayOfMonth;
    }


    protected static function initSchema()
    {
        return JsonSchema::fromClass(TransferSchedule::class);
    }
}
