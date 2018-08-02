<?php

namespace Gopay\Resources;

use Gopay\Enums\AppTokenMode;
use Gopay\Enums\InstallmentPlanType;
use Gopay\Enums\Period;
use Gopay\Enums\Reason;
use Gopay\Enums\SubscriptionStatus;
use Gopay\Errors\GopayLogicError;
use Gopay\Resources\Mixins\GetCharges;
use Gopay\Resources\Mixins\GetScheduledPayments;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\RequesterUtils;
use Gopay\Utility\Json\JsonSchema;
use Money\Currency;
use Money\Money;

class Subscription extends Resource
{
    use Jsonable;
    use Pollable;
    use GetCharges, GetScheduledPayments {
        GetCharges::validate insteadof GetScheduledPayments;
    }

    public $storeId;
    public $transactionTokenId;
    public $currency;
    public $amount;
    public $amountFormatted;
    public $period;
    public $initialAmount;
    public $initialAmountFormatted;
    public $scheduleSettings;
    public $paymentsLeft;
    public $amountLeft;
    public $amountLeftFormatted;
    public $status;
    public $metadata;
    public $mode;
    public $createdOn;
    public $updatedOn;
    public $nextPayment;
    public $installmentPlan;

    public function __construct(
        $id,
        $storeId,
        $transactionTokenId,
        $currency,
        $amount,
        $amountFormatted,
        $period,
        $initialAmount,
        $initialAmountFormatted,
        ScheduleSettings $scheduleSettings,
        $paymentsLeft,
        $amountLeft,
        $amountLeftFormatted,
        $status,
        $metadata,
        $mode,
        $createdOn,
        $updatedOn,
        ScheduledPayment $nextPayment = null,
        InstallmentPlan $installmentPlan = null,
        $context = null
    ) {
        parent::__construct($id, $context);
        $this->storeId = $storeId;
        $this->transactionTokenId = $transactionTokenId;
        $this->currency = new Currency($currency);
        $this->amount = new Money($amount, $this->currency);
        $this->amountFormatted = $amountFormatted;
        $this->period = Period::fromValue($period);
        $this->initialAmount = isset($initialAmount) ? new Money($initialAmount, $this->currency) : null;
        $this->initialAmountFormatted = $initialAmountFormatted;
        $this->scheduleSettings = $scheduleSettings;
        $this->nextPayment = $nextPayment;
        $this->paymentsLeft = $paymentsLeft;
        $this->amountLeft = isset($amountLeft) ? new Money($amountLeft, $this->currency) : null;
        $this->amountLeftFormatted = $amountLeftFormatted;
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
        return $this->context->withPath(['stores', $this->storeId, 'subscriptions', $this->id]);
    }

    protected function getChargeContext()
    {
        return $this->context->withPath(['stores', $this->storeId, 'subscriptions', $this->id, 'charges']);
    }

    protected function getScheduledPaymentContext()
    {
        return $this->context->withPath(['stores', $this->storeId, 'subscriptions', $this->id, 'payments']);
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('schedule_settings', true, ScheduleSettings::getSchema()->getParser())
            ->upsert('next_payment', false, ScheduledPayment::getSchema()->getParser())
            ->upsert('installment_plan', false, InstallmentPlan::getSchema()->getParser());
    }
}
