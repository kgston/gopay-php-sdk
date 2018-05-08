<?php

namespace Gopay\Resources\Authentication;

use Gopay\Utility\Json\JsonSchema;

abstract class AppJWT
{
    public $token;
    public $secret;

    protected function __construct($token, $secret)
    {
        $this->token = $token;
        $this->secret = $secret;
    }

    public static function createToken($appToken, $appSecret)
    {
        $tokenBody = base64_decode(explode(".", $appToken)[1]);
        $appTokenBody = json_decode($tokenBody, true);
        if (array_key_exists("store_id", $appTokenBody)) {
            $class = StoreAppJWT::class;
        } else {
            $class = MerchantAppJWT::class;
        }
        $result = $class::getSchema()->parse($appTokenBody, array($appToken, $appSecret));
        return $result;
    }
}
