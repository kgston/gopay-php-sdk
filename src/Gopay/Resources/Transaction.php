<?php

namespace Gopay\Resources;

use Gopay\Enums\AppTokenMode;
use Gopay\Enums\ChargeStatus;
use Gopay\Enums\TransactionType;
use Gopay\Utility\Json\JsonSchema;
use Money\Currency;
use Money\Money;

class Transaction
{
    use Jsonable;

    public $id;
    public $storeId;
    public $resourceId;
    public $chargeId;
    public $currency;
    public $amount;
    public $amountFormatted;
    public $type;
    public $status;
    public $metadata;
    public $mode;
    public $createdOn;
    private $context;

    public function __construct(
        $id,
        $storeId,
        $resourceId,
        $chargeId,
        $currency,
        $amount,
        $amountFormatted,
        $type,
        $status,
        $metadata,
        $mode,
        $createdOn,
        $context
    ) {
        $this->id = $id;
        $this->storeId = $storeId;
        $this->resourceId = $resourceId;
        $this->chargeId = $chargeId;
        $this->currency = new Currency($currency);
        $this->amount = new Money($amount, $this->currency);
        $this->amountFormatted = $amountFormatted;
        $this->type = TransactionType::fromValue($type);
        $this->status = ChargeStatus::fromValue($status);
        $this->metadata = $metadata;
        $this->mode = AppTokenMode::fromValue($mode);
        $this->createdOn = date_create($createdOn);
        $this->context = $context;
    }


    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
