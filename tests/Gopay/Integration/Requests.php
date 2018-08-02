<?php
namespace GopayTest\Integration;

use DateTimeZone;
use Gopay\Enums\ActiveFilter;
use Gopay\Enums\AppTokenMode;
use Gopay\Enums\InstallmentPlanType;
use Gopay\Enums\PaymentType;
use Gopay\Enums\Period;
use Gopay\Enums\RefundReason;
use Gopay\Enums\TokenType;
use Gopay\Resources\InstallmentPlan;
use Gopay\Resources\ScheduleSettings;
use Gopay\Resources\PaymentMethod\CardPayment;
use GopayTest\Integration\CardNumber;
use Money\Money;

trait Requests
{
    public static $SUCCESSFUL = '4916741415383284';
    public static $CHARGE_FAIL = '4111111111111111';

    public function createValidToken(
        PaymentType $paymentType = null,
        TokenType $type = null,
        $cardNumber = null
    ) {
        $paymentType = isset($paymentType) ? $paymentType : PaymentType::CARD();
        $type = isset($type) ? $type : TokenType::ONE_TIME();
        $cardNumber = isset($cardNumber) ? $cardNumber : static::$SUCCESSFUL;
        $paymentMethod = null;

        switch ($paymentType) {
            case PaymentType::CARD():
                $paymentMethod = $this->createCardPayment($type, $cardNumber);
                break;
        }
        $transactionToken = $this->getClient()->createToken($paymentMethod);
        return $transactionToken;
    }

    public function createCardPayment(TokenType $type, $cardNumber = null)
    {
        $cardNumber = isset($cardNumber) ? $cardNumber : static::$SUCCESSFUL;
        return new CardPayment(
            'test@test.com',
            'PHP test',
            $cardNumber,
            '02',
            '2022',
            '123',
            $type,
            null,
            'test line 1',
            'test line 2',
            'test state',
            'test city',
            'jp',
            '101-1111',
            '81',
            '12910298309128',
            ['customer_id' => 'PHP TEST']
        );
    }

    public function createValidCharge($capture = null)
    {
        $capture = isset($capture) ? $capture : true;
        $transactionToken = $this->createValidToken();
        $charge = $this->getClient()->createCharge($transactionToken->id, Money::JPY(1000), $capture);
        return $charge->awaitResult();
    }

    public function createValidRefund()
    {
        $charge = $this->createValidCharge(true);
        return $charge->createRefund(
            Money::JPY(1000),
            RefundReason::FRAUD(),
            'test',
            ['something' => 'value']
        )->awaitResult();
    }

    public function createValidSubscription()
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

    public function createValidScheduleSubscription()
    {
        $this->deactivateExistingSubscriptionToken();
        $schedule = new ScheduleSettings(
            date_create('last day of this month'),
            new DateTimeZone('Asia/Tokyo'),
            true
        );
        return $this
            ->createValidToken(PaymentType::CARD(), TokenType::SUBSCRIPTION())
            ->createSubscription(
                Money::JPY(10000),
                Period::MONTHLY(),
                Money::JPY(1000),
                $schedule
            )
            ->awaitResult();
    }

    public function createValidInstallmentSubscription()
    {
        $this->deactivateExistingSubscriptionToken();
        $installmentPlan = new InstallmentPlan(
            InstallmentPlanType::FIXED_CYCLES(),
            10
        );
        return $this
            ->createValidToken(PaymentType::CARD(), TokenType::SUBSCRIPTION())
            ->createSubscription(
                Money::JPY(10000),
                Period::BIWEEKLY(),
                Money::JPY(1000),
                null,
                $installmentPlan
            )
            ->awaitResult();
    }
    
    public function createUnconfirmedSubscription()
    {
        $this->deactivateExistingSubscriptionToken();
        return $this
            ->createValidToken(PaymentType::CARD(), TokenType::SUBSCRIPTION(), static::$CHARGE_FAIL)
            ->createSubscription(
                Money::JPY(10000),
                Period::BIWEEKLY(),
                Money::JPY(1000)
            )
            ->awaitResult();
    }

    public function deactivateExistingSubscriptionToken()
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
}
