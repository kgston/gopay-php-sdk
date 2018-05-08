<?php

namespace Gopay\Resources\PaymentData;

use Gopay\Utility\Json\JsonSchema;

class Card
{
    private static $schema;

    public $cardholder;
    public $expMonth;
    public $expYear;
    public $lastFour;
    public $brand;
    public $country;

    public function __construct($cardholder, $expMonth, $expYear, $lastFour, $brand, $country)
    {
        $this->cardholder = $cardholder;
        $this->expMonth = $expMonth;
        $this->expYear = $expYear;
        $this->lastFour = $lastFour;
        $this->brand = $brand;
        $this->country = $country;
    }


    public static function getSchema()
    {
        if (!isset(self::$schema)) {
            self::$schema = JsonSchema::fromClass(self::class);
        }
        return self::$schema;
    }
}
