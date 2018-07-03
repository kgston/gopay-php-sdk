<?php
namespace GopayTest\Integration;

use Gopay\Enums\CancelStatus;
use Gopay\Enums\ChargeStatus;
use Gopay\Errors\GopayRequestError;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class ChargeTest extends TestCase
{
    use IntegrationSuite;
    use Requests;

    public function testCreateCharge()
    {
        $charge = $this->createValidCharge(true);
        $this->assertEquals(1000, $charge->requestedAmount);
        $this->assertEquals(new Currency('JPY'), $charge->requestedCurrency);
    }

    public function testCreateChargeOnToken()
    {
        $charge = $this->createValidToken()->createCharge(Money::JPY(1000));
        $this->assertEquals(1000, $charge->requestedAmount);
        $this->assertEquals(new Currency('JPY'), $charge->requestedCurrency);
    }

    public function testAuthCaptureCharge()
    {
        $charge = $this->createValidCharge(false);
        $captured = $charge->capture(Money::JPY(1000));
        $this->assertTrue($captured);
    }

    public function testPartialAuthCaptureCharge()
    {
        $charge = $this->createValidCharge(false);
        $captured = $charge->capture(Money::JPY(500));
        $this->assertTrue($captured);
    }

    public function testDefaultAuthCaptureCharge()
    {
        $charge = $this->createValidCharge(false);
        $captured = $charge->capture();
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
        $this->getClient()->createCharge($transactionToken->id, Money::JPY(-1000));
    }

    public function testInvalidAuthCapture()
    {
        $this->expectException(GopayRequestError::class);
        $charge = $this->createValidCharge(false);
        $charge->capture(Money::JPY(2000));
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
