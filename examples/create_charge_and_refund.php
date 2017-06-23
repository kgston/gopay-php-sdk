<?php

require_once("vendor/autoload.php");

use Gopay\GopayClient;
$client = new GopayClient("token", "secret");

$client->createCardToken("test@test.com", "test account", "4242424242424242", "02", "2022", "123", "one_time", NULL, "test", NULL, "test", "test", "jp", "101-1111", "81", "12910298309128")->createCharge(1000, "usd");
$token = $client->createCardToken("test@test.com", "test account", "4242424242424242", "02", "2022", "123", "one_time", NULL, "test", NULL, "test", "test", "jp", "101-1111", "81", "12910298309128");
$charge = $client->createCharge($token->id, 1000, "usd");

do {
    sleep(1);
    $charge = $charge->fetch();
} while (strtolower($charge->status) === "pending");

$refund = $charge->createRefund(1000, "usd", "fraud", "test", array("something" => NULL));
$refund->fetch();
