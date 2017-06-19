<?php

namespace Gopay\Resources;

use Gopay\Utility\Json\JsonSchema;

class BankAccount extends Resource {
    use Jsonable;

    public $primary;
    public $holderName;
    public $bank_name;
    public $branchName;
    public $country;
    public $bankAddress;
    public $currency;
    public $accountNumber;
    public $routingNumber;
    public $swiftCode;
    public $ifscCode;
    public $routingCode;
    public $lastFour;
    public $status;
    public $active;
    public $createdOn;

    public function __construct($id, $primary, $holderName, $bank_name, $branchName, $country, $bankAddress, $currency, $accountNumber, $routingNumber, $swiftCode, $ifscCode, $routingCode, $lastFour, $status, $active, $createdOn, $context)
    {
        parent::__construct($id, $context);
        $this->primary = $primary;
        $this->holderName = $holderName;
        $this->bank_name = $bank_name;
        $this->branchName = $branchName;
        $this->country = $country;
        $this->bankAddress = $bankAddress;
        $this->currency = $currency;
        $this->accountNumber = $accountNumber;
        $this->routingNumber = $routingNumber;
        $this->swiftCode = $swiftCode;
        $this->ifscCode = $ifscCode;
        $this->routingCode = $routingCode;
        $this->lastFour = $lastFour;
        $this->status = $status;
        $this->active = $active;
        $this->createdOn = $createdOn;
    }


    protected static function initSchema()
    {
        return JsonSchema::fromClass(BankAccount::class);
    }
}