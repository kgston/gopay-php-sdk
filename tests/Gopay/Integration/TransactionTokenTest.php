<?php
namespace GopayTest\Integration;

use Gopay\Errors\GopayRequestError;
use PHPUnit\Framework\TestCase;

class TransactionTokenTest extends TestCase
{
    use IntegrationSuite;
    use Requests;

    public function testCreateToken() {
        $transactionToken = $this->createValidToken();
        $this->assertEquals("test@test.com", $transactionToken->email);
        $this->assertEquals("one_time", $transactionToken->type);
    }

    public function testGetExistingToken() {
        $transactionToken = $this->createValidToken();
        $retrievedTransactionToken = $this->getClient()->getTransactionToken($this->storeAppJWT->storeId, $transactionToken->id);
        $this->assertEquals($transactionToken->id, $retrievedTransactionToken->id);
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