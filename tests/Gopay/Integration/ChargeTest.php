<?php
namespace GopayTest\Integration;

use Gopay\Errors\GopayRequestError;
use PHPUnit\Framework\TestCase;

class ChargeTest extends TestCase
{
    use IntegrationSuite;
    use Requests;

    public function testCreateCharge()
    {
        $charge = $this->createValidCharge();
        $this->assertEquals(1000, $charge->requestedAmount);
        $this->assertEquals("JPY", $charge->requestedCurrency);
    }

    public function testInvalidCharge() {
        $this->expectException(GopayRequestError::class);
        $transactionToken = $this->createValidToken();
        $this->getClient()->createCharge($transactionToken->id, -1000, "jpy");
    }

}