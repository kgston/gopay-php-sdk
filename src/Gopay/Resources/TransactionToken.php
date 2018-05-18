<?php

namespace Gopay\Resources;

use DateTime;
use Gopay\Enums\AppTokenMode;
use Gopay\Enums\Currency;
use Gopay\Enums\PaymentType;
use Gopay\Enums\Period;
use Gopay\Enums\TokenType;
use Gopay\Enums\UsageLimit;
use Gopay\Errors\GopayLogicError;
use Gopay\Errors\GopaySDKError;
use Gopay\Errors\GopayValidationError;
use Gopay\Resources\Mixins\GetTransactionTokens;
use Gopay\Resources\PaymentData\CardData;
use Gopay\Resources\PaymentMethod\PaymentMethodPatch;
use Gopay\Utility\Json\JsonSchema;
use Gopay\Utility\RequesterUtils;

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
        $this->lastUsedOn = date_create($lastUsedOn);
        // The payment data may not be available when retrieving from a list. Triggering a ->fetch() will fix this
        $this->data = $data;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert("data", false, function ($value, $json) {
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
        return $this->context->withPath(array("stores", $this->storeId, "tokens", $this->id));
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
        $amount,
        Currency $currency,
        $capture = true,
        DateTime $captureAt = null,
        array $metadata = null
    ) {
        if ($this->type === TokenType::SUBSCRIPTION()) {
            throw new GopayLogicError(REASON::NON_SUBSCRIPTION_PAYMENT());
        }
        $payload = array(
            'transaction_token_id' => $this->id,
            'amount' => $amount,
            'currency' => $currency->getValue()
        );

        if ($metadata != null) {
            $payload = array_map(array("metadata" => $metadata), $payload);
        }
        if (!$capture) {
            $payload = array_merge($payload, array("capture" => "false"));
        }

        $context = $this->context->withPath("charges");
        return RequesterUtils::executePost(Charge::class, $context, $payload);
    }

    public function createSubscription(
        $amount,
        Currency $currency,
        Period $period,
        $initialAmount = null,
        DateTime $subsequentCyclesStart = null,
        InstallmentPlan $installmentPlan = null,
        array $metadata = null
    ) {
        if ($this->type !== TokenType::SUBSCRIPTION()) {
            throw new GopayLogicError(REASON::NOT_SUBSCRIPTION_PAYMENT());
        }
        if ($amount <= 0) {
            throw new GopayValidationError(Field::AMOUNT(), REASON::INVALID_AMOUNT());
        }
        if ($initialAmount < 0) {
            throw new GopayValidationError(Field::INITIAL_AMOUNT(), REASON::INVALID_AMOUNT());
        }
        $payload = array(
            'transaction_token_id' => $this->id,
            'amount' => $amount,
            'currency' => $currency->getValue(),
            'period' => $period->getValue()
        );
        if ($metadata != null) {
            $payload = array_merge(array("metadata" => $metadata), $payload);
        }
        if ($initialAmount != null) {
            $payload = array_merge($payload, array("initial_amount" => $initialAmount));
        }
        if ($subsequentCyclesStart != null) {
            if ($subsequentCyclesStart < date_create()) {
                throw new GopayValidationError(Field::SUBSEQUENT_CYCLES_START(), REASON::INCOHERENT_DATE_RANGE());
            }
            $payload = array_merge(
                $payload,
                array("subsequent_cycles_start" => $subsequentCyclesStart->format(DateTime::ATOM))
            );
        }
        if ($installmentPlan != null) {
            $payload = array_merge(
                $payload,
                array("installment_plan" => $installmentPlan)
            );
        }

        $context = $this->context->withPath("subscriptions");
        return RequesterUtils::executePost(Subscription::class, $context, $payload);
    }
}
