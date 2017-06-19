<?php

namespace Gopay\Resources;


use Gopay\Utility\Json\JsonSchema;

class Charge extends Resource
{
    use Jsonable;

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
}