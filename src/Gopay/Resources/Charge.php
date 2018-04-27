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

    public function __construct($id,
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
                                $context)
    {
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
        $this->createdOn = $createdOn;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }

    protected function getIdContext()
    {
        return $this->context->withPath(array("stores", $this->storeId, "charges", $this->id));
    }

    public function createRefund($amount,
                                 $currency,
                                 $reason = NULL,
                                 $message = NULL,
                                 $metadata = NULL) {
        $payload = FunctionalUtils::strip_nulls(array(
            "amount" => $amount,
            "currency" => $currency,
            "reason" => $reason,
            "message" => $message,
            "metadata" => $metadata
            ));
        $context = $this->getIdContext()->appendPath("refunds");
        return RequesterUtils::execute_post(Refund::class, $context, $payload);
    }

    public function listRefunds($cursor=NULL,
                                $limit=NULL,
                                $cursorDirection=NULL)
    {
        $query = FunctionalUtils::strip_nulls(array(
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => $cursorDirection
        ));
        return RequesterUtils::execute_get_paginated(
            Refund::class,
            $this->getIdContext()->appendPath("refunds"),
            $query
        );
    }

    public function capture($amount,
                            $currency) {
        $payload = array(
            'amount' => $amount,
            'currency' => $currency
        );
        $context = $this->getIdContext()->appendPath("capture");
        return RequesterUtils::execute_post(NULL, $context, $payload);
    }

    public function cancel($metadata = NULL){
        $payload = FunctionalUtils::strip_nulls(array(
            "metadata" => $metadata
        ));
        $context = $this->getIdContext()->appendPath("cancels");
        return RequesterUtils::execute_post(Cancel::class, $context, $payload);
    }

    public function listCancels($cursor=NULL,
                                $limit=NULL,
                                $cursorDirection=NULL)
    {
        $query = FunctionalUtils::strip_nulls(array(
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => $cursorDirection
        ));
        return RequesterUtils::execute_get_paginated(
            Cancel::class,
            $this->getIdContext()->appendPath("cancels"),
            $query
        );

    }

}