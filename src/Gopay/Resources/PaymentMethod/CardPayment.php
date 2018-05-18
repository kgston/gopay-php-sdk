<?php

namespace Gopay\Resources\PaymentMethod;

use JsonSerializable;
use Gopay\Enums\PaymentType;
use Gopay\Enums\TokenType;

class CardPayment extends PaymentMethod implements JsonSerializable
{
    public $email;
    public $cardholder;
    public $cardNumber;
    public $expMonth;
    public $expYear;
    public $cvv;
    public $usageLimit = null;
    public $line1 = null;
    public $line2 = null;
    public $state = null;
    public $city = null;
    public $country = null;
    public $zip = null;
    public $phoneNumberCountryCode = null;
    public $phoneNumberLocalNumber = null;

    public function __construct(
        $email,
        $cardholder,
        $cardNumber,
        $expMonth,
        $expYear,
        $cvv,
        TokenType $type = null,
        UsageLimit $usageLimit = null,
        $line1 = null,
        $line2 = null,
        $state = null,
        $city = null,
        $country = null,
        $zip = null,
        $phoneNumberCountryCode = null,
        $phoneNumberLocalNumber = null,
        array $metadata = null
    ) {
        $this->acceptedTypes = TokenType::findValues();
        $type = $type === null ? TokenType::ONE_TIME(): $type;
        parent::__construct($email, PaymentType::CARD(), $type, $usageLimit, $metadata);

        $this->cardholder = $cardholder;
        $this->cardNumber = $cardNumber;
        $this->expMonth = $expMonth;
        $this->expYear = $expYear;
        $this->cvv = $cvv;
        $this->line1 = $line1;
        $this->line2 = $line2;
        $this->state = $state;
        $this->city = $city;
        $this->zip = $zip;
        $this->phoneNumberCountryCode = $phoneNumberCountryCode;
        $this->phoneNumberLocalNumber = $phoneNumberLocalNumber;
    }

    // Accepts all types
    protected function acceptsTokenType(TokenType $tokenType)
    {
    }

    public function jsonSerialize()
    {
        $parentData = parent::jsonSerialize();
        $data = array(
            "cardholder" => $this->cardholder,
            "card_number" => $this->cardNumber,
            "exp_month" => $this->expMonth,
            "exp_year" => $this->expYear,
            "cvv" => $this->cvv
        );
        
        if ($this->line1 &
            $this->state &&
            $this->city &&
            $this->country &&
            $this->zip &&
            $this->phoneNumberCountryCode &&
            $this->phoneNumberLocalNumber) {
            $this->data = array_merge($data, array(
                "line1" => $this->line1,
                "line2" => $this->line2,
                "state" => $this->state,
                "city" => $this->city,
                "country" => $this->country,
                "zip" => $this->zip,
                "phone_number" => array(
                    "country_code" => $this->phoneNumberCountryCode,
                    "local_number" => $this->phoneNumberLocalNumber
            )));
        }
        $parentData['data'] = $data;

        return $parentData;
    }
}
