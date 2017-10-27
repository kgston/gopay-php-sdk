<?php

namespace Gopay\Resources;

use Gopay\Resources\PaymentData\CardData;
use Gopay\Utility\Json\JsonSchema;
use Gopay\Utility\RequesterUtils;

class TransactionToken extends Resource {
    use Jsonable;

    private static $cardDataSchema;

    public $storeId;
    public $email;
    public $paymentType;
    public $mode;
    public $type;
    public $usageLimit;
    public $createdOn;
    public $lastUsedOn;
    public $data;

    function __construct($id,
                         $storeId,
                         $email,
                         $paymentType,
                         $mode,
                         $type,
                         $usageLimit,
                         $createdOn,
                         $lastUsedOn,
                         $data,
                         $context)
    {
        parent::__construct($id, $context);
        $this->email = $email;
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
        return JsonSchema::fromClass(TransactionToken::class);
    }


    public static function getCardSchema() {
        if (!isset(self::$cardDataSchema)) {
            self::$cardDataSchema = JsonSchema::fromClass(self::class)
                ->upsert("data", true, $formatter = CardData::getSchema()->getParser());
        }
        return self::$cardDataSchema;
    }

    public function createCharge($amount, $currency, $capture = true, $metadata = NULL) {
        $payload = array(
            'transaction_token_id' => $this->id,
            'amount' => $amount,
            'currency' => $currency
        );

        if ($metadata != NULL)  {
            $payload = array_map(array("metadata" => $metadata), $payload);
        }
        if (!$capture) {
            $payload = array_merge($payload, array("capture" => "false"));
        }

        $context = $this->context->withPath("charges");
        return RequesterUtils::execute_post(Charge::class, $context, $payload);
    }

}