<?php
namespace GopayTest\Integration;

class TransactionTokenTest extends \PHPUnit_Framework_TestCase
{
    use IntegrationSuite;

    public function testCreateToken()
    {
        $transactionToken = $this->client->createCardToken(
            "test@test.com",
            "PHP test",
            "4242424242424242",
            "02",
            "2022",
            "123",
            "one_time",
            NULL,
            "test",
            NULL,
            "test",
            "test",
            "jp",
            "101-1111",
            "81",
            "12910298309128");

        $this->assertEquals("test@test.com", $transactionToken->email);
        $this->assertEquals("one_time", $transactionToken->type);
    }

    public function xtestInvalidCardNumber() {
        $transactionToken = $this->client->createCardToken(
            "test@test.com",
            "PHP test",
            "4242424242424243",
            "02",
            "2022",
            "123",
            "one_time",
            NULL,
            "test",
            NULL,
            "test",
            "test",
            "jp",
            "101-1111",
            "81",
            "12910298309128");
    }
}