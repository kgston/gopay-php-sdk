<?php
/**
 * Created by PhpStorm.
 * User: adamsar
 * Date: 6/19/17
 * Time: 5:00 PM
 */

namespace Gopay\Resources;


use Gopay\Utility\Json\JsonSchema;

class Subscription extends Resource
{
    use Jsonable;

    public $storeId;
    public $transactionTokenId;
    public $amount;
    public $currency;
    public $amountFormatted;
    public $period;
    public $status;
    public $metadata;
    public $mode;
    public $createdOn;

    public function __construct($id, $storeId, $transactionTokenId, $amount, $currency, $amountFormatted, $period, $status, $metadata, $mode, $createdOn, $context)
    {
        parent::__construct($id, $context);
        $this->storeId = $storeId;
        $this->transactionTokenId = $transactionTokenId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->amountFormatted = $amountFormatted;
        $this->period = $period;
        $this->status = $status;
        $this->metadata = $metadata;
        $this->mode = $mode;
        $this->createdOn = $createdOn;
    }

    protected function getIdContext()
    {
        return $this->context->withPath(array("stores", $this->storeId, "subscriptions", $this->id));
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }

}