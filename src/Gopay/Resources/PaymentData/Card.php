<?php

namespace Gopay\Resources\PaymentData;

use Gopay\Enums\CardBrand;
use Gopay\Enums\CardCategory;
use Gopay\Enums\CardSubBrand;
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
        $this->brand = CardBrand::fromValue($brand);
        $this->country = $country;
        $this->category = CardCategory::fromValue($category);
        $this->issuer = $issuer;
        $this->subBrand = CardSubBrand::fromValue($subBrand);
    }


    public static function getSchema()
    {
        if (!isset(self::$schema)) {
            self::$schema = JsonSchema::fromClass(self::class);
        }
        return self::$schema;
    }
}
