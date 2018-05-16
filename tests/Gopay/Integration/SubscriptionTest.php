<?php
namespace GopayTest\Integration;

use Gopay\Enums\InstallmentPlanType;
use Gopay\Enums\Period;
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
        var_dump($subscription);
        $this->assertEquals(1000, $subscription->amount);
        $this->assertEquals("11111111-1111-1111-1111-111111111111", $subscription->id);
        $this->assertEquals("22222222-2222-2222-2222-222222222222", $subscription->storeId);
        $this->assertEquals("33333333-3333-3333-3333-333333333333", $subscription->transactionTokenId);
        $this->assertEquals(1000, $subscription->amount);
        $this->assertEquals("JPY", $subscription->currency);
        $this->assertEquals(1000, $subscription->amountFormatted);
        $this->assertEquals(Period::MONTHLY(), $subscription->period);
        $this->assertEquals(100, $subscription->initialAmount);
        $this->assertEquals("2018-05-14T09:40:39.337331Z", $subscription->subsequentCyclesStart);
        $this->assertEquals("canceled", $subscription->status);
        $this->assertEquals("test", $subscription->mode);
        $this->assertEquals("2017-07-04T06:06:05.580391Z", $subscription->createdOn);
        $this->assertEquals(InstallmentPlanType::FIXED_CYCLES(), $subscription->installmentPlan->planType);
        $this->assertEquals("10", $subscription->installmentPlan->fixedCycles);
    }
}
