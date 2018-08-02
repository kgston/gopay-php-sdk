<?php

namespace Gopay\Resources;

use DateTimeZone;
use Gopay\Resources\Mixins\GetCharges;
use Gopay\Utility\Json\JsonSchema;
use Money\Currency;
use Money\Money;

class ScheduledPayment extends Resource
{
    use Jsonable;
    use GetCharges {
        listCharges as private fullListCharges;
    }

    public $subscriptionId;
    public $dueDate;
    public $zoneId;
    public $currency;
    public $amount;
    public $amountFormatted;
    public $isPaid;
    public $isLastPayment;
    public $createdOn;
    public $updatedOn;

    public function __construct(
        $id,
        $subscriptionId,
        $dueDate,
        $zoneId,
        $currency,
        $amount,
        $amountFormatted,
        $isPaid,
        $isLastPayment,
        $createdOn,
        $updatedOn,
        $context
    ) {
        parent::__construct($id, $context);
        $this->subscriptionId = $subscriptionId;
        $this->dueDate = date_create($dueDate);
        $this->zoneId = new DateTimeZone($zoneId);
        $this->currency = new Currency($currency);
        $this->amount = new Money($amount, $this->currency);
        $this->amountFormatted = $amountFormatted;
        $this->isPaid = $isPaid;
        $this->isLastPayment = $isLastPayment;
        $this->createdOn = date_create($createdOn);
        $this->updatedOn = date_create($updatedOn);
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }

    protected function getChargeContext()
    {
        return $this->getIdContext()->appendPath('charges');
    }

    public function listCharges(
        $cursor = null,
        $limit = null,
        CursorDirection $cursorDirection = null
    ) {
        return $this->fullListCharges(
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $cursor,
            $limit,
            $cursorDirection
        );
    }
}
