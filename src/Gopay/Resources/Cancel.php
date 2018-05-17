<?php

namespace Gopay\Resources;

use Gopay\Utility\Json\JsonSchema;

class Cancel extends Resource
{
    use Jsonable;
    use Pollable;

    public $chargeId;
    public $storeId;
    public $status;
    public $error;
    public $metadata;
    public $mode;
    public $createdOn;

    public function __construct($id, $chargeId, $storeId, $status, $error, $metadata, $mode, $createdOn, $context)
    {
        parent::__construct($id, $context);
        $this->chargeId = $chargeId;
        $this->storeId = $storeId;
        $this->status = $status;
        $this->error = $error;
        $this->metadata = $metadata;
        $this->mode = $mode;
        $this->createdOn = date_create($createdOn);
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(Cancel::class);
    }
    
    protected function getIdContext()
    {
        return $this->context->withPath(
            array("stores", $this->storeId, "charges", $this->chargeId, "cancels", $this->id)
        );
    }
}
