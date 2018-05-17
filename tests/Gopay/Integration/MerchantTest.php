<?php
namespace GopayTest\Integration;

use PHPUnit\Framework\TestCase;
use \DateTime;

class MerchantTest extends TestCase
{
    use IntegrationSuite;

    public function testGetMe()
    {
        $me = $this->getClient()->getMe();
        $this->assertLessThan(date_create('now'), $me->createdOn);
        $this->assertTrue(is_string($me->name));
    }
}
