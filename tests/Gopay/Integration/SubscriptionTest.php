<?php
namespace GopayTest\Integration;

use Gopay\Resources\Subscription;
use PHPUnit\Framework\TestCase;

class SubscriptionTest extends TestCase
{
    use IntegrationSuite;

    public function testSubscriptionParse()
    {
        $str = <<<EOD
   {
      "id": "11111111-1111-1111-1111-111111111111",
      "store_id": "22222222-2222-2222-2222-222222222222",
      "transaction_token_id": "33333333-3333-3333-3333-333333333333",
      "amount": 1000,
      "currency": "JPY",
      "amount_formatted": 1000,
      "period": "monthly",
      "status": "canceled",
      "metadata": {
      },
      "mode": "test",
      "created_on": "2017-07-04T06:06:05.580391Z"
    }
EOD;

        $json = json_decode($str, true);
        $subscription = Subscription::getSchema()->parse($json, array($this->getClient()->getStoreBasedContext()));
        $this->assertEquals(1000, $subscription->amount);
        $this->assertEquals("11111111-1111-1111-1111-111111111111", $subscription->id);
        $this->assertEquals("22222222-2222-2222-2222-222222222222", $subscription->storeId);
        $this->assertEquals("33333333-3333-3333-3333-333333333333", $subscription->transactionTokenId);
        $this->assertEquals(1000, $subscription->amount);
        $this->assertEquals("JPY", $subscription->currency);
        $this->assertEquals(1000, $subscription->amountFormatted);
        $this->assertEquals("monthly", $subscription->period);
        $this->assertEquals("canceled", $subscription->status);
        $this->assertEquals("test", $subscription->mode);
        $this->assertEquals("2017-07-04T06:06:05.580391Z", $subscription->createdOn);
    }
}
