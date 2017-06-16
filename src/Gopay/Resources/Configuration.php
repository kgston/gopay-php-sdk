<?php

namespace Gopay\Resources;

use Gopay\Utility\FunctionalUtils as fp;
use Gopay\Utility\JsonSchema;

class CardConfiguration {

    private static $schema;

    public $enabled;
    public $debitEnabled;
    public $prepaidEnabled;
    public $forbiddenCardBrands;
    public $allowedCountriesByIp;
    public $foreignCardsAllowed;
    public $failOnNewEmail;

    public function __construct($enabled, $debitEnabled, $prepaidEnabled, $forbiddenCardBrands, $allowedCountriesByIp, $foreignCardsAllowed, $failOnNewEmail)
    {
        $this->enabled = $enabled;
        $this->debitEnabled = $debitEnabled;
        $this->prepaidEnabled = $prepaidEnabled;
        $this->forbiddenCardBrands = $forbiddenCardBrands;
        $this->allowedCountriesByIp = $allowedCountriesByIp;
        $this->foreignCardsAllowed = $foreignCardsAllowed;
        $this->failOnNewEmail = $failOnNewEmail;
    }

    public static function getSchema() {
        if (!isset(self::$schema)) {
            self::schema = JsonSchema::fromClass(CardConfiguration::class);
        }
        return self::$schema;
    }

    public static function fromJson($json) {
        return new CardConfiguration(
            fp::get_or_null($json, "enabled"),
            fp::get_or_null($json, "debit_enabled"),
            fp::get_or_null($json, "prepaid_enabled"),
            fp::get_or_null($json, "forbidden_card_brands"),
            fp::get_or_null($json, "allowed_countries_by_ip"),
            fp::get_or_null($json, "foreign_cards_allowed"),
            fp::get_or_null($json, "fail_on_new_email")
        );
    }

}



class QRConfiguration {
    //$schema = JsonSchema::fromClass(QRConfiguration::class);
    public $enabled;
    public $forbiddenQrScanGateway;

    public function __construct($enabled, $forbiddenQrScanGateway)
    {
        $this->enabled = $enabled;
        $this->forbiddenQrScanGateway = $forbiddenQrScanGateway;
    }

    public static function fromJson($json) {
        return new QRConfiguration(
            fp::get_or_null($json, "enabled"),
            fp::get_or_null($json, "forbidden_qr_scan_gateway")
        );
    }

}

//$recurringJsonSchema = JsonSchema::fromClass(RecurringConfiguration::class);

class RecurringConfiguration {

    public $recurringType;
    public $chargeWaitPeriod;

    public function __construct($recurringType, $chargeWaitPeriod) {
        $this->recurringType = $recurringType;
        $this->chargeWaitPeriod = $chargeWaitPeriod;
    }

    public static function fromJson($json) {
        return new RecurringConfiguration(
            fp::get_or_null($json, "recurring_type"),
            fp::get_or_null($json, "charge_wait_period")
        );
    }

}

//$securityConfSchema = JsonSchema::fromClass(SecurityConfiguration::class);

class SecurityConfiguration {

    public $inspectSuspiciousLoginAfter;

    public function __construct($inspectSuspiciousLoginAfter)
    {
        $this->inspectSuspiciousLoginAfter = $inspectSuspiciousLoginAfter;
    }

    public static function fromJson($json) {
        return new SecurityConfiguration(
            fp::get_or_null($json, "inspect_suspicious_login_after")
        );
    }

}


class Configuration
{
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

    public static function fromJson(array $json) {
        return new Configuration(
            fp::get_or_null($json, "percent_fee"),
            fp::get_or_null($json, "flat_fee_amount"),
            fp::get_or_null($json, "flat_fee_currency"),
            fp::get_or_null($json, "wait_period"),
            fp::get_or_null($json, "transfer_period"),
            fp::get_or_null($json, "logo_url"),
            CardConfiguration::fromJson(fp::get_or_else($json, "card_configuration", array())),
            QRConfiguration::fromJson(fp::get_or_else($json, "qr_configuration", array())),
            RecurringConfiguration::fromJson(fp::get_or_else($json, "recurring_configuration", array())),
            SecurityConfiguration::fromJson(fp::get_or_else($json, "security_configuration", array()))
        );
    }

}