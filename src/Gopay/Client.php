<?php

namespace Gopay;


use Gopay\Requests\HttpRequester;
use Gopay\Requests\RequestContext;
use Gopay\Requests\Requester;
use Gopay\Resources\Jsonable;
use Gopay\Resources\Merchant;

class Client
{
    private $endpoint;

    private $appToken;

    private $requester;

    function __construct($endpoint, $appToken){
        $this->endpoint = $endpoint;
        $this->appToken = $appToken;
        $this->requester = new HttpRequester();
    }

    private function getDefaultContext() {
        return new RequestContext($this->endpoint, "/", $this->appToken);
    }

    public function withRequester(Requester $requester) {
        $this->requester = $requester;
        return $this;
    }

    public function getMe() {
        return execute_get(
            $this->requester,
            Merchant::class,
            $this->getDefaultContext()->withPath("me")
        );
    }

    public function listStores() {

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