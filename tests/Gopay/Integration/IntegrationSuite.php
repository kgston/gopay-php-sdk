<?php
namespace GopayTest\Integration;

use Gopay\GopayClient;

trait IntegrationSuite
{
    public $client;

    public function __construct()
    {
        $token = getenv('GOPAY_PHP_TEST_TOKEN');
        $secret = getenv('GOPAY_PHP_TEST_SECRET');
        $this->client = new GopayClient($token, $secret);
    }


}