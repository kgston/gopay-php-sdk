<?php

namespace Gopay\Resources;

use Gopay\Enums\AppTokenMode;
use Gopay\Enums\CancelStatus;
use Gopay\Utility\Json\JsonSchema;

class SimpleList
{
    public $items;
    private $jsonableClass;
    private $context;

    public function __construct($items, $jsonableClass, $context)
    {
        $this->items = $items;
        $this->jsonableClass = $jsonableClass;
        $this->context = $context;
    }

    public static function fromResponse(
        $response,
        $jsonableClass,
        $context
    ) {
        $parser = $jsonableClass::getContextParser($context);
        return new SimpleList(
            array_map($parser, $response),
            $jsonableClass,
            $context
        );
    }
}
