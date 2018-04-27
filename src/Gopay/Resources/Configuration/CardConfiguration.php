<?php

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
    public $cardLimit;
    public $allowEmptyCvv;

    public function __construct($enabled, $debitEnabled, $prepaidEnabled, $forbiddenCardBrands, $allowedCountriesByIp, $foreignCardsAllowed, $failOnNewEmail, $cardLimit, $allowEmptyCvv)
    {
        $this->enabled = $enabled;
        $this->debitEnabled = $debitEnabled;
        $this->prepaidEnabled = $prepaidEnabled;
        $this->forbiddenCardBrands = $forbiddenCardBrands;
        $this->allowedCountriesByIp = $allowedCountriesByIp;
        $this->foreignCardsAllowed = $foreignCardsAllowed;
        $this->failOnNewEmail = $failOnNewEmail;
        $this->cardLimit = $cardLimit;
        $this->allowEmptyCvv = $allowEmptyCvv;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(CardConfiguration::class);
    }
}