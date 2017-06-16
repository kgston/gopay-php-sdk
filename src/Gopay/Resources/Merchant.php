<?php

namespace Gopay\Resources;


use Gopay\Requests\RequestContext;
use Gopay\Utility\FunctionalUtils as fp;
use Gopay\Utility\JsonSchema;


class Merchant extends Resource {

    public $verificationDataId;
    public $name;
    public $email;
    public $verified;
    public $configuration;
    public $createdOn;

    public function __construct($id,
                                $verificationDataId,
                                $name,
                                $email,
                                $verified,
                                $configuration,
                                $createdOn,
                                $context = NULL)
    {
        CardConfiguration::getSchema();
        parent::__construct($id, $context);
        $this->verificationDataId = $verificationDataId;
        $this->name = $name;
        $this->email = $email;
        $this->verified = $verified;
        $this->configuration = $configuration;
        $this->createdOn = $createdOn;
    }

    public static function fromJson(array $json, RequestContext $requestContext) {
        return new Merchant(
            $json["id"],
            $json["verification_data_id"],
            $json["name"],
            $json["email"],
            $json["verified"],
            Configuration::fromJson(fp::get_or_else($json, "configuration", array())),
            $json["created_on"],
            $context = $requestContext
        );
    }


}