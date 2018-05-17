<?php

namespace Gopay\Resources;

use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\Json\JsonSchema;
use Gopay\Utility\RequesterUtils;

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
        $context
    ) {
        parent::__construct($id, $context);
        $this->storeId = $storeId;
        $this->transactionTokenId = $transactionTokenId;
        $this->transactionTokenType = $transactionTokenType;
        $this->subscriptionId = $subscriptionId;
        $this->requestedAmount = $requestedAmount;
        $this->requestedCurrency = $requestedCurrency;
        $this->requestedAmountFormatted = $requestedAmountFormatted;
        $this->chargedAmount = $chargedAmount;
        $this->chargedCurrency = $chargedCurrency;
        $this->chargedAmountFormatted = $chargedAmountFormatted;
        $this->captureAt = $captureAt;
        $this->status = $status;
        $this->error = $error;
        $this->metadata = $metadata;
        $this->mode = $mode;
        $this->createdOn = date_create($createdOn);
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }

    protected function getIdContext()
    {
        return $this->context->withPath(array("stores", $this->storeId, "charges", $this->id));
    }

    public function createRefund(
        $amount,
        $currency,
        $reason = null,
        $message = null,
        $metadata = null
    ) {
        $payload = FunctionalUtils::stripNulls(array(
            "amount" => $amount,
            "currency" => $currency,
            "reason" => $reason,
            "message" => $message,
            "metadata" => $metadata
        ));
        $context = $this->getIdContext()->appendPath("refunds");
        return RequesterUtils::executePost(Refund::class, $context, $payload);
    }

    public function listRefunds(
        $cursor = null,
        $limit = null,
        $cursorDirection = null
    ) {
        $query = FunctionalUtils::stripNulls(array(
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => $cursorDirection
        ));
        return RequesterUtils::executeGetPaginated(
            Refund::class,
            $this->getIdContext()->appendPath("refunds"),
            $query
        );
    }

    public function capture(
        $amount,
        $currency
    ) {
        $payload = array(
            'amount' => $amount,
            'currency' => $currency
        );
        $context = $this->getIdContext()->appendPath("capture");
        return RequesterUtils::executePost(null, $context, $payload);
    }

    public function cancel($metadata = null)
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
        $cursorDirection = null
    ) {
        $query = FunctionalUtils::stripNulls(array(
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => $cursorDirection
        ));
        return RequesterUtils::executeGetPaginated(
            Cancel::class,
            $this->getIdContext()->appendPath("cancels"),
            $query
        );
    }
}
