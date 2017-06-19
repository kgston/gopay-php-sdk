<?php
/**
 * Created by PhpStorm.
 * User: adamsar
 * Date: 6/16/17
 * Time: 5:50 PM
 */

namespace Gopay\Resources;

use Gopay\Resources\PaymentData\CardData;
use Gopay\Utility\Json\JsonSchema;

class TransactionToken extends Resource {
    use Jsonable;

    private static $cardDataSchema;

    public $storeId;
    public $email;
    public $paymentType;
    public $active;
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
                         $active,
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
        $this->active = $active;
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

}