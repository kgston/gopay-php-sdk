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
    public $qrScanConfiguration;
    public $recurringConfiguration;
    public $securityConfiguration;

    public function __construct($percentFee,
                                $flatFeeAmount,
                                $flatFeeCurrency,
                                $waitPeriod,
                                $transferPeriod,
                                $logoUrl,
                                $cardConfiguration,
                                $qrScanConfiguration,
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
        $this->qrScanConfiguration = $qrScanConfiguration;
        $this->recurringConfiguration = $recurringConfiguration;
        $this->securityConfiguration = $securityConfiguration;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(Configuration::class)
                ->upsert("card_configuration", true, $formatter = CardConfiguration::getSchema()->getParser())
                ->upsert("qr_scan_configuration", true, $formatter = QRConfiguration::getSchema()->getParser())
                ->upsert("recurring_configuration", true, $formatter = RecurringConfiguration::getSchema()->getParser())
                ->upsert("security_configuration", true, $formatter = SecurityConfiguration::getSchema()->getParser());
    }
}