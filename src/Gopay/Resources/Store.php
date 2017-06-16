<?php

namespace Gopay\Resources;


use Gopay\Requests\RequestContext;
use Gopay\Resources\Configuration\Configuration;
use Gopay\Utility\Json\JsonSchema;

class Store extends Resource
{
    use Jsonable;
    public $name;
    public $createdOn;
    public $configuration;

    public function __construct($id,
                                $name,
                                $createdOn,
                                $configuration,
                                RequestContext $context = NULL)
    {
        parent::__construct($id, $context);
        $this->name = $name;
        $this->createdOn = $createdOn;
        $this->configuration = $configuration;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(Store::class)
            ->upsert("configuration", false, Configuration::getSchema()->getParser());
    }
}