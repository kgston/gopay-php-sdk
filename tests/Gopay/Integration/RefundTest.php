<?php
namespace GopayTest\Integration;

use Gopay\Enums\RefundReason;
use Gopay\Errors\GopayRequestError;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class RefundTest extends TestCase
{
    use IntegrationSuite;
    use Requests;

    public function testCreateRefund()
    {
        $refund = $this->createValidRefund();
        $this->assertEquals(new Currency('JPY'), $refund->currency);
        $this->assertEquals(1000, $refund->amount);
        $this->assertEquals(RefundReason::FRAUD(), $refund->reason);
        $this->assertEquals("test", $refund->message);
        $this->assertEquals(['something' => 'value'], $refund->metadata);
    }

    public function testInvalidRefund()
    {
        $this->expectException(GopayRequestError::class);
        $charge = $this->createValidCharge(true);
        $charge->createRefund(Money::JPY(2000), RefundReason::FRAUD(), "test", array("something" => "value"));
    }
}
