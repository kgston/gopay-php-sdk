<?php

namespace Gopay\Resources;

use Gopay\Enums\Period;
use Gopay\Utility\Json\JsonSchema;

class Subscription extends Resource
{
    use Jsonable;

    public $storeId;
    public $transactionTokenId;
    public $amount;
    public $currency;
    public $amountFormatted;
    public $period;
    public $initialAmount;
    public $subsequentCyclesStart;
    public $status;
    public $metadata;
    public $mode;
    public $createdOn;

    public function __construct(
        $id,
        $storeId,
        $transactionTokenId,
        $amount,
        $currency,
        $amountFormatted,
        $period,
        $initialAmount,
        $subsequentCyclesStart,
        $status,
        $metadata,
        $mode,
        $createdOn,
        $installmentPlan,
        $context
    ) {
        if (!$period instanceof Period) {
            $period = Period::fromValue($period);
        }

        parent::__construct($id, $context);
        $this->storeId = $storeId;
        $this->transactionTokenId = $transactionTokenId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->amountFormatted = $amountFormatted;
        $this->period = $period;
        $this->initialAmount = $initialAmount;
        $this->subsequentCyclesStart = date_create($subsequentCyclesStart);
        $this->status = $status;
        $this->metadata = $metadata;
        $this->mode = $mode;
        $this->createdOn = date_create($createdOn);
        $this->installmentPlan = $installmentPlan;
    }

    public function cancel()
    {
        return RequesterUtils::executeDelete($this->getIdContext());
    }

    protected function getIdContext()
    {
        return $this->context->withPath(array("stores", $this->storeId, "subscriptions", $this->id));
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert("installment_plan", false, InstallmentPlan::getSchema()->getParser());
    }
}
