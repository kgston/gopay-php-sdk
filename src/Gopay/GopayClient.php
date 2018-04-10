<?php

namespace Gopay;

use Composer\DependencyResolver\Request;
use Exception;
use Gopay\Errors\GopayInvalidWebhookData;
use Gopay\Errors\GopayUnknownWebhookEvent;
use Gopay\Requests\HttpRequester;
use Gopay\Requests\RequestContext;
use Gopay\Requests\Requester;
use Gopay\Resources\AppJWT;
use Gopay\Resources\BankAccount;
use Gopay\Resources\CardConfiguration;
use Gopay\Resources\Charge;
use Gopay\Resources\Merchant;
use Gopay\Resources\Mixins\GetCharges;
use Gopay\Resources\Mixins\GetSubscriptions;
use Gopay\Resources\Mixins\GetTransactions;
use Gopay\Resources\Refund;
use Gopay\Resources\Store;
use Gopay\Resources\Subscription;
use Gopay\Resources\Transaction;
use Gopay\Resources\TransactionToken;
use Gopay\Resources\Transfer;
use Gopay\Resources\WebhookPayload;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\HttpUtils;
use Gopay\Utility\RequesterUtils;

class GopayClient {
    use GetSubscriptions;
    use GetTransactions;
    use GetCharges;

    private $endpoint;
    private $storeAppJWT;
    private $merchantAppJWT;
    private $requester;

    function __construct(AppJWT $storeAppJWT = NULL,
                         AppJWT $merchantAppJWT = NULL,
                         $endpoint = "https://api.gopay.jp"){
        $this->endpoint = $endpoint;
        $this->storeAppJWT = $storeAppJWT;
        $this->merchantAppJWT = $merchantAppJWT;
        $this->requester = new HttpRequester();

    }

    public function getStoreBasedContext() {
        return new RequestContext($this->requester, $this->endpoint, "/", $this->storeAppJWT);
    }

    public function getMerchantBasedContext() {
        return new RequestContext($this->requester, $this->endpoint, "/", $this->merchantAppJWT);
    }

    public function withRequester(Requester $requester) {
        $this->requester = $requester;
        return $this;
    }

    public function getMe() {
        return RequesterUtils::execute_get(
            Merchant::class,
            $this->getStoreBasedContext()->withPath("me")
        );
    }

    public function listStores($cursor = NULL,
                               $limit = NULL,
                               $cursorDirection = NULL) {
        $query = FunctionalUtils::strip_nulls(array(
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => $cursorDirection
        ));
        return RequesterUtils::execute_get_paginated(
            Store::class,
            $this->getStoreBasedContext()->withPath("stores"),
            $query
        );
    }

    public function getStore($id) {
        $context = $this->getStoreBasedContext()->withPath("stores/" . $id);
        return RequesterUtils::execute_get(Store::class, $context);
    }

    public function listBankAccounts($cursor=NULL,
                                     $limit=NULL,
                                     $cursorDirection=NULL) {
        $query = FunctionalUtils::strip_nulls(array(
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => $cursorDirection
        ));
        $context = $this->getStoreBasedContext()->withPath("bank_accounts");
        return RequesterUtils::execute_get_paginated(BankAccount::class, $context, $query);
    }

    public function getBankAccount($id) {
        $context = $this->getStoreBasedContext()->withPath(array("bank_accounts", $id));
        return RequesterUtils::execute_get(BankAccount::class, $context);
    }

