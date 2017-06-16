<?php
/**
 * Created by PhpStorm.
 * User: adamsar
 * Date: 6/16/17
 * Time: 1:58 PM
 */

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

    public static function fromJson($json)
    {
        return new QRConfiguration(
            fp::get_or_null($json, "enabled"),
            fp::get_or_null($json, "forbidden_qr_scan_gateway")
        );
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(QRConfiguration::class);
    }
}