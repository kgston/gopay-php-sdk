<?php

namespace Gopay\Resources\Configuration;

use Gopay\Resources\Jsonable;
use Gopay\Resources\PaymentData\Card;
use Gopay\Utility\Json\JsonSchema;


class Configuration {
    use Jsonable;

    public $defaultPercentFee;
    public $flatFees;
    public $logoUrl;
    public $country;
    public $language;
    public $transferSchedule;
    public $cardConfiguration;
    public $qrScanConfiguration;
    public $convenienceConfiguration;
    public $recurringConfiguration;
    public $securityConfiguration;
    public $cardBrandPercentFees;

    public function __construct($defaultPercentFee,
                                $flatFees,
                                $logoUrl,
                                $country,
                                $language,
                                $transferSchedule,
                                $cardConfiguration,
                                $qrScanConfiguration,
                                $convenienceConfiguration,
                                $recurringConfiguration,
                                $securityConfiguration,
                                $cardBrandPercentFees)
    {
        $this->defaultPercentFee = $defaultPercentFee;
        $this->flatFees = $flatFees;
        $this->logoUrl = $logoUrl;
        $this->country = $country;
        $this->language = $language;
        $this->transferSchedule = $transferSchedule;
        $this->cardConfiguration = $cardConfiguration;
        $this->qrScanConfiguration = $qrScanConfiguration;
        $this->convenienceConfiguration = $convenienceConfiguration;
        $this->recurringConfiguration = $recurringConfiguration;
        $this->securityConfiguration = $securityConfiguration;
        $this->cardBrandPercentFees = $cardBrandPercentFees;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(Configuration::class)
                ->upsert("transfer_schedule", true, $formatter = TransferSchedule::getSchema()->getParser())
                ->upsert("card_configuration", true, $formatter = CardConfiguration::getSchema()->getParser())
                ->upsert("qr_scan_configuration", true, $formatter = QRConfiguration::getSchema()->getParser())
                ->upsert("recurring_configuration", true, $formatter = RecurringConfiguration::getSchema()->getParser())
                ->upsert("security_configuration", true, $formatter = SecurityConfiguration::getSchema()->getParser())
                ->upsert("card_brand_percent_fees", true, $formatter = CardBrandPercentFees::getSchema()->getParser());
    }
}