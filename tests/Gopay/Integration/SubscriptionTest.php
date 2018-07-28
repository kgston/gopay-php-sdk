<?php
namespace GopayTest\Integration;

use DateTime;
use Gopay\Enums\ActiveFilter;
use Gopay\Enums\AppTokenMode;
use Gopay\Enums\InstallmentPlanType;
use Gopay\Enums\PaymentType;
use Gopay\Enums\Period;
use Gopay\Enums\SubscriptionStatus;
use Gopay\Enums\TokenType;
use Gopay\Resources\InstallmentPlan;
use Gopay\Resources\Paginated;
use Gopay\Resources\Subscription;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class SubscriptionTest extends TestCase
{
    use IntegrationSuite;
    use Requests;

    private function createValidSubscription()
    {
        $this->deactivateExistingSubscriptionToken();
        return $this
            ->createValidToken(PaymentType::CARD(), TokenType::SUBSCRIPTION())
            ->createSubscription(
                Money::JPY(10000),
                Period::BIWEEKLY(),
                Money::JPY(1000)
            )
            ->awaitResult();
    }
    
    private function createUnconfirmedSubscription()
    {
        $this->deactivateExistingSubscriptionToken();
        return $this
            ->createValidToken(PaymentType::CARD(), TokenType::SUBSCRIPTION(), static::$CHARGE_FAIL)
            ->createSubscription(
                Money::JPY(10000),
                Period::BIWEEKLY(),
                Money::JPY(1000),
                date_create("+1 day")
            )
            ->awaitResult();
    }

    private function deactivateExistingSubscriptionToken()
    {
        $tokenList = $this->getClient()->listTransactionTokens(
            null,
            null,
            TokenType::SUBSCRIPTION(),
            AppTokenMode::TEST(),
            ActiveFilter::ACTIVE()
        );
        
        foreach ($tokenList->items as $token) {
            $token->deactivate();
        }
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
      "initial_amount_formatted": 100,
      "subsequent_cycles_start": "2018-05-14T09:40:39.337331Z",
      "status": "canceled",
      "installment_plan": {
        "plan_type": "fixed_cycles",
        "fixed_cycles": 10
      },
      "metadata": {},
      "mode": "test",
      "created_on": "2017-07-04T06:06:05.580391Z",
      "updated_on": "2017-07-04T06:06:05.580391Z"
    }
EOD;

        $json = json_decode($str, true);
        $subscription = Subscription::getSchema()->parse($json, array($this->getClient()->getStoreBasedContext()));
        $this->assertEquals(1000, $subscription->amount);
        $this->assertEquals("11111111-1111-1111-1111-111111111111", $subscription->id);
        $this->assertEquals("22222222-2222-2222-2222-222222222222", $subscription->storeId);
        $this->assertEquals("33333333-3333-3333-3333-333333333333", $subscription->transactionTokenId);
        $this->assertEquals(1000, $subscription->amount);
        $this->assertEquals(1000, $subscription->amountFormatted);
        $this->assertEquals(new Currency('JPY'), $subscription->currency);
        $this->assertEquals(Period::MONTHLY(), $subscription->period);
        $this->assertEquals(100, $subscription->initialAmount);
        $this->assertEquals(100, $subscription->initialAmountFormatted);
        $this->assertEquals(date_create("2018-05-14T09:40:39.337331Z"), $subscription->subsequentCyclesStart);
        $this->assertEquals(SubscriptionStatus::CANCELED(), $subscription->status);
        $this->assertEquals(AppTokenMode::TEST(), $subscription->mode);
        $this->assertEquals(date_create("2017-07-04T06:06:05.580391Z"), $subscription->createdOn);
        $this->assertEquals(date_create("2017-07-04T06:06:05.580391Z"), $subscription->updatedOn);
        $this->assertEquals(InstallmentPlanType::FIXED_CYCLES(), $subscription->installmentPlan->planType);
        $this->assertEquals("10", $subscription->installmentPlan->fixedCycles);
    }

    public function testCreateSubscription()
    {
        $subscription = $this->createValidSubscription();
        $this->assertEquals(10000, $subscription->amount);
        $this->assertEquals(new Currency('JPY'), $subscription->currency);
        $this->assertEquals(Period::BIWEEKLY(), $subscription->period);
        $this->assertEquals(1000, $subscription->initialAmount);
        $this->assertInstanceOf(DateTime::class, $subscription->createdOn);
        $this->assertInstanceOf(DateTime::class, $subscription->updatedOn);
    }

    public function testGetSubscription()
    {
        $subscription = $this->createValidSubscription();

        $getSubscription = $this->getClient()->getSubscription($this->storeAppJWT->storeId, $subscription->id);
        $this->assertEquals(10000, $getSubscription->amount);
        $this->assertEquals(new Currency('JPY'), $getSubscription->currency);
        $this->assertEquals(Period::BIWEEKLY(), $getSubscription->period);
        $this->assertEquals(1000, $getSubscription->initialAmount);
    }

    public function testPatchSubscription()
    {
        $subscription = $this->createUnconfirmedSubscription();

        $updatedToken = $this->createValidToken(PaymentType::CARD(), TokenType::SUBSCRIPTION());
        
        $patchedSubscription = $subscription->patch(
            $updatedToken->id,
            Money::JPY(99999),
            Money::JPY(2000),
            null,
            new InstallmentPlan(InstallmentPlanType::FIXED_CYCLES(), 9)
        )->awaitResult();
        $this->assertEquals(99999, $patchedSubscription->amount);
        $this->assertEquals(new Currency('JPY'), $patchedSubscription->currency);
        $this->assertEquals(Period::BIWEEKLY(), $patchedSubscription->period);
        $this->assertEquals(2000, $patchedSubscription->initialAmount);
        $this->assertEquals(
            InstallmentPlanType::FIXED_CYCLES(),
            $patchedSubscription->installmentPlan->planType
        );
        $this->assertEquals(9, $patchedSubscription->installmentPlan->fixedCycles);
    }
    
    public function testCancelSubscription()
    {
        $subscription = $this->createValidSubscription();

        $getSubscription = $this->getClient()->getSubscription($this->storeAppJWT->storeId, $subscription->id);
        $this->assertTrue($getSubscription->cancel());

        $canceledSubscription = $this->getClient()->getSubscription($this->storeAppJWT->storeId, $subscription->id);
        $this->assertEquals(SubscriptionStatus::CANCELED(), $canceledSubscription->status);
    }

    public function testListSubscription()
    {
        $this->createValidSubscription();
        $subscriptions = $this->getClient()->listSubscriptions();
        $this->assertGreaterThan(0, count($subscriptions->items));
    }

    public function testListChargesForSubscription()
    {
        $subscription = $this->createValidSubscription();
        $getSubscription = $this->getClient()->getSubscription($this->storeAppJWT->storeId, $subscription->id);
        $charges = $getSubscription->listCharges();
        $this->assertInstanceOf(Paginated::class, $charges);
    }
}
