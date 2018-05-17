<?php

namespace Gopay\Resources;

use Composer\DependencyResolver\Request;
use Gopay\Requests\RequestContext;
use Gopay\Resources\Configuration\Configuration;
use Gopay\Resources\Mixins\GetCharges;
use Gopay\Resources\Mixins\GetSubscriptions;
use Gopay\Resources\Mixins\GetTransactions;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\Json\JsonSchema;
use Gopay\Utility\RequesterUtils;

class Store extends Resource
{
    use Jsonable;
    use GetSubscriptions;
    use GetTransactions;
    use GetCharges;

    public $name;
    public $createdOn;
    public $configuration;


    public function __construct(
        $id,
        $name,
        $createdOn,
        $configuration,
        RequestContext $context = null
    ) {
        parent::__construct($id, $context);
        $this->name = $name;
        $this->createdOn = date_create($createdOn);
        $this->configuration = $configuration;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(Store::class)
            ->upsert("configuration", false, Configuration::getSchema()->getParser());
    }

    public function getCharge($chargeId)
    {
        $context = $this->getIdContext()->appendPath(array("charges", $chargeId));
        return RequesterUtils::executeGet(Charge::class, $context);
    }

    public function getSubscription($subscriptionId)
    {
        $context = $this->getIdContext()->appendPath(array("subscriptions", $subscriptionId));
        return RequesterUtils::executeGet(Subscription::class, $context);
    }

    protected function getSubscriptionContext()
    {
        return $this->getIdContext()->appendPath("subscriptions");
    }

    protected function getTransactionContext()
    {
        return $this->getIdContext()->appendPath("transaction_history");
    }

    protected function getChargeContext()
    {
        return $this->getIdContext()->appendPath("charges");
    }
}
