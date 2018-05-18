<?php
namespace GopayTest\Integration;

use Gopay\Enums\CancelStatus;
use Gopay\Enums\ChargeStatus;
use Gopay\Enums\Currency;
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
        $this->assertEquals(Currency::JPY(), $charge->requestedCurrency);
    }

    public function testCreateChargeOnToken()
    {
        $charge = $this->createValidToken()->createCharge(1000, Currency::JPY());
        $this->assertEquals(1000, $charge->requestedAmount);
        $this->assertEquals(Currency::JPY(), $charge->requestedCurrency);
    }

    public function testAuthCaptureCharge()
    {
        $charge = $this->createValidCharge(false);
        $captured = $charge->capture(1000, Currency::JPY());
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
        $this->getClient()->createCharge($transactionToken->id, -1000, Currency::JPY());
    }

    public function testInvalidAuthCapture()
    {
        $this->expectException(GopayRequestError::class);
        $charge = $this->createValidCharge(false);
        $charge->capture(2000, Currency::JPY());
    }
    
    public function testCancelAuthCharge()
    {
        $charge = $this->createValidCharge(false);
        $this->assertEquals(ChargeStatus::AUTHORIZED(), $charge->status);
        $cancel = $charge->cancel(array(
            'something'=>'anything'
        ))->awaitResult();
        $this->assertEquals(CancelStatus::SUCCESSFUL(), $cancel->status);
        $this->assertEquals($cancel->metadata['something'], 'anything');
    }
    
    public function testInvalidCancel()
    {
        $charge = $this->createValidCharge();
        $this->assertEquals(ChargeStatus::SUCCESSFUL(), $charge->status);
        
        $this->expectException(GopayRequestError::class);
        $charge->cancel();
    }
}
