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
        $createdOn = DateTime::createFromFormat(DateTime::ISO8601, $me->createdOn);
        $this->assertLessThan(new DateTime(), $createdOn);
        $this->assertTrue(is_string($me->name));
    }
}
