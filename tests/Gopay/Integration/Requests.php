<?php
namespace GopayTest\Integration;

use Gopay\Enums\Currency;
use Gopay\Enums\PaymentType;
use Gopay\Enums\RefundReason;
use Gopay\Enums\TokenType;
use Gopay\Resources\PaymentMethod\CardPayment;

use GopayTest\Integration\CardNumber;

trait Requests
{
    public static $SUCCESSFUL = '4242424242424242';
    public static $CHARGE_FAIL = '4111111111111111';

    public function createValidToken(
        PaymentType $paymentType = null,
        TokenType $type = null,
        $cardNumber = null
    ) {
        $paymentType = isset($paymentType) ? $paymentType : PaymentType::CARD();
        $cardNumber = isset($cardNumber) ? $cardNumber : static::$SUCCESSFUL;
        $paymentMethod = null;

        switch ($paymentType) {
            case PaymentType::CARD():
                $paymentMethod = new CardPayment(
                    "test@test.com",
                    "PHP test",
                    $cardNumber,
                    "02",
                    "2022",
                    "123",
                    $type,
                    null,
                    "test line 1",
                    "test line 2",
                    "test state",
                    "test city",
                    "jp",
                    "101-1111",
                    "81",
                    "12910298309128",
                    array('customer_id' => 'PHP TEST')
                );
                break;
        }
        $transactionToken = $this->getClient()->createToken($paymentMethod);
        return $transactionToken;
    }

    public function createValidCharge(bool $capture = true)
    {
        $transactionToken = $this->createValidToken();
        $charge = $this->getClient()->createCharge($transactionToken->id, 1000, Currency::JPY(), $capture);
        return $charge->awaitResult();
    }

    public function createValidRefund()
    {
        $charge = $this->createValidCharge(true);
        return $charge->createRefund(
            1000,
            Currency::JPY(),
            RefundReason::FRAUD(),
            "test",
            array("something" => "value")
        )->awaitResult();
    }
}
