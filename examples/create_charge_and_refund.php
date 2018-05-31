<?php

require_once("vendor/autoload.php");

use Gopay\GopayClient;
use Gopay\Resources\PaymentMethod\CardPayment;

$client = new GopayClient("token", "secret");
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

$client->createToken($paymentMethod)->createCharge(1000, "usd");
// Or
$token = $client->createToken($paymentMethod);
$charge = $client->createCharge($token->id, 1000, "usd");

$charge = $charge->awaitResult();

$refund = $charge->createRefund(1000, "usd", "fraud", "test", array("something" => null));
$refund->fetch();
