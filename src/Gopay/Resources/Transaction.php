<?php
/**
 * Created by PhpStorm.
 * User: adamsar
 * Date: 6/19/17
 * Time: 4:50 PM
 */

namespace Gopay\Resources;


use Gopay\Utility\Json\JsonSchema;

class Transaction
{
    use Jsonable;

    public $id;
    public $storeId;
    public $resourceId;
    public $chargeId;
    public $amount;
    public $currency;
    public $amountFormatted;
    public $type;
    public $status;
    public $metadata;
    public $mode;
    public $createdOn;
    private $context;

    public function __construct($id, $storeId, $resourceId, $chargeId, $amount, $currency, $amountFormatted, $type, $status, $metadata, $mode, $createdOn, $context)
    {
        $this->id = $id;
        $this->storeId = $storeId;
        $this->resourceId = $resourceId;
        $this->chargeId = $chargeId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->amountFormatted = $amountFormatted;
        $this->type = $type;
        $this->status = $status;
        $this->metadata = $metadata;
        $this->mode = $mode;
        $this->createdOn = $createdOn;
        $this->context = $context;
    }


    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}