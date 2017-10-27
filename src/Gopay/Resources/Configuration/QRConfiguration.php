<?php

namespace Gopay\Resources\Configuration;

use Gopay\Resources\Jsonable;
use Gopay\Utility\FunctionalUtils as fp;
use Gopay\Utility\Json\JsonSchema;

class QRConfiguration
{
    use Jsonable;
    public $enabled;
    public $forbiddenQrScanGateway;

    public function __construct($enabled, $forbiddenQrScanGateway)
    {
        $this->enabled = $enabled;
        $this->forbiddenQrScanGateway = $forbiddenQrScanGateway;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(QRConfiguration::class);
    }
}