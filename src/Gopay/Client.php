<?php

namespace Gopay;

use Gopay\Requests\HttpRequester;
use Gopay\Requests\RequestContext;
use Gopay\Requests\Requester;
use Gopay\Resources\CardConfiguration;
use Gopay\Resources\Merchant;
use Gopay\Resources\Store;
use Gopay\Utility\RequesterUtils;

class Client
{
    private $endpoint;

    private $appToken;

    private $appSecret;

    private $requester;

    function __construct($appToken, $appSecret, $endpoint = "https://api.gopay.jp"){
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

    }

    public function listBankAccounts() {

    }

    public function getBankAccount($id) {

    }

    public function createToken() {

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