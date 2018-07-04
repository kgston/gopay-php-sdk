<?php

namespace Gopay\Resources;

use Gopay\Enums\AppTokenMode;
use Gopay\Enums\ChargeStatus;
use Gopay\Enums\Field;
use Gopay\Enums\Reason;
use Gopay\Enums\RefundReason;
use Gopay\Enums\TokenType;
use Gopay\Errors\GopayValidationError;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\RequesterUtils;
use Gopay\Utility\Json\JsonSchema;
use Money\Currency;
use Money\Money;

class Charge extends Resource
{
    use Jsonable;
    use Pollable;

    public $storeId;
    public $transactionTokenId;
    public $transactionTokenType;
    public $subscriptionId;
    public $requestedAmount;
    public $requestedCurrency;
    public $requestedAmountFormatted;
    public $chargedAmount;
    public $chargedCurrency;
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
        $requestedAmount,
        $requestedCurrency,
        $requestedAmountFormatted,
        $chargedAmount,
        $chargedCurrency,
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
        $this->requestedAmount = $requestedAmount;
        $this->requestedCurrency = new Currency($requestedCurrency);
        $this->requestedAmountFormatted = $requestedAmountFormatted;
        $this->chargedAmount = $chargedAmount;
        $this->chargedCurrency = isset($chargedCurrency) ? new Currency($chargedCurrency) : null;
        $this->chargedAmountFormatted = $chargedAmountFormatted;
        $this->captureAt = date_create($captureAt);
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
        return $this->context->withPath(array("stores", $this->storeId, "charges", $this->id));
    }

    public function patch(array $metadata)
    {
        return RequesterUtils::executePatch(self::class, $this->getIdContext(), array('metadata' => $metadata));
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
            array(
                "reason" => isset($reason) ? $reason->getValue() : null,
                "message" => $message,
                "metadata" => $metadata
            )
        );
        $context = $this->getIdContext()->appendPath("refunds");
        return RequesterUtils::executePost(Refund::class, $context, $payload);
    }

    public function listRefunds(
        $cursor = null,
        $limit = null,
        CursorDirection $cursorDirection = null
    ) {
        $query = FunctionalUtils::stripNulls(array(
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => $cursorDirection == null ? $cursorDirection : $cursorDirection->getValue()
        ));
        return RequesterUtils::executeGetPaginated(
            Refund::class,
            $this->getIdContext()->appendPath("refunds"),
            $query
        );
    }

    public function capture(Money $money = null)
    {
        $context = $this->getIdContext()->appendPath("capture");
        return RequesterUtils::executePost(null, $context, $money);
    }

    public function cancel(array $metadata = null)
    {
        $payload = FunctionalUtils::stripNulls(array(
            "metadata" => $metadata
        ));
        $context = $this->getIdContext()->appendPath("cancels");
        return RequesterUtils::executePost(Cancel::class, $context, $payload);
    }

    public function listCancels(
        $cursor = null,
        $limit = null,
        CursorDirection $cursorDirection = null
    ) {
        $query = FunctionalUtils::stripNulls(array(
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => $cursorDirection == null ? $cursorDirection : $cursorDirection->getValue()
        ));
        return RequesterUtils::executeGetPaginated(
            Cancel::class,
            $this->getIdContext()->appendPath("cancels"),
            $query
        );
    }
}
