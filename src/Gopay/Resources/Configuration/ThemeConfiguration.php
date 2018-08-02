<?php

namespace Gopay\Resources\Configuration;

use Gopay\Resources\Jsonable;
use Gopay\Utility\FunctionalUtils as fp;
use Gopay\Utility\Json\JsonSchema;

class ThemeConfiguration
{
    use Jsonable;
    public $colors;

    public function __construct($colors)
    {
        $this->colors = $colors;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('colors', true, ColorsConfiguration::getSchema()->getparser());
    }
}
