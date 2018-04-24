<?php
namespace GopayTest\Integration;

use Gopay\GopayClient;
use Gopay\Resources\AppJWT;

trait IntegrationSuite
{
    public $client = NULL;
    public $storeAppJWT;

    private function init()
    {
        $token = getenv('GOPAY_PHP_TEST_TOKEN');
        $secret = getenv('GOPAY_PHP_TEST_SECRET');
        $endpoint = getenv('GOPAY_PHP_TEST_ENDPOINT');
        $this->storeAppJWT = AppJWT::createToken($token, $secret);
        $this->client = new GopayClient($this->storeAppJWT, null, $endpoint);
    }

    public function getClient() {
        if ($this->client === NULL) {
            $this->init();
        }
        return $this->client;
    }
}