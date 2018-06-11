<?php
namespace GopayTest\Integration;

use Gopay\GopayClient;
use Gopay\Enums\AppTokenMode;
use Gopay\Resources\Authentication\AppJWT;
use Gopay\Resources\Authentication\StoreAppJWT;

trait IntegrationSuite
{
    private $client = null;
    public $storeAppJWT;

    private function init()
    {
        $token = getenv('GOPAY_PHP_TEST_TOKEN');
        $secret = getenv('GOPAY_PHP_TEST_SECRET');
        $endpoint = getenv('GOPAY_PHP_TEST_ENDPOINT');
        $this->storeAppJWT = AppJWT::createToken($token, $secret);

        if ($this->storeAppJWT instanceof StoreAppJWT && $this->storeAppJWT->mode === AppTokenMode::TEST()) {
            $this->client = new GopayClient($this->storeAppJWT, null, $endpoint);
        } else {
            $this->markTestSkipped('Unable to run test suite with a Merchant app token or a non-test token');
        }
    }

    public function getClient()
    {
        if ($this->client === null) {
            $this->init();
        }
        return $this->client;
    }
}
