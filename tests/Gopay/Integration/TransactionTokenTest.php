<?php
namespace GopayTest\Integration;

use Gopay\Errors\GopayRequestError;
use PHPUnit\Framework\TestCase;

class TransactionTokenTest extends TestCase
{
    use IntegrationSuite;

    public function testCreateToken()
    {
        $transactionToken = $this->getClient()->createCardToken(
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

    public function testInvalidCardNumber() {
        $this->expectException(GopayRequestError::class);
        $this->getClient()->createCardToken(
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