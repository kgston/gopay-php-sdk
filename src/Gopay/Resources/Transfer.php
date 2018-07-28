<?php

namespace Gopay\Resources;

use Gopay\Enums\CursorDirection;
use Gopay\Enums\TransferStatus;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\Json\JsonSchema;
use Gopay\Utility\RequesterUtils;
use Money\Currency;

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
        $this->currency = new Currency($currency);
        $this->amountFormatted = $amountFormatted;
        $this->status = TransferStatus::fromValue($status);
        $this->errorCode = $errorCode;
        $this->errorText = $errorText;
        $this->metadata = $metadata;
        $this->note = $note;
        $this->from = date_create($from);
        $this->to = date_create($to);
        $this->createdOn = date_create($createdOn);
    }

    public function listLedgers(
        $cursor = null,
        $limit = null,
        CursorDirection $cursorDirection = null
    ) {
        $query = FunctionalUtils::stripNulls([
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => isset($cursorDirection) ? $cursorDirection.getValue() : null
        ]);
        $context = $this->getIdContext()->appendPath("ledgers");
        return RequesterUtils::executeGetPaginated(Ledger::class, $context, $query);
    }

    public function listStatusChanges()
    {
        $context = $this->getIdContext()->appendPath("status_changes");
        return RequesterUtils::executeGetPaginated(TransferStatusChange::class, $context, $query);
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
