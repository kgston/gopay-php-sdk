<?php

namespace Gopay\Resources;


use Gopay\Requests\RequestContext;
use Gopay\Resources\Configuration\Configuration;
use Gopay\Utility\Json\JsonSchema;
use Gopay\Utility\RequesterUtils;

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

    public function listCharges($lastFour=NULL,
                                $name=NULL,
                                $expMonth=NULL,
                                $expYear=NULL,
                                $cardNumber=NULL,
                                $from=NULL,
                                $to=NULL,
                                $email=NULL,
                                $phone=NULL,
                                $amountFrom=NULL,
                                $amountTo=NULL,
                                $currency=NULL,
                                $mode=NULL,
                                $cursor=NULL,
                                $limit=NULL,
                                $cursorDirection=NULL)
    {
        $context = $this->getIdContext()->appendPath("charges");
        $query = array(
            "last_four" => $lastFour,
            "name" => $name,
            "exp_month" => $expMonth,
            "exp_year" => $expYear,
            "card_number" => $cardNumber,
            "from" => $from,
            "to" => $to,
            "email" => $email,
            "phone" => $phone,
            "amount_from" => $amountFrom,
            "amount_to" => $amountTo,
            "currency" => $currency,
            "mode" => $mode,
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => $cursorDirection
        );
        return RequesterUtils::execute_get_paginated(Charge::class, $context, $query);
    }
}