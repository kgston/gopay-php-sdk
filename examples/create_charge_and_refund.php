<?php

require_once("vendor/autoload.php");

use Gopay\GopayClient;
use Gopay\Enums\RefundReason;
use Gopay\Resources\PaymentMethod\CardPayment;
use Money\Money;

$client = new GopayClient(AppJWT::createToken('token', 'secret'));
$paymentMethod = new CardPayment(
    "test@test.com",
    "PHP example",
    "4242424242424242",
    "02",
    "2022",
    "123",
    null,
    "test line 1",
    "test line 2",
    "test state",
    "jp",
    "101-1111",
    "81",
    "12910298309128"
);

$client->createToken($paymentMethod)->createCharge(Money::USD(1000));
// Or
$token = $client->createToken($paymentMethod);
$charge = $client->createCharge($token->id, Money::USD(1000));
$charge = $charge->awaitResult();

$refund = $charge
    ->createRefund(Money::USD(1000), RefundReason::FRAUD(), "test", array("something" => null))
    ->awaitResult(); // Long polls for the next status change, with a 5s timeout

// Use fetch to fetch the latest data from the API
$refund->fetch();
