<?php

require_once("vendor/autoload.php");

use Gopay\Client;
$client = new Client("VRx5f9RAbPPtszu1hOjR", "6EHR2bkgRnjura3Mrfjm", "https://api.gyro-n.money");
$client->getMe();