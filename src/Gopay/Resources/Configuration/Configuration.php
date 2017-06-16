<?php

namespace Gopay\Resources\Configuration;

use Gopay\Resources\Jsonable;
use Gopay\Utility\Json\JsonSchema;


class Configuration {
    use Jsonable;

    public $percentFee;
    public $flatFeeAmount;
    public $flatFeeCurrency;
    public $waitPeriod;
    public $transferPeriod;
    public $logoUrl;
    public $cardConfiguration;
    public $qrConfiguration;
    public $recurringConfiguration;
    public $securityConfiguration;

    public function __construct($percentFee,
                                $flatFeeAmount,
                                $flatFeeCurrency,
                                $waitPeriod,
                                $transferPeriod,
                                $logoUrl,
                                $cardConfiguration,
                                $qrConfiguration,
                                $recurringConfiguration,
                                $securityConfiguration)
    {
        $this->percentFee = $percentFee;
        $this->flatFeeAmount = $flatFeeAmount;
        $this->flatFeeCurrency = $flatFeeCurrency;
        $this->waitPeriod = $waitPeriod;
        $this->transferPeriod = $transferPeriod;
        $this->logoUrl = $logoUrl;
        $this->cardConfiguration = $cardConfiguration;
        $this->qrConfiguration = $qrConfiguration;
        $this->recurringConfiguration = $recurringConfiguration;
        $this->securityConfiguration = $securityConfiguration;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(Configuration::class)
                ->replace("card_configuration", $formatter = CardConfiguration::getSchema()->parse)
                ->replace("qr_configuration", $formatter = QRConfiguration::getSchema()->parse)
                ->replace("recurring_configuration", $formatter = RecurringConfiguration::getSchema()->parse)
                ->replace("security_configuration", $formatter = SecurityConfiguration::getSchema()->parse);
    }
}