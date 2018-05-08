<?php

namespace Gopay\Resources\Configuration;

use Gopay\Resources\Jsonable;
use Gopay\Utility\Json\JsonSchema;

class UserTransactionsConfiguration
{
    use Jsonable;
    
    public $enabled;
    public $notifyCustomer;
    
    public function __construct($enabled, $notifyCustomer)
    {
        $this->enabled = $enabled;
        $this->notifyCustomer = $notifyCustomer;
    }
    
    protected static function initSchema()
    {
        return JsonSchema::fromClass(UserTransactionsConfiguration::class);
    }
}
