<?php

namespace Gopay\Resources\Configuration;

use Gopay\Resources\Jsonable;
use Gopay\Utility\Json\JsonSchema;

class SecurityConfiguration
{

    use Jsonable;

    public $inspectSuspiciousLoginAfter;
    public $refundPercentLimit;
    public $limitChargeByCardConfiguration;

    public function __construct($inspectSuspiciousLoginAfter, $refundPercentLimit, $limitChargeByCardConfiguration)
    {
        $this->inspectSuspiciousLoginAfter = $inspectSuspiciousLoginAfter;
        $this->refundPercentLimit = $refundPercentLimit;
        $this->limitChargeByCardConfiguration = $limitChargeByCardConfiguration;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(SecurityConfiguration::class)
                ->upsert(
                    "limit_charge_by_card_configuration",
                    false,
                    $formatter = LimitChargeByCardConfiguration::getSchema()->getParser()
                );
    }
}
