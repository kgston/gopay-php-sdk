<?php
namespace GopayTest\Integration;

use Gopay\Errors\GopayRequestError;
use PHPUnit\Framework\TestCase;

class RefundTest extends TestCase
{
    use IntegrationSuite;
    use Requests;

    public function testCreateRefund()
    {
        $refund = $this->createValidRefund();
        $this->assertEquals("JPY", $refund->currency);
        $this->assertEquals(1000, $refund->amount);
        $this->assertEquals("fraud", $refund->reason);
        $this->assertEquals("test", $refund->message);
        $this->assertEquals(['something' => 'value'], $refund->metadata);
    }

    public function testInvalidRefund() {
        $this->expectException(GopayRequestError::class);
        $charge = $this->createValidCharge();
        $charge->createRefund(2000, "jpy", "fraud", "test", array("something" => "value"));
    }


}