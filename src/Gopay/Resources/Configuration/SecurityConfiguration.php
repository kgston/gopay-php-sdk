<?php
/**
 * Created by PhpStorm.
 * User: adamsar
 * Date: 6/16/17
 * Time: 1:58 PM
 */

namespace Gopay\Resources\Configuration;

use Gopay\Resources\Jsonable;
use Gopay\Utility\Json\JsonSchema;

class SecurityConfiguration
{

    use Jsonable;

    public $inspectSuspiciousLoginAfter;

    public function __construct($inspectSuspiciousLoginAfter)
    {
        $this->inspectSuspiciousLoginAfter = $inspectSuspiciousLoginAfter;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(SecurityConfiguration::class);
    }
}