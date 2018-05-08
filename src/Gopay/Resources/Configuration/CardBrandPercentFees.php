<?php

namespace Gopay\Resources\Configuration;

use Gopay\Resources\Jsonable;
use Gopay\Utility\Json\JsonSchema;

class CardBrandPercentFees
{
    use Jsonable;

    public $visa;
    public $americanExpress;
    public $mastercard;
    public $maestro;
    public $discover;
    public $jcb;
    public $dinersClub;
    public $unionPay;

    public function __construct($visa, $americanExpress, $mastercard, $maestro, $discover, $jcb, $dinersClub, $unionPay)
    {
        $this->visa = $visa;
        $this->americanExpress = $americanExpress;
        $this->mastercard = $mastercard;
        $this->maestro = $maestro;
        $this->discover = $discover;
        $this->jcb = $jcb;
        $this->dinersClub = $dinersClub;
        $this->unionPay = $unionPay;
    }


    protected static function initSchema()
    {
        return JsonSchema::fromClass(CardBrandPercentFees::class);
    }
}
