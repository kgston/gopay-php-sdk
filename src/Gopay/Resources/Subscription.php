<?php

namespace Gopay\Resources;

use Gopay\Enums\AppTokenMode;
use Gopay\Enums\Currency;
use Gopay\Enums\InstallmentPlanType;
use Gopay\Enums\Period;
use Gopay\Enums\Reason;
use Gopay\Enums\SubscriptionStatus;
use Gopay\Errors\GopayLogicError;
use Gopay\Utility\RequesterUtils;
use Gopay\Utility\Json\JsonSchema;

class Subscription extends Resource
{
    use Jsonable;
    use Pollable;

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
        parent::__construct($id, $context);
        $this->storeId = $storeId;
        $this->transactionTokenId = $transactionTokenId;
        $this->amount = $amount;
        $this->currency = Currency::fromValue($currency);
        $this->amountFormatted = $amountFormatted;
        $this->period = Period::fromValue($period);
        $this->initialAmount = $initialAmount;
        $this->subsequentCyclesStart = date_create($subsequentCyclesStart);
        $this->status = SubscriptionStatus::fromValue($status);
        $this->metadata = $metadata;
        $this->mode = AppTokenMode::fromValue($mode);
        $this->createdOn = date_create($createdOn);
        $this->installmentPlan = $installmentPlan;
    }

    public function patch(
        $transactionTokenId = null,
        $amount = null,
        Currency $currency = null,
        $initialAmount = null,
        array $metadata = null,
        InstallmentPlan $installmentPlan = null
    ) {
        if ($this->isTerminal()) {
            throw new GopayLogicError(Reason::SUBSCRIPTION_ALREADY_ENDED());
        }
        if ($this->isProcessing()) {
            throw new GopayLogicError(Reason::SUBSCRIPTION_PROCESSING());
        }
        if ($this->installmentPlan === null && $installmentPlan->planType === InstallmentPlanType::NONE()) {
            throw new GopayLogicError(Reason::INSTALLMENT_PLAN_NOT_FOUND());
        }

        $payload = array();
        if (isset($transactionTokenId)) {
            $payload['transaction_token_id'] = $transactionTokenId;
        }
        if (isset($amount)) {
            $payload['amount'] = $amount;
        }
        if (isset($currency)) {
            $payload['currency'] = $currency->getValue();
        }
        if (isset($initialAmount)) {
            $payload['initial_amount'] = $initialAmount;
        }
        if (isset($metadata)) {
            $payload['metadata'] = $metadata;
        }
        if (isset($installmentPlan)) {
            $payload['installment_plan'] = $installmentPlan;
        }
        return $this->update($payload);
    }

    public function cancel()
    {
        return RequesterUtils::executeDelete($this->getIdContext());
    }

    public function isEditable()
    {
        return $this->status === SubscriptionStatus::UNVERIFIED() ||
            $this->status === SubscriptionStatus::UNCONFIRMED();
    }
    
    public function isProcessing()
    {
        return $this->status === SubscriptionStatus::UNPAID() ||
            $this->status === SubscriptionStatus::CURRENT() ||
            $this->status === SubscriptionStatus::SUSPENDED();
    }

    public function isTerminal()
    {
        return $this->status === SubscriptionStatus::CANCELED() ||
            $this->status === SubscriptionStatus::COMPLETED();
    }

    public static function isSubscribable(PaymentType $paymentType)
    {
        return PaymentType::CARD() === $paymentType ||
            PaymentType::KONBINI() === $paymentType ||
            PaymentType::APPLE_PAY() === $paymentType;
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
