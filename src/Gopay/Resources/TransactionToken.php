<?php

namespace Gopay\Resources;

use Gopay\Enums\TokenType;
use Gopay\Resources\PaymentData\CardData;
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
        $createdOn,
        $lastUsedOn,
        $data,
        $context
    ) {
        if (!$type instanceof TokenType) {
            $type = TokenType::fromValue($type);
        }
        
        parent::__construct($id, $context);
        $this->email = $email;
        $this->active = $active;
        $this->storeId = $storeId;
        $this->paymentType = $paymentType;
        $this->mode = $mode;
        $this->type = $type;
        $this->usageLimit = $usageLimit;
        $this->createdOn = $createdOn;
        $this->lastUsedOn = $lastUsedOn;
        $this->data = $data;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert("data", true, $formatter = CardData::getSchema()->getParser());
    }

    public function createCharge($amount, $currency, $capture = true, $metadata = null)
    {
        $payload = array(
            'transaction_token_id' => $this->id,
            'amount' => $amount,
            'currency' => $currency
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
}
