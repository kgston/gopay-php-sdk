<?php

namespace Gopay\Resources\Configuration;

use \Gopay\Resources\Jsonable;
use Gopay\Utility\Json\JsonSchema;

class LimitChargeByCardConfiguration {
    
    use Jsonable;
    
    public $quantityOfCharges;
    public $durationWindow;
    
    function __construct($quantityOfCharges, $durationWindow) {
        $this->quantityOfCharges = $quantityOfCharges;
        $this->durationWindow = $durationWindow;
    }
    
    protected static function initSchema() {
        return JsonSchema::fromClass(LimitChargeByCardConfiguration::class);
    }


}
