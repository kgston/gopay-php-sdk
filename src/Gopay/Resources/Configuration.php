<?php

namespace Gopay\Resources;

use function Gopay\Utility\get_or_else;
use function Gopay\Utility\get_or_null;

class CardConfiguration {

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

    public static function fromJson($json) {
        return new CardConfiguration(
            get_or_null($json, "enabled"),
            get_or_null($json, "debit_enabled"),
            get_or_null($json, "prepaid_enabled"),
            get_or_null($json, "forbidden_card_brands"),
            get_or_null($json, "allowed_countries_by_ip"),
            get_or_null($json, "foreign_cards_allowed"),
            get_or_null($json, "fail_on_new_email")
        );
    }

}

class QRConfiguration {
    public $enabled;
    public $forbidden_qr_scan_gateway;

    public function __construct($enabled, $forbidden_qr_scan_gateway)
    {
        $this->enabled = $enabled;
        $this->forbidden_qr_scan_gateway = $forbidden_qr_scan_gateway;
    }

    public static function fromJson($json) {
        return new QRConfiguration(
            get_or_null($json, "enabled"),
            get_or_null($json, "forbidden_qr_scan_gateway")
        );
    }

}

class RecurringConfiguration {

    public $recurring_type;
    public $charge_wait_period;

    public function __construct($recurring_type, $charge_wait_period) {
        $this->recurring_type = $recurring_type;
        $this->charge_wait_period = $charge_wait_period;
    }

    public static function fromJson($json) {
        return new RecurringConfiguration(
            get_or_null($json, "recurring_type"),
            get_or_null($json, "charge_wait_period")
        );
    }

}

class SecurityConfiguration {

    public $inspectSuspiciousLoginAfter;

    public function __construct($inspectSuspiciousLoginAfter)
    {
        $this->inspectSuspiciousLoginAfter = $inspectSuspiciousLoginAfter;
    }

    public static function fromJson($json) {
        return new SecurityConfiguration(
            get_or_null($json, "inspect_suspicious_login_after")
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
            get_or_null($json, "percent_fee"),
            get_or_null($json, "flat_fee_amount"),
            get_or_null($json, "flat_fee_currency"),
            get_or_null($json, "wait_period"),
            get_or_null($json, "transfer_period"),
            get_or_null($json, "logo_url"),
            CardConfiguration::fromJson(get_or_else($json, "card_configuration", array())),
            QRConfiguration::fromJson(get_or_else($json, "qr_configuration", array())),
            RecurringConfiguration::fromJson(get_or_else($json, "recurring_configuration", array())),
            SecurityConfiguration::fromJson(get_or_else($json, "security_configuration", array()))
        );
    }

}