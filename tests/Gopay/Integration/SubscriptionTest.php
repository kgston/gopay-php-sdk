<?php
namespace GopayTest\Integration;

use DateTime;
use DateTimeZone;
use Gopay\Enums\AppTokenMode;
use Gopay\Enums\InstallmentPlanType;
use Gopay\Enums\PaymentType;
use Gopay\Enums\Period;
use Gopay\Enums\SubscriptionStatus;
use Gopay\Enums\TokenType;
use Gopay\Resources\InstallmentPlan;
use Gopay\Resources\Paginated;
use Gopay\Resources\ScheduledPayment;
use Gopay\Resources\Subscription;
use Gopay\Resources\ScheduleSettings;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class SubscriptionTest extends TestCase
{
    use IntegrationSuite;
    use Requests;

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
            "schedule_settings": {
                "start_on": "2017-07-31",
                "zone_id": "Asia/Tokyo",
                "preserve_end_of_month": true
            },
            "next_payment": {
                "id": "11e893e1-2842-3cea-b0a8-47819043c1eb",
                "due_date": "2018-08-30",
                "zone_id": "Asia/Tokyo",
                "amount": 1000,
                "currency": "JPY",
                "amount_formatted": 1000,
                "is_paid": false,
                "is_last_payment": false,
                "created_on": "2018-07-31T10:13:08.715295Z",
                "updated_on": "2018-07-31T10:13:08.715295Z"
            },
            "payments_left": 9,
            "status": "canceled",
            "installment_plan": {
                "plan_type": "fixed_cycles",
                "fixed_cycles": 10
            },
            "amount_left": 5000,
            "amount_left_formatted": 5000,
            "metadata": {},
            "mode": "test",
            "created_on": "2017-07-04T06:06:05.580391Z",
            "updated_on": "2017-07-04T06:06:05.580391Z"
        }
EOD;

        $json = json_decode($str, true);
        $subscription = Subscription::getSchema()->parse($json, [$this->getClient()->getStoreBasedContext()]);
        $this->assertEquals('11111111-1111-1111-1111-111111111111', $subscription->id);
        $this->assertEquals('22222222-2222-2222-2222-222222222222', $subscription->storeId);
        $this->assertEquals('33333333-3333-3333-3333-333333333333', $subscription->transactionTokenId);
        $this->assertEquals(Money::JPY(1000), $subscription->amount);
        $this->assertEquals(1000, $subscription->amountFormatted);
        $this->assertEquals(new Currency('JPY'), $subscription->currency);
        $this->assertEquals(Period::MONTHLY(), $subscription->period);
        $this->assertEquals(Money::JPY(100), $subscription->initialAmount);
        $this->assertEquals(100, $subscription->initialAmountFormatted);
        $this->assertEquals(date_create('2017-07-31'), $subscription->scheduleSettings->startOn);
        $this->assertEquals(new DateTimeZone('Asia/Tokyo'), $subscription->scheduleSettings->zoneId);
        $this->assertTrue($subscription->scheduleSettings->preserveEndOfMonth);
        $this->assertEquals(SubscriptionStatus::CANCELED(), $subscription->status);
        $this->assertEquals(AppTokenMode::TEST(), $subscription->mode);
        $this->assertInstanceOf(ScheduledPayment::class, $subscription->nextPayment);
        $this->assertEquals(date_create('2017-07-04T06:06:05.580391Z'), $subscription->createdOn);
        $this->assertEquals(date_create('2017-07-04T06:06:05.580391Z'), $subscription->updatedOn);
        $this->assertEquals(InstallmentPlanType::FIXED_CYCLES(), $subscription->installmentPlan->planType);
        $this->assertEquals('10', $subscription->installmentPlan->fixedCycles);
        $this->assertEquals('9', $subscription->paymentsLeft);
        $this->assertEquals(Money::JPY(5000), $subscription->amountLeft);
        $this->assertEquals(5000, $subscription->amountLeftFormatted);
    }

    public function testCreateSubscription()
    {
        $subscription = $this->createValidSubscription();
        $this->assertEquals(Money::JPY(10000), $subscription->amount);
        $this->assertEquals(new Currency('JPY'), $subscription->currency);
        $this->assertEquals(Period::BIWEEKLY(), $subscription->period);
        $this->assertEquals(Money::JPY(1000), $subscription->initialAmount);
        $this->assertInstanceOf(DateTime::class, $subscription->createdOn);
        $this->assertInstanceOf(DateTime::class, $subscription->updatedOn);
    }

    public function testCreateScheduleSubscription()
    {
        $subscription = $this->createValidScheduleSubscription();
        $this->assertEquals(date_create('last day of this month midnight'), $subscription->scheduleSettings->startOn);
        $this->assertEquals(new DateTimeZone('Asia/Tokyo'), $subscription->scheduleSettings->zoneId);
        $this->assertTrue($subscription->scheduleSettings->preserveEndOfMonth);
    }

    public function testCreateInstallmentSubscription()
    {
        $subscription = $this->createValidInstallmentSubscription();
        $this->assertEquals(InstallmentPlanType::FIXED_CYCLES(), $subscription->installmentPlan->planType);
        $this->assertEquals('10', $subscription->installmentPlan->fixedCycles);
        $this->assertEquals('9', $subscription->paymentsLeft);
        $this->assertInstanceOf(Money::class, $subscription->amountLeft);
        $this->assertInstanceOf(ScheduledPayment::class, $subscription->nextPayment);
    }

    public function testGetSubscription()
    {
        $subscription = $this->createValidSubscription();

        $getSubscription = $this->getClient()->getSubscription($this->storeAppJWT->storeId, $subscription->id);
        $this->assertEquals(Money::JPY(10000), $getSubscription->amount);
        $this->assertEquals(new Currency('JPY'), $getSubscription->currency);
        $this->assertEquals(Period::BIWEEKLY(), $getSubscription->period);
        $this->assertEquals(Money::JPY(1000), $getSubscription->initialAmount);
        $this->assertInstanceOf(ScheduledPayment::class, $subscription->nextPayment);
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
        $this->assertEquals(Money::JPY(99999), $patchedSubscription->amount);
        $this->assertEquals(new Currency('JPY'), $patchedSubscription->currency);
        $this->assertEquals(Period::BIWEEKLY(), $patchedSubscription->period);
        $this->assertEquals(Money::JPY(2000), $patchedSubscription->initialAmount);
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
        $this->assertInstanceOf(Subscription::class, reset($subscriptions->items));
    }

    public function testListPaymentsForSubscription()
    {
        $subscription = $this->createValidInstallmentSubscription();
        $getSubscription = $this->getClient()->getSubscription($this->storeAppJWT->storeId, $subscription->id);
        $payments = $getSubscription->listScheduledPayments();
        $this->assertInstanceOf(Paginated::class, $payments);
        $this->assertGreaterThan(0, count($payments->items));
        $this->assertInstanceOf(ScheduledPayment::class, reset($payments->items));
    }

    public function testListChargesForSubscription()
    {
        $subscription = $this->createValidSubscription();
        $getSubscription = $this->getClient()->getSubscription($this->storeAppJWT->storeId, $subscription->id);
        $charges = $getSubscription->listCharges();
        $this->assertInstanceOf(Paginated::class, $charges);
    }
}
