<?php
namespace GopayTest\Integration;

use Gopay\Enums\Currency;
use Gopay\Enums\RefundReason;
use Gopay\Errors\GopayRequestError;
use PHPUnit\Framework\TestCase;

class RefundTest extends TestCase
{
    use IntegrationSuite;
    use Requests;

    public function testCreateRefund()
    {
        $refund = $this->createValidRefund();
        $this->assertEquals(Currency::JPY(), $refund->currency);
        $this->assertEquals(1000, $refund->amount);
        $this->assertEquals(RefundReason::FRAUD(), $refund->reason);
        $this->assertEquals("test", $refund->message);
        $this->assertEquals(['something' => 'value'], $refund->metadata);
    }

    public function testInvalidRefund()
    {
        $this->expectException(GopayRequestError::class);
        $charge = $this->createValidCharge(true);
        $charge->createRefund(2000, Currency::JPY(), RefundReason::FRAUD(), "test", array("something" => "value"));
    }
}
