<?php

namespace Gopay;

use Gopay\Requests\HttpRequester;
use Gopay\Requests\RequestContext;
use Gopay\Requests\Requester;
use Gopay\Resources\CardConfiguration;
use Gopay\Resources\Merchant;
use Gopay\Resources\Store;
use Gopay\Resources\TransactionToken;
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

    public function listStores() {
        return RequesterUtils::execute_get_paginated(
            Store::class,
            $this->getDefaultContext()->withPath("stores")
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
        return RequesterUtils::execute_post(TransactionToken::class, $context, $payload);
    }

    public function createCharge() {

    }

    public function listTransactions() {

    }

    public function listTransfers() {

    }

    public function getTransfer($id) {

    }
}