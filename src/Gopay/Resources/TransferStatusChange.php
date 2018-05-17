<?php

namespace Gopay\Resources;

use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\Json\JsonSchema;
use Gopay\Utility\RequesterUtils;

class TransferStatusChange extends Resource
{
    use Jsonable;
    public $id;
    public $merchantId;
    public $transferId;
    public $oldStatus;
    public $newStatus;
    public $reason;
    public $createdOn;

    public function __construct(
        $id,
        $merchantId,
        $transferId,
        $oldStatus,
        $newStatus,
        $reason,
        $createdOn,
        $context
    ) {
        parent::__construct($id, $context);
        $this->merchantId = $merchantId;
        $this->transferId = $transferId;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->reason = $reason;
        $this->createdOn = date_create($createdOn);
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
