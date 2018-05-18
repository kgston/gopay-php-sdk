<?php

namespace Gopay\Resources\Configuration;

use Gopay\Resources\Jsonable;
use Gopay\Utility\Json\JsonSchema;

class RecurringConfiguration
{
    use Jsonable;

    public $recurringType;
    public $chargeWaitPeriod;
    public $cardChargeCvvConfirmation;

    public function __construct($recurringType, $chargeWaitPeriod, $cardChargeCvvConfirmation)
    {
        $this->recurringType = $recurringType;
        $this->chargeWaitPeriod = $chargeWaitPeriod;
        $this->cardChargeCvvConfirmation = $cardChargeCvvConfirmation;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(RecurringConfiguration::class)
            ->upsert(
                "card_charge_cvv_confirmation",
                true,
                $formatter = CardChargeCvvConfirmation::getSchema()->getParser()
            );
    }
}
