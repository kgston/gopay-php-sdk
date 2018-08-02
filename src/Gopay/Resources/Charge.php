<?php

namespace Gopay\Resources;

use Gopay\Enums\AppTokenMode;
use Gopay\Enums\ChargeStatus;
use Gopay\Enums\Field;
use Gopay\Enums\Reason;
use Gopay\Enums\RefundReason;
use Gopay\Enums\TokenType;
use Gopay\Errors\GopayValidationError;
use Gopay\Resources\Mixins\GetCancels;
use Gopay\Resources\Mixins\GetRefunds;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\RequesterUtils;
use Gopay\Utility\Json\JsonSchema;
use Money\Currency;
use Money\Money;

class Charge extends Resource
{
    use Jsonable;
    use Pollable;
    use GetCancels, GetRefunds {
        GetCancels::validate insteadof GetRefunds;
    }

    public $storeId;
    public $transactionTokenId;
    public $transactionTokenType;
    public $subscriptionId;
    public $requestedCurrency;
    public $requestedAmount;
    public $requestedAmountFormatted;
    public $chargedCurrency;
    public $chargedAmount;
    public $chargedAmountFormatted;
    public $captureAt;
    public $status;
    public $error;
    public $metadata;
    public $mode;
    public $createdOn;
    public $updatedOn;

    public function __construct(
        $id,
        $storeId,
        $transactionTokenId,
        $transactionTokenType,
        $subscriptionId,
        $requestedCurrency,
        $requestedAmount,
        $requestedAmountFormatted,
        $chargedCurrency,
        $chargedAmount,
        $chargedAmountFormatted,
        $captureAt,
        $status,
        $error,
        $metadata,
        $mode,
        $createdOn,
        $updatedOn,
        $context
    ) {
        parent::__construct($id, $context);
        $this->storeId = $storeId;
        $this->transactionTokenId = $transactionTokenId;
        $this->transactionTokenType = TokenType::fromValue($transactionTokenType);
        $this->subscriptionId = $subscriptionId;
        $this->requestedCurrency = new Currency($requestedCurrency);
        $this->requestedAmount = new Money($requestedAmount, $this->requestedCurrency);
        $this->requestedAmountFormatted = $requestedAmountFormatted;
        $this->chargedCurrency = isset($chargedCurrency) ? new Currency($chargedCurrency) : null;
        $this->chargedAmount = isset($chargedAmount) ? new Money($chargedAmount, $this->chargedCurrency) : null;
        $this->chargedAmountFormatted = $chargedAmountFormatted;
        $this->captureAt = isset($captureAt) ? date_create($captureAt) : null;
        $this->status = ChargeStatus::fromValue($status);
        $this->error = $error;
        $this->metadata = $metadata;
        $this->mode = AppTokenMode::fromValue($mode);
        $this->createdOn = date_create($createdOn);
        $this->updatedOn = date_create($updatedOn);
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }

    protected function getIdContext()
    {
        return $this->context->withPath(['stores', $this->storeId, 'charges', $this->id]);
    }

    public function patch(array $metadata)
    {
        return RequesterUtils::executePatch(self::class, $this->getIdContext(), ['metadata' => $metadata]);
    }

    public function createRefund(
        Money $money,
        RefundReason $reason = null,
        $message = null,
        array $metadata = null
    ) {
        if (isset($reason) && RefundReason::CHARGEBACK() === $reason) {
            throw new GopayValidationError(Field::REASON(), Reason::INVALID_PERMISSIONS());
        }
        $payload = FunctionalUtils::stripNulls(
            $money->jsonSerialize() +
            [
                'reason' => isset($reason) ? $reason->getValue() : null,
                'message' => $message,
                'metadata' => $metadata
            ]
        );
        $context = $this->getIdContext()->appendPath('refunds');
        return RequesterUtils::executePost(Refund::class, $context, $payload);
    }

    public function capture(Money $money = null)
    {
        $context = $this->getIdContext()->appendPath('capture');
        return RequesterUtils::executePost(null, $context, $money);
    }

    public function cancel(array $metadata = null)
    {
        $payload = FunctionalUtils::stripNulls([
            'metadata' => $metadata
        ]);
        $context = $this->getIdContext()->appendPath('cancels');
        return RequesterUtils::executePost(Cancel::class, $context, $payload);
    }

    protected function getCancelContext()
    {
        return $this->context->withPath(['stores', $this->storeId, 'charges', $this->id, 'cancels']);
    }

    protected function getRefundContext()
    {
        return $this->context->withPath(['stores', $this->storeId, 'charges', $this->id, 'refunds']);
    }
}
