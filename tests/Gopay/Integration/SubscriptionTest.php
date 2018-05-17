<?php
namespace GopayTest\Integration;

use Gopay\Enums\InstallmentPlanType;
use Gopay\Enums\Period;
use Gopay\Enums\TokenType;
use Gopay\Resources\Subscription;
use PHPUnit\Framework\TestCase;

class SubscriptionTest extends TestCase
{
    use IntegrationSuite;
    use Requests;

    public function createValidSubscription()
    {
        $transactionToken = $this->createValidToken(TokenType::SUBSCRIPTION());
        return $this->getClient()->createSubscription(
            $transactionToken->id,
            10000,
            "jpy",
            Period::BIWEEKLY(),
            1000,
            date_create("+5 months")
        );
    }

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
      "initial_amount": 100,
      "subsequent_cycles_start": "2018-05-14T09:40:39.337331Z",
      "status": "canceled",
      "installment_plan": {
        "plan_type": "fixed_cycles",
        "fixed_cycles": 10
      },
      "metadata": {},
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
        $this->assertEquals(Period::MONTHLY(), $subscription->period);
        $this->assertEquals(100, $subscription->initialAmount);
        $this->assertEquals(date_create("2018-05-14T09:40:39.337331Z"), $subscription->subsequentCyclesStart);
        $this->assertEquals("canceled", $subscription->status);
        $this->assertEquals("test", $subscription->mode);
        $this->assertEquals(date_create("2017-07-04T06:06:05.580391Z"), $subscription->createdOn);
        $this->assertEquals(InstallmentPlanType::FIXED_CYCLES(), $subscription->installmentPlan->planType);
        $this->assertEquals("10", $subscription->installmentPlan->fixedCycles);
    }

    public function testCreateSubscription()
    {
        $subscription = $this->createValidSubscription();
        $this->assertEquals(10000, $subscription->amount);
        $this->assertEquals("JPY", $subscription->currency);
        $this->assertEquals(Period::BIWEEKLY(), $subscription->period);
        $this->assertEquals(1000, $subscription->initialAmount);
    }

    public function testGetSubscription()
    {
        $subscription = $this->createValidSubscription();

        $getSubscription = $this->getClient()->getSubscription($this->storeAppJWT->storeId, $subscription->id);
        $this->assertEquals(10000, $getSubscription->amount);
        $this->assertEquals("JPY", $getSubscription->currency);
        $this->assertEquals(Period::BIWEEKLY(), $getSubscription->period);
        $this->assertEquals(1000, $getSubscription->initialAmount);
    }

    public function testCancelSubscription()
    {
        $subscription = $this->createValidSubscription();
        sleep(1);

        $getSubscription = $this->getClient()->getSubscription($this->storeAppJWT->storeId, $subscription->id);
        $this->assertTrue($getSubscription->cancel());

        $canceledSubscription = $this->getClient()->getSubscription($this->storeAppJWT->storeId, $subscription->id);
        echo($canceledSubscription->id);
        $this->assertEquals('canceled', $canceledSubscription->status);
    }

    public function testListSubscription()
    {
        $this->createValidSubscription();
        $subscriptions = $this->getClient()->listSubscriptions();
        $this->assertGreaterThan(0, count($subscriptions->items));
    }
}
