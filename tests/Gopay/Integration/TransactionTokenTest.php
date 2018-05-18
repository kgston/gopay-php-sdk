<?php
namespace GopayTest\Integration;

use Gopay\Enums\ActiveFilter;
use Gopay\Enums\AppTokenMode;
use Gopay\Enums\TokenType;
use Gopay\Errors\GopayRequestError;
use Gopay\Resources\PaymentMethod\CardPayment;
use Gopay\Resources\PaymentMethod\CardPaymentPatch;
use Gopay\Resources\PaymentMethod\PaymentMethodPatch;
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
        $this->assertEquals('PHP TEST', $transactionToken->metadata['customer_id']);
    }

    public function testGetExistingToken()
    {
        $transactionToken = $this->createValidToken();
        $retrievedTransactionToken = $this->getClient()->getTransactionToken(
            $transactionToken->id
        );
        $this->assertEquals($transactionToken->id, $retrievedTransactionToken->id);
    }

    public function testListExistingTokens()
    {
        $localCustomerId = substr(sha1(rand()), 0, 15);
        $transactionToken = $this->getClient()->createToken(new CardPayment(
            "test@test.com",
            "PHP test",
            "4242424242424242",
            "02",
            "2022",
            "123",
            TokenType::Recurring(),
            null,
            "test line 1",
            "test line 2",
            "test state",
            "test city",
            "jp",
            "101-1111",
            "81",
            "12910298309128"
        ), $localCustomerId);
        
        $maxRetries = 3;
        $tokenList = null;
        do {
            $maxRetries--;
            sleep(1); // It takes a bit of time for to index to get updated
            $tokenList = $this->getClient()->listTransactionTokens(
                "test@test.com",
                $localCustomerId,
                TokenType::RECURRING(),
                AppTokenMode::TEST(),
                ActiveFilter::ACTIVE()
            );
        } while (empty($tokenList->items) || empty($maxRetries));
        
        $this->assertTrue(count($tokenList->items) === 1);
        $this->assertTrue(array_key_exists('gopay-customer-id', $tokenList->items[0]->metadata));
    }

    public function testPatchExistingToken()
    {
        $transactionToken = $this->createValidToken();
        $this->assertEquals("test@test.com", $transactionToken->email);
        $this->assertEquals('PHP TEST', $transactionToken->metadata['customer_id']);
        
        $patchRequest = new PaymentMethodPatch(
            "test@changed.int",
            array('customer_id' => 'PHP TESTER')
        );
        $patchedTxToken = $transactionToken->patch($patchRequest);
        $this->assertEquals("test@changed.int", $patchedTxToken->email);
        $this->assertEquals('PHP TESTER', $patchedTxToken->metadata['customer_id']);
        $this->assertTrue($patchedTxToken->data !== null);
    }

    public function testPatchExistingCardPayment()
    {
        $transactionToken = $this->createValidToken();
        $this->assertEquals("test@test.com", $transactionToken->email);
        $this->assertEquals('PHP TEST', $transactionToken->metadata['customer_id']);
        
        $patchRequest = new CardPaymentPatch(
            999,
            "test@changed.int",
            null
        );
        $patchedTxToken = $transactionToken->patch($patchRequest);
        $this->assertEquals("test@changed.int", $patchedTxToken->email);
        $this->assertEquals('PHP TEST', $patchedTxToken->metadata['customer_id']);
    }

    public function testDeleteExistingToken()
    {
        $transactionToken = $this->createValidToken();
        $transactionToken->deactivate();

        $deactivatedTransactionToken = $this->getClient()->getTransactionToken($transactionToken->id);
        $this->assertFalse($deactivatedTransactionToken->active);
    }

    public function testInvalidCardNumber()
    {
        $this->expectException(GopayRequestError::class);
        $this->getClient()->createToken(new CardPayment(
            "test@test.com",
            "PHP test",
            "4242424242424243",
            "02",
            "2022",
            "123",
            null,
            null,
            "test line 1",
            "test line 2",
            "test state",
            "test city",
            "jp",
            "101-1111",
            "81",
            "12910298309128"
        ));
    }
}
