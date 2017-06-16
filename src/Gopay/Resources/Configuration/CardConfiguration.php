<?php
/**
 * Created by PhpStorm.
 * User: adamsar
 * Date: 6/16/17
 * Time: 1:59 PM
 */

namespace Gopay\Resources\Configuration;

use Gopay\Resources\Jsonable;
use Gopay\Utility\FunctionalUtils as fp;
use Gopay\Utility\Json\JsonSchema;

class CardConfiguration
{

    use Jsonable;

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

    public static function fromJson($json)
    {
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

    protected static function initSchema()
    {
        return JsonSchema::fromClass(CardConfiguration::class);
    }
}