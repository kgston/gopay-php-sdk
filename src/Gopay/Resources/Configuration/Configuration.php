<?php

namespace Gopay\Resources\Configuration;

use Gopay\Resources\Jsonable;
use Gopay\Resources\PaymentData\Card;
use Gopay\Utility\Json\JsonSchema;

class Configuration
{
    use Jsonable;

    public $percentFee;
    public $flatFees;
    public $logoUrl;
    public $country;
    public $language;
    public $timeZone;
    public $minTransferPayout;
    public $transferSchedule;
    public $userTransactionsConfiguration;
    public $cardConfiguration;
    public $qrScanConfiguration;
    public $convenienceConfiguration;
    public $recurringTokenConfiguration;
    public $securityConfiguration;
    public $installmentsConfiguration;
    public $cardBrandPercentFees;

    public function __construct(
        $percentFee,
        $flatFees,
        $logoUrl,
        $country,
        $language,
        $timeZone,
        $minTransferPayout,
        $transferSchedule,
        $userTransactionsConfiguration,
        $cardConfiguration,
        $qrScanConfiguration,
        $convenienceConfiguration,
        $recurringTokenConfiguration,
        $securityConfiguration,
        $installmentsConfiguration,
        $cardBrandPercentFees
    ) {
        $this->percentFee = $percentFee;
        $this->flatFees = $flatFees;
        $this->logoUrl = $logoUrl;
        $this->country = $country;
        $this->language = $language;
        $this->timeZone = $timeZone;
        $this->minTransferPayout = $minTransferPayout;
        $this->transferSchedule = $transferSchedule;
        $this->userTransactionsConfiguration = $userTransactionsConfiguration;
        $this->cardConfiguration = $cardConfiguration;
        $this->qrScanConfiguration = $qrScanConfiguration;
        $this->convenienceConfiguration = $convenienceConfiguration;
        $this->recurringTokenConfiguration = $recurringTokenConfiguration;
        $this->securityConfiguration = $securityConfiguration;
        $this->installmentsConfiguration = $installmentsConfiguration;
        $this->cardBrandPercentFees = $cardBrandPercentFees;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(Configuration::class)
                ->upsert(
                    "transfer_schedule",
                    false,
                    $formatter = TransferSchedule::getSchema()->getParser()
                )
                ->upsert(
                    "user_transactions_configuration",
                    true,
                    $formatter = UserTransactionsConfiguration::getSchema()->getParser()
                )
                ->upsert(
                    "card_configuration",
                    true,
                    $formatter = CardConfiguration::getSchema()->getParser()
                )
                ->upsert(
                    "qr_scan_configuration",
                    true,
                    $formatter = QRConfiguration::getSchema()->getParser()
                )
                ->upsert(
                    "convenience_configuration",
                    true,
                    $formatter = ConvenienceConfiguration::getSchema()->getParser()
                )
                ->upsert(
                    "recurring_token_configuration",
                    true,
                    $formatter = RecurringConfiguration::getSchema()->getParser()
                )
                ->upsert(
                    "security_configuration",
                    true,
                    $formatter = SecurityConfiguration::getSchema()->getParser()
                )
                ->upsert(
                    "installments_configuration",
                    true,
                    $formatter = InstallmentsConfiguration::getSchema()->getParser()
                )
                ->upsert(
                    "card_brand_percent_fees",
                    true,
                    $formatter = CardBrandPercentFees::getSchema()->getParser()
                );
    }
}
