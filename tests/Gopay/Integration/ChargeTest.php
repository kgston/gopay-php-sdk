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

    public function testCreateChargeOnToken()
    {
        $charge = $this->createValidToken()->createCharge(1000, "JPY");
        $this->assertEquals(1000, $charge->requestedAmount);
        $this->assertEquals("JPY", $charge->requestedCurrency);
    }

    public function testAuthCaptureCharge()
    {
        $charge = $this->createValidCharge(false);
        $captured = $charge->capture(1000, "JPY");
        $this->assertTrue($captured);
    }

    public function testPatchCharge()
    {
        $charge = $this->createValidCharge(true);
        $this->assertEquals(0, count($charge->metadata));
        
        $charge = $charge->patch(array('testId' => 12345));
        $this->assertTrue($charge->metadata['testId'] === 12345);
    }

    public function testInvalidCharge()
    {
        $this->expectException(GopayRequestError::class);
        $transactionToken = $this->createValidToken();
        $this->getClient()->createCharge($transactionToken->id, -1000, "jpy");
    }

    public function testInvalidAuthCapture()
    {
        $this->expectException(GopayRequestError::class);
        $charge = $this->createValidCharge(false);
        $charge->capture(2000, "JPY");
    }
    
    public function testCancelAuthCharge()
    {
        $charge = $this->createValidCharge(false);
        $this->assertEquals('authorized', $charge->status);
        $cancel = $charge->cancel(array(
            'something'=>'anything'
        ))->awaitResult();
        $this->assertEquals($cancel->metadata['something'], 'anything');
    }
    
    public function testInvalidCancel()
    {
        $this->expectException(GopayRequestError::class);
        $charge = $this->createValidCharge();
        $this->assertEquals('successful', $charge->status);
        $charge->cancel();
    }
}
