<?php

namespace Gopay\Resources;


use Gopay\Utility\Json\JsonSchema;

class Ledger
{
    private static $schema;

    public $id;
    public $storeId;
    public $transferId;
    public $amount;
    public $currency;
    public $amountFormatted;
    public $percentFee;
    public $flatFeeAmount;
    public $flatFeeCurrency;
    public $flatFeeFormatted;
    public $exchangeRate;
    public $note;
    public $createdOn;

    public function __construct($id, $storeId, $transferId, $amount, $currency, $amountFormatted, $percentFee, $flatFeeAmount, $flatFeeCurrency, $flatFeeFormatted, $exchangeRate, $note, $createdOn)
    {
        $this->id = $id;
        $this->storeId = $storeId;
        $this->transferId = $transferId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->amountFormatted = $amountFormatted;
        $this->percentFee = $percentFee;
        $this->flatFeeAmount = $flatFeeAmount;
        $this->flatFeeCurrency = $flatFeeCurrency;
        $this->flatFeeFormatted = $flatFeeFormatted;
        $this->exchangeRate = $exchangeRate;
        $this->note = $note;
        $this->createdOn = $createdOn;
    }


    public static function getSchema() {
        if (!isset(self::$schema)) {
            self::$schema = JsonSchema::fromClass(self::class);

        }
        return self::$schema;
    }

}