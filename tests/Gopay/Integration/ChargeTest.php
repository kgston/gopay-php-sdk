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
        $charge = $this->createValidCharge(true);
        $this->assertEquals(1000, $charge->requestedAmount);
        $this->assertEquals("JPY", $charge->requestedCurrency);
    }

    public function testCreateChargeOnToken() {
        $charge = $this->createValidToken()->createCharge(1000, "JPY");
        $this->assertEquals(1000, $charge->requestedAmount);
        $this->assertEquals("JPY", $charge->requestedCurrency);
    }

    public function testAuthCaptureCharge() {
        $charge = $this->createValidCharge(False);
        $captured = $charge->capture(1000, "JPY");
        $this->assertTrue($captured);
    }

    public function testInvalidCharge() {
        $this->expectException(GopayRequestError::class);
        $transactionToken = $this->createValidToken();
        $this->getClient()->createCharge($transactionToken->id, -1000, "jpy");
    }

    public function testInvalidAuthCapture() {
        $this->expectException(GopayRequestError::class);
        $charge = $this->createValidCharge(False);
        $charge->capture(2000, "JPY");
    }
    
    public function testCancelAuthCharge() {
        $charge = $this->createValidCharge(False);
        $this->assertEquals('authorized', $charge->status);
        $cancel = $charge->cancel(array(
            'something'=>'anything'
        ))->awaitResult();
        $this->assertEquals($cancel->metadata['something'], 'anything');
    }
    
    public function testInvalidCancel() {
        $this->expectException(GopayRequestError::class);
        $charge = $this->createValidCharge();
        $this->assertEquals('successful', $charge->status);
        $charge->cancel();
    }

}