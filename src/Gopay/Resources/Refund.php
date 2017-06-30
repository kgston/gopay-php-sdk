<?php

namespace Gopay\Resources;


use Gopay\Utility\Json\JsonSchema;

class Refund extends Resource
{

    use Jsonable;
    use Pollable;

    public $chargeId;
    public $status;
    public $amount;
    public $currency;
    public $amountFormatted;
    public $reason;
    public $message;
    public $error;
    public $metadata;

    public function __construct($id, $chargeId, $status, $amount, $currency, $amountFormatted, $reason, $message, $error, $metadata, $context)
    {
        parent::__construct($id, $context);
        $this->chargeId = $chargeId;
        $this->status = $status;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->amountFormatted = $amountFormatted;
        $this->reason = $reason;
        $this->message = $message;
        $this->error = $error;
        $this->metadata = $metadata;
    }


    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class );
    }
}