<?php

namespace Gopay\Resources;

use Gopay\Enums\AppTokenMode;
use Gopay\Enums\RefundReason;
use Gopay\Enums\RefundStatus;
use Gopay\Utility\Json\JsonSchema;
use Money\Currency;
use Money\Money;

class Refund extends Resource
{
    use Jsonable;
    use Pollable;

    public $storeId;
    public $chargeId;
    public $status;
    public $currency;
    public $amount;
    public $amountFormatted;
    public $reason;
    public $message;
    public $error;
    public $metadata;
    public $mode;
    public $createdOn;

    public function __construct(
        $id,
        $storeId,
        $chargeId,
        $status,
        $currency,
        $amount,
        $amountFormatted,
        $reason,
        $message,
        $error,
        $metadata,
        $mode,
        $createdOn,
        $context
    ) {
        parent::__construct($id, $context);
        $this->storeId = $storeId;
        $this->chargeId = $chargeId;
        $this->status = RefundStatus::fromValue($status);
        $this->currency = new Currency($currency);
        $this->amount = new Money($amount, $this->currency);
        $this->amountFormatted = $amountFormatted;
        $this->reason = RefundReason::fromValue($reason);
        $this->message = $message;
        $this->error = $error;
        $this->metadata = $metadata;
        $this->mode = AppTokenMode::fromValue($mode);
        $this->createdOn = date_create($createdOn);
    }


    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
