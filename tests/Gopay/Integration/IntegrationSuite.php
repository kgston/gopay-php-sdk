<?php
namespace GopayTest\Integration;

use Gopay\GopayClient;
use Gopay\Resources\AppJWT;

trait IntegrationSuite
{
    public $client = NULL;

    private function init()
    {
        $token = getenv('GOPAY_PHP_TEST_TOKEN');
        $secret = getenv('GOPAY_PHP_TEST_SECRET');
        $endpoint = getenv('GOPAY_PHP_TEST_ENDPOINT');
        $this->client = new GopayClient($endpoint, AppJWT::createToken($token, $secret));
    }

    public function getClient() {
        if ($this->client === NULL) {
            $this->init();
        }
        return $this->client;
    }
}