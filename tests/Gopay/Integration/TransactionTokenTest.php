<?php
namespace GopayTest\Integration;

use Gopay\Enums\TokenType;
use Gopay\Errors\GopayRequestError;
use PHPUnit\Framework\TestCase;

class TransactionTokenTest extends TestCase
{
    use IntegrationSuite;
    use Requests;

    public function testCreateToken()
    {
        $transactionToken = $this->createValidToken();
        $this->assertEquals("test@test.com", $transactionToken->email);
        $this->assertEquals(TokenType::ONE_TIME(), $transactionToken->type);
    }

    public function testGetExistingToken()
    {
        $transactionToken = $this->createValidToken();
        $retrievedTransactionToken = $this->getClient()->getTransactionToken(
            $this->storeAppJWT->storeId,
            $transactionToken->id
        );
        $this->assertEquals($transactionToken->id, $retrievedTransactionToken->id);
    }

    public function testInvalidCardNumber()
    {
        $this->expectException(GopayRequestError::class);
        $this->getClient()->createCardToken(
            "test@test.com",
            "PHP test",
            "4242424242424243",
            "02",
            "2022",
            "123",
            TokenType::ONE_TIME(),
            null,
            "test",
            null,
            "test",
            "test",
            "jp",
            "101-1111",
            "81",
            "12910298309128"
        );
    }
}