    public function createCardToken($email,
                                    $cardholder,
                                    $cardNumber,
                                    $expMonth,
                                    $expYear,
                                    $cvv,
                                    $type = "one_time",
                                    $usageLimit = NULL,
                                    $line1 = NULL,
                                    $line2 = NULL,
                                    $state = NULL,
                                    $city = NULL,
                                    $country = NULL,
                                    $zip = NULL,
                                    $phoneNumberCountryCode = NULL,
                                    $phoneNumberLocalNumber = NULL) {
        $context = $this->getStoreBasedContext()->withPath("tokens");
        $data = array(
            "cardholder" => $cardholder,
            "card_number" => $cardNumber,
            "exp_month" => $expMonth,
            "exp_year" => $expYear,
            "cvv" => $cvv
        );
        if ($line1 &
            $state &&
            $city &&
            $country &&
            $zip &&
            $phoneNumberCountryCode &&
            $phoneNumberLocalNumber) {
            $data = array_merge($data, array(
                "line1" => $line1,
                "line2" => $line2,
                "state" => $state,
                "city" => $city,
                "country" => $country,
                "zip" => $zip,
                "phone_number" => array(
                    "country_code" => $phoneNumberCountryCode,
                    "local_number" => $phoneNumberLocalNumber
            )));
        }

        $payload = array(
            "payment_type" => "card",
            "type" => $type,
            "usage_limit" => $usageLimit,
            "email" => $email,
            "data" => $data
        );
        $response = $context->getRequester()->post($context->getFullURL(), $payload, RequesterUtils::getHeaders($context));
        return TransactionToken::getCardSchema()->parse($response, array($context));
    }

    public function createCharge($transactionTokenId,
                                 $amount,
                                 $currency,
                                 $capture = true,
                                 $metadata = NULL) {
        $payload = array(
            'transaction_token_id' => $transactionTokenId,
            'amount' => $amount,
            'currency' => $currency
        );
        if ($metadata != NULL)  {
            $payload = array_merge(array("metadata" => $metadata), $payload);
        }
        if (!$capture) {
            $payload = array_merge($payload, array("capture" => "false"));
        }

        $context = $this->getStoreBasedContext()->withPath("charges");
        return RequesterUtils::execute_post(Charge::class, $context, $payload);
    }

    public function getCharge($storeId, $chargeId) {
        $context = $this->getStoreBasedContext()->withPath(array("stores", $storeId, "charges", $chargeId));
        return RequesterUtils::execute_get(Charge::class, $context);
    }

    public function getSubscription($storeId, $subscriptionId) {
        $context = $this->getStoreBasedContext()->withPath(array("stores", $storeId, "subscriptions", $subscriptionId));
        return RequesterUtils::execute_get(Subscription::class, $context);
    }

    public function listTransfers($cursor=NULL,
                                  $limit=NULL,
                                  $cursorDirection=NULL) {
        $query = FunctionalUtils::strip_nulls(array(
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => $cursorDirection
        ));
        $context = $this->getStoreBasedContext()->withPath("transfers");
        return RequesterUtils::execute_get_paginated(Transfer::class, $context, $query);
    }

    public function getTransfer($id) {
        $context = $this->getStoreBasedContext()->withPath(array("transfers", $id));
        return RequesterUtils::execute_get(Transfer::class, $context);
    }

    public function parseWebhookData($data) {
        try {
            $event = $data["event"];
            $parser = NULL;
            switch(strtolower($event)) {

                case "charge_finished":
                    $parser = Charge::getContextParser($this->getStoreBasedContext()->withPath("charges"));
                    break;

                case "subscription_payment":
                case "subscription_failure":
                case "subscription_cancelled":
                    $parser = Subscription::getContextParser($this->getStoreBasedContext()->withPath("subscriptions"));
                    break;

                case "refund_finished":
                    $parser = Refund::getContextParser($this->getStoreBasedContext());
                    break;

                case "transfer_finalized":
                    $parser = Transfer::getContextParser($this->getStoreBasedContext()->withPath("transfers"));
                    break;

                default:
                    throw new GopayUnknownWebhookEvent($event);
            }
            return new WebhookPayload($event, $parser($data["data"]));
        } catch (Exception $exception) {
            throw new GopayInvalidWebhookData($data);
        }
    }

    protected function getSubscriptionContext()
    {
        return $this->getStoreBasedContext()->withPath("subscriptions");
    }

    protected function getTransactionContext()
    {
        return $this->getStoreBasedContext()->withPath("transaction_history");
    }

    protected function getChargeContext()
    {
        return $this->getStoreBasedContext()->withPath("charges");
    }
}