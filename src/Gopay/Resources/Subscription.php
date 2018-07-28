<?php

namespace Gopay\Resources;

use Gopay\Enums\AppTokenMode;
use Gopay\Enums\InstallmentPlanType;
use Gopay\Enums\Period;
use Gopay\Enums\Reason;
use Gopay\Enums\SubscriptionStatus;
use Gopay\Errors\GopayLogicError;
use Gopay\Resources\Mixins\GetCharges;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\RequesterUtils;
use Gopay\Utility\Json\JsonSchema;
use Money\Currency;
use Money\Money;

class Subscription extends Resource
{
    use Jsonable;
    use Pollable;
    use GetCharges;

    public $storeId;
    public $transactionTokenId;
    public $amount;
    public $amountFormatted;
    public $currency;
    public $period;
    public $initialAmount;
    public $initialAmountFormatted;
    public $subsequentCyclesStart;
    public $status;
    public $metadata;
    public $mode;
    public $createdOn;
    public $updatedOn;

    public function __construct(
        $id,
        $storeId,
        $transactionTokenId,
        $amount,
        $amountFormatted,
        $currency,
        $period,
        $initialAmount,
        $initialAmountFormatted,
        $subsequentCyclesStart,
        $status,
        $metadata,
        $mode,
        $createdOn,
        $updatedOn,
        $installmentPlan,
        $context
    ) {
        parent::__construct($id, $context);
        $this->storeId = $storeId;
        $this->transactionTokenId = $transactionTokenId;
        $this->amount = $amount;
        $this->amountFormatted = $amountFormatted;
        $this->currency = new Currency($currency);
        $this->period = Period::fromValue($period);
        $this->initialAmount = $initialAmount;
        $this->initialAmountFormatted = $initialAmountFormatted;
        $this->subsequentCyclesStart = date_create($subsequentCyclesStart);
        $this->status = SubscriptionStatus::fromValue($status);
        $this->metadata = $metadata;
        $this->mode = AppTokenMode::fromValue($mode);
        $this->createdOn = date_create($createdOn);
        $this->updatedOn = date_create($updatedOn);
        $this->installmentPlan = $installmentPlan;
    }

    public function patch(
        $transactionTokenId = null,
        Money $money = null,
        Money $initialAmount = null,
        array $metadata = null,
        InstallmentPlan $installmentPlan = null
    ) {
        if ($this->isTerminal()) {
            throw new GopayLogicError(Reason::SUBSCRIPTION_ALREADY_ENDED());
        }
        if ($this->isProcessing()) {
            throw new GopayLogicError(Reason::SUBSCRIPTION_PROCESSING());
        }
        if (!isset($this->installmentPlan) && $installmentPlan->planType === InstallmentPlanType::NONE()) {
            throw new GopayLogicError(Reason::INSTALLMENT_PLAN_NOT_FOUND());
        }

        $payload = [
            'transaction_token_id' => $transactionTokenId,
            'initial_amount' => isset($initialAmount) ? $initialAmount->getAmount() : null,
            'metadata' => $metadata,
            'installment_plan' => $installmentPlan
        ];
        if (isset($money)) {
            $payload += $money->jsonSerialize();
        }
        return $this->update(FunctionalUtils::stripNulls($payload));
    }

    public function cancel()
    {
        if ($this->isTerminal()) {
            throw new GopayLogicError(Reason::SUBSCRIPTION_ALREADY_ENDED());
        }
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
        return $this->context->withPath(array('stores', $this->storeId, 'subscriptions', $this->id));
    }

    protected function getChargeContext()
    {
        return $this->context->withPath(array('stores', $this->storeId, 'subscriptions', $this->id, 'charges'));
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('installment_plan', false, InstallmentPlan::getSchema()->getParser());
    }
}
