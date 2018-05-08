<?php
namespace Gopay\Resources\PaymentData;

use Gopay\Utility\Json\JsonSchema;

class Address
{
    private static $schema;

    public $line1;
    public $line2;
    public $state;
    public $city;
    public $country;
    public $zip;
    public $phoneNumber;

    public function __construct($line1, $line2, $state, $city, $country, $zip, $phoneNumber)
    {
        $this->line1 = $line1;
        $this->line2 = $line2;
        $this->state = $state;
        $this->city = $city;
        $this->country = $country;
        $this->zip = $zip;
        $this->phoneNumber = $phoneNumber;
    }

    public static function getSchema()
    {
        if (!isset(self::$schema)) {
            self::$schema = JsonSchema::fromClass(self::class);
        }
        return self::$schema;
    }
}
