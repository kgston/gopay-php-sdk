<?php

namespace Gopay\Resources;


use Gopay\Requests\RequestContext;
use function Gopay\Utility\get_or_else;

class Store extends Resource implements Jsonable
{
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

    public static function fromJson(array $json, RequestContext $requestContext)
    {
        return new Store(
            $json["id"],
            $json["name"],
            $json["createdOn"],
            Configuration::fromJson(get_or_else($json, "configuration", array())),
            $requestContext
        );
    }
}