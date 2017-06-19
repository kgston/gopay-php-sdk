<?php

namespace Gopay;

use Gopay\Requests\HttpRequester;
use Gopay\Requests\RequestContext;
use Gopay\Requests\Requester;
use Gopay\Resources\CardConfiguration;
use Gopay\Resources\Charge;
use Gopay\Resources\Merchant;
use Gopay\Resources\Store;
use Gopay\Resources\TransactionToken;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\RequesterUtils;

class Client
{
    private $endpoint;

    private $appToken;

    private $appSecret;

    private $requester;

    function __construct($appToken,
                         $appSecret = NULL,
                         $endpoint = "https://api.gopay.jp"){
        $this->endpoint = $endpoint;
        $this->appToken = $appToken;
        $this->appSecret = $appSecret;
        $this->requester = new HttpRequester();

    }

    private function getDefaultContext()
    {
        return new RequestContext($this->requester, $this->endpoint, "/", $this->appToken, $this->appSecret);
    }

    public function withRequester(Requester $requester) {
        $this->requester = $requester;
        return $this;
    }

    public function getMe() {
        return RequesterUtils::execute_get(
            Merchant::class,
            $this->getDefaultContext()->withPath("me")
        );
    }

    public function listStores($cursor=NULL,
                               $limit=NULL,
                               $cursorDirection=NULL) {
        $query = FunctionalUtils::strip_nulls(array(
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => $cursorDirection
        ));
        return RequesterUtils::execute_get_paginated(
            Store::class,
            $this->getDefaultContext()->withPath("stores"),
            $query
        );
    }

    public function getStore($id) {
        $context = $this->getDefaultContext()->withPath("stores/" . $id);
        return RequesterUtils::execute_get(Store::class, $context);
    }

    public function listBankAccounts() {

    }

    public function getBankAccount($id) {

    }

    public function createCardToken($email,
                                    $amount,
                                    $currency,
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
        $context = $this->getDefaultContext()->withPath("tokens");
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
            "amount" => $amount,
            "currency" => $currency,
            "data" => $data
        );
        $response = $context->getRequester()->post($context->getFullURL(), $payload, RequesterUtils::getHeaders($context));
        return TransactionToken::getCardSchema()->parse($response, array($context));
    }

    public function createCharge($transactionTokenId,
                                 $amount,
                                 $currency,
                                 $metadata = NULL) {
        $payload = array(
            'transaction_token_id' => $transactionTokenId,
            'amount' => $amount,
            'currency' => $currency
        );
        if ($metadata != NULL)  {
            $payload = array_map(array("metadata" => $metadata), $payload);
        }

        $context = $this->getDefaultContext()->withPath("charges");
        return RequesterUtils::execute_post(Charge::class, $context, $payload);
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
                                $cursorDirection=NULL) {
        $context = $this->getDefaultContext()->withPath("charges");
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

    public function listTransactions() {

    }

    public function listTransfers() {

    }

    public function getTransfer($id) {

    }
}