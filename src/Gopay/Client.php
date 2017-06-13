<?php

namespace Gopay;


use Gopay\Requests\HttpRequester;

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
}