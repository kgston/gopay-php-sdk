<?php

require_once("vendor/autoload.php");

use Gopay\Client;
$client = new Client("token", "secret");
$client->getMe();
$client->listStores();