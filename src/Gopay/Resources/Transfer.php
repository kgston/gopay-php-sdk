<?php
/**
 * Created by PhpStorm.
 * User: adamsar
 * Date: 6/19/17
 * Time: 6:50 PM
 */

namespace Gopay\Resources;


use Gopay\Utility\Json\JsonSchema;

class Transfer extends Resource
{
    use Jsonable;
    public $bankAccountId;
    public $amount;
    public $currency;
    public $amountFormatted;
    public $status;
    public $metadata;
    public $startedBy;
    public $from;
    public $to;

    public function __construct($id, $bankAccountId, $amount, $currency, $amountFormatted, $status, $metadata, $startedBy, $from, $to, $context)
    {
        parent::__construct($id, $context);
        $this->bankAccountId = $bankAccountId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->amountFormatted = $amountFormatted;
        $this->status = $status;
        $this->metadata = $metadata;
        $this->startedBy = $startedBy;
        $this->from = $from;
        $this->to = $to;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}