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
    public $category;
    public $issuer;
    public $subBrand;

    public function __construct(
        $cardholder,
        $expMonth,
        $expYear,
        $lastFour,
        $brand,
        $country,
        $category,
        $issuer,
        $subBrand
    ) {
        $this->cardholder = $cardholder;
        $this->expMonth = $expMonth;
        $this->expYear = $expYear;
        $this->lastFour = $lastFour;
        $this->brand = $brand;
        $this->country = $country;
        $this->category = $category;
        $this->issuer = $issuer;
        $this->subBrand = $subBrand;
    }


    public static function getSchema()
    {
        if (!isset(self::$schema)) {
            self::$schema = JsonSchema::fromClass(self::class);
        }
        return self::$schema;
    }
}
