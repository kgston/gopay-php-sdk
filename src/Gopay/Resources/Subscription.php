<?php

namespace Gopay\Resources;

use Gopay\Enums\AppTokenMode;
use Gopay\Enums\Field;
use Gopay\Enums\InstallmentPlanType;
use Gopay\Enums\Period;
use Gopay\Enums\Reason;
use Gopay\Enums\SubscriptionStatus;
use Gopay\Errors\GopayLogicError;
use Gopay\Errors\GopayValidationError;
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
        $this->installmentPlan = $installmentPlan;
    }

    public function patch(
        $transactionTokenId = null,
        Money $initialAmount = null,
        Period $period = null,
        ScheduleSettings $scheduleSettings = null,
        SubscriptionStatus $status = null,
        array $metadata = null,
        InstallmentPlan $installmentPlan = null
    ) {
        if (SubscriptionStatus::CANCELED() == $this->status) {
            throw new GopayLogicError(Reason::CANNOT_CHANGE_CANCELED_SUBSCRIPTION());
        }
        if (isset($transactionTokenId) && !$this->isTokenPatchable()) {
            throw new GopayLogicError(Reason::CANNOT_CHANGE_TOKEN());
        }
        if (isset($initialAmount) && !$this->isEditable() && $initialAmount->isNegative()) {
            throw new GopayValidationError(Field::INITIAL_AMOUNT(), Reason::INVALID_FORMAT());
        }
        if (isset($period) && !$this->isEditable()) {
            throw new GopayLogicError(Reason::CANNOT_SET_AFTER_SUBSCRIPTION_STARTED());
        }
        if (isset($status) &&
        SubscriptionStatus::UNPAID() != $status &&
        !SubscriptionStatus::SUSPENDED() != $this->status) {
            throw new GopayValidationError(Field::STATUS(), Reason::FORBIDDEN_PARAMETER());
        }
        if (isset($installmentPlan) && !$this->isEditable()) {
            throw new GopayLogicError(Reason::INSTALLMENT_ALREADY_SET());
        }

        $payload = [
            'transaction_token_id' => $transactionTokenId,
            'initial_amount' => isset($initialAmount) ? $initialAmount->getAmount() : null,
            'period' => isset($period) ? $period->getValue() : null,
            'schedule_settings' => $scheduleSettings,
            'status' => isset($status) ? $status->getValue() : null,
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
        switch ($this->status) {
            case SubscriptionStatus::UNVERIFIED():
            case SubscriptionStatus::UNCONFIRMED():
                return true;
            default:
                return false;
        }
    }
    
    public function isProcessing()
    {
        switch ($this->status) {
            case SubscriptionStatus::UNPAID():
            case SubscriptionStatus::CURRENT():
            case SubscriptionStatus::SUSPENDED():
                return true;
            default:
                return false;
        }
    }

    public function isTokenPatchable()
    {
        switch ($this->status) {
            case SubscriptionStatus::UNCONFIRMED():
            case SubscriptionStatus::UNPAID():
            case SubscriptionStatus::CURRENT():
            case SubscriptionStatus::SUSPENDED():
                return true;
            default:
                return false;
        }
    }

    public function isTerminal()
    {
        switch ($this->status) {
            case SubscriptionStatus::CANCELED():
            case SubscriptionStatus::COMPLETED():
                return true;
            default:
                return false;
        }
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
