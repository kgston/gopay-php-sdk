<?php

namespace Gopay\Resources;


use Gopay\Requests\RequestContext;
use Gopay\Utility\FunctionalUtils as fp;

class Store extends Resource
{
    use Jsonable;
    public $name;
    public $createdOn;
    public $configuration;

    public function __construct($id,
                                $name,
                                $createdOn,
                                Configuration $configuration,
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
            ->upsert("configuration", $formatter = Configuration::getSchema()->getParser());
    }
}