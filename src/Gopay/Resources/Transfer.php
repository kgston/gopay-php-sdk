<?php

namespace Gopay\Resources;

use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\Json\JsonSchema;
use Gopay\Utility\RequesterUtils;

class Transfer extends Resource
{
    use Jsonable;
    public $bankAccountId;
    public $amount;
    public $currency;
    public $amountFormatted;
    public $status;
    public $errorCode;
    public $errorText;
    public $metadata;
    public $note;
    public $from;
    public $to;
    public $createdOn;

    public function __construct(
        $id,
        $bankAccountId,
        $amount,
        $currency,
        $amountFormatted,
        $status,
        $errorCode,
        $errorText,
        $metadata,
        $note,
        $from,
        $to,
        $createdOn,
        $context
    ) {
        parent::__construct($id, $context);
        $this->bankAccountId = $bankAccountId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->amountFormatted = $amountFormatted;
        $this->status = $status;
        $this->errorCode = $errorCode;
        $this->errorText = $errorText;
        $this->metadata = $metadata;
        $this->note = $note;
        $this->from = $from;
        $this->to = $to;
        $this->createdOn = $createdOn;
    }

    public function listLedgers(
        $cursor = null,
        $limit = null,
        $cursorDirection = null
    ) {
        $query = FunctionalUtils::stripNulls(array(
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => $cursorDirection
        ));
        $context = $this->getIdContext()->appendPath("ledgers");
        return RequesterUtils::executeGetPaginated(Ledger::class, $context, $query);
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
