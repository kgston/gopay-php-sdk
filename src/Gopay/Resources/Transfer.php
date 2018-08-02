<?php

namespace Gopay\Resources;

use Gopay\Enums\CursorDirection;
use Gopay\Enums\TransferStatus;
use Gopay\Resources\Mixins\GetLedgers;
use Gopay\Resources\Mixins\GetStatusChanges;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\Json\JsonSchema;
use Gopay\Utility\RequesterUtils;
use Money\Currency;
use Money\Money;

class Transfer extends Resource
{
    use Jsonable;
    use GetLedgers, GetStatusChanges {
        GetLedgers::validate insteadof GetStatusChanges;
    }
    
    public $bankAccountId;
    public $currency;
    public $amount;
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
        $currency,
        $amount,
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
        $this->currency = new Currency($currency);
        $this->amount = new Money($amount, $this->currency);
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

    protected function getLedgerContext()
    {
        return $this->getIdContext()->appendPath('ledgers');
    }

    protected function getStatusChangeContext()
    {
        return $this->getIdContext()->appendPath('status_changes');
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
