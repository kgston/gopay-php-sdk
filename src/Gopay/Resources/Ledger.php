<?php

namespace Gopay\Resources;

use Gopay\Enums\LedgerOrigin;
use Gopay\Utility\Json\JsonSchema;
use Money\Currency;

class Ledger
{
    private static $schema;

    public $id;
    public $storeId;
    public $amount;
    public $currency;
    public $amountFormatted;
    public $percentFee;
    public $flatFeeAmount;
    public $flatFeeCurrency;
    public $flatFeeFormatted;
    public $exchangeRate;
    public $origin;
    public $note;
    public $createdOn;

    public function __construct(
        $id,
        $storeId,
        $amount,
        $currency,
        $amountFormatted,
        $percentFee,
        $flatFeeAmount,
        $flatFeeCurrency,
        $flatFeeFormatted,
        $exchangeRate,
        $origin,
        $note,
        $createdOn
    ) {
        $this->id = $id;
        $this->storeId = $storeId;
        $this->amount = $amount;
        $this->currency = new Currency($currency);
        $this->amountFormatted = $amountFormatted;
        $this->percentFee = $percentFee;
        $this->flatFeeAmount = $flatFeeAmount;
        $this->flatFeeCurrency = new Currency($flatFeeCurrency);
        $this->flatFeeFormatted = $flatFeeFormatted;
        $this->exchangeRate = $exchangeRate;
        $this->origin = LedgerOrigin::fromValue($origin);
        $this->note = $note;
        $this->createdOn = date_create($createdOn);
    }

    public static function getSchema()
    {
        if (!isset(self::$schema)) {
            self::$schema = JsonSchema::fromClass(self::class);
        }
        return self::$schema;
    }
}
