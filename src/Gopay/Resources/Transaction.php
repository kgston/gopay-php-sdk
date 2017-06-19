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
    public $store_id;
    public $charge_id;
    public $amount;
    public $currency;
    public $amountFormatted;
    public $type;
    public $status;
    public $createdOn;

    public function __construct($id, $store_id, $charge_id, $amount, $currency, $amountFormatted, $type, $status, $createdOn)
    {
        $this->id = $id;
        $this->store_id = $store_id;
        $this->charge_id = $charge_id;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->amountFormatted = $amountFormatted;
        $this->type = $type;
        $this->status = $status;
        $this->createdOn = $createdOn;
    }


    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}