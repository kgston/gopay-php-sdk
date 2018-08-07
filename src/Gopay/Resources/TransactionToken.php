<?php

namespace Gopay\Resources;

use DateTime;
use DateTimeZone;
use Gopay\Enums\AppTokenMode;
use Gopay\Enums\Field;
use Gopay\Enums\PaymentType;
use Gopay\Enums\Period;
use Gopay\Enums\Reason;
use Gopay\Enums\TokenType;
use Gopay\Enums\UsageLimit;
use Gopay\Errors\GopayLogicError;
use Gopay\Errors\GopaySDKError;
use Gopay\Errors\GopayValidationError;
use Gopay\Resources\Mixins\GetTransactionTokens;
use Gopay\Resources\PaymentData\CardData;
use Gopay\Resources\PaymentMethod\PaymentMethodPatch;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\RequesterUtils;
use Gopay\Utility\Json\JsonSchema;
use Money\Money;

class TransactionToken extends Resource
{
    use Jsonable;

    public $storeId;
    public $email;
    public $active;
    public $paymentType;
    public $mode;
    public $type;
    public $usageLimit;
    public $metadata;
    public $createdOn;
    public $lastUsedOn;
    public $data;

    public function __construct(
        $id,
        $storeId,
        $email,
        $active,
        $paymentType,
        $mode,
        $type,
        $usageLimit,
        $metadata,
        $createdOn,
        $lastUsedOn,
        $data,
        $context
    ) {
        parent::__construct($id, $context);
        $this->email = $email;
        $this->active = $active;
        $this->storeId = $storeId;
        $this->paymentType = PaymentType::fromValue($paymentType);
        $this->mode = AppTokenMode::fromValue($mode);
        $this->type = TokenType::fromValue($type);
        $this->usageLimit = UsageLimit::fromValue($usageLimit);
        $this->metadata = $metadata;
        $this->createdOn = date_create($createdOn);
        $this->lastUsedOn = isset($lastUsedOn) ? date_create($lastUsedOn) : null;
        // The payment data may not be available when retrieving from a list. Triggering a ->fetch() will fix this
        $this->data = $data;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('data', false, function ($value, $json) {
                $paymentType = PaymentType::fromValue($json['payment_type']);
                switch ($paymentType) {
                    case PaymentType::CARD():
                        return CardData::getSchema()->parse($value);
                    case PaymentType::QR_SCAN():
                    case PaymentType::KONBINI():
                    case PaymentType::APPLE_PAY():
                        throw new GopaySDKError(Reason::UNSUPPORTED_FEATURE());
                }
            });
    }

    protected function getIdContext()
    {
        return $this->context->withPath(['stores', $this->storeId, 'tokens', $this->id]);
    }

    public function patch(PaymentMethodPatch $paymentPatch)
    {
        return $this->update($paymentPatch)->fetch();
    }

    public function deactivate()
    {
        return RequesterUtils::executeDelete($this->getIdContext());
    }

    public function createCharge(
        Money $money,
        $capture = true,
        DateTime $captureAt = null,
        array $metadata = null
    ) {
        if ($this->type === TokenType::SUBSCRIPTION()) {
            throw new GopayLogicError(Reason::NON_SUBSCRIPTION_PAYMENT());
        }
        $payload = $money->jsonSerialize() + [
            'transaction_token_id' => $this->id,
            'capture' => $capture ? null : false,
            'metadata' => $metadata
        ];

        $context = $this->context->withPath('charges');
        return RequesterUtils::executePost(Charge::class, $context, FunctionalUtils::stripNulls($payload));
    }

    public function createSubscription(
        Money $money,
        Period $period,
        Money $initialAmount = null,
        ScheduleSettings $scheduleSettings = null,
        InstallmentPlan $installmentPlan = null,
        array $metadata = null
    ) {
        if ($this->type !== TokenType::SUBSCRIPTION()) {
            throw new GopayLogicError(Reason::NOT_SUBSCRIPTION_PAYMENT());
        }
        if (!$money->isPositive()) {
            throw new GopayValidationError(Field::AMOUNT(), Reason::INVALID_AMOUNT());
        }
        if (isset($initialAmount) && ($initialAmount->isNegative() || !$initialAmount->isSameCurrency($money))) {
            throw new GopayValidationError(Field::INITIAL_AMOUNT(), Reason::INVALID_AMOUNT());
        }
        if (isset($scheduleSettings) &&
        $scheduleSettings->preserveEndOfMonth === true &&
        Period::MONTHLY() !== $period) {
            throw new GopayValidationError(Field::PRESERVE_END_OF_MONTH(), Reason::MUST_BE_MONTH_BASE_TO_SET());
        }
        
        $payload = $money->jsonSerialize() + [
            'transaction_token_id' => $this->id,
            'period' => $period->getValue(),
            'initial_amount' => isset($initialAmount) ? $initialAmount->getAmount() : null,
            'schedule_settings' => $scheduleSettings,
            'installment_plan' => $installmentPlan,
            'metadata' => $metadata
        ];

        $context = $this->context->withPath('subscriptions');
        return RequesterUtils::executePost(Subscription::class, $context, FunctionalUtils::stripNulls($payload));
    }
}
