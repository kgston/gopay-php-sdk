<?php

namespace Gopay\Resources;

use Gopay\Resources\StoreAppJWT;
use Gopay\Resources\MerchantAppJWT;
use Gopay\Utility\Json\JsonSchema;

abstract class AppJWT {
    public $token;
    public $secret;

    protected function __construct($token, $secret) {
        $this->token = $token;
        $this->secret = $secret;
    }

    public static function createToken($appToken, $appSecret): AppJWT {
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

class StoreAppJWT extends AppJWT {
    use Jsonable;

    public $sub;
    public $iat;
    public $merchantId;
    public $storeId;
    public $domains;
    public $mode;
    public $creatorId;
    public $version;
    public $jti;

    public function __construct($sub, $iat, $merchantId, $storeId, $domains, $mode, $creatorId, $version, $jti, $token, $secret) {
        if ($sub != "app_token") {
            throw new Exception("Invalid JWT");
        }
        parent::__construct($token, $secret);
        $this->iat = $iat;
        $this->merchantId = $merchantId;
        $this->storeId = $storeId;
        $this->domains = $domains;
        $this->mode = $mode;
        $this->creatorId = $creatorId;
        $this->version = $version;
        $this->jti = $jti;
    }

    protected static function initSchema() {
        return JsonSchema::fromClass(StoreAppJWT::class, true, false);
    }
}

class MerchantAppJWT extends AppJWT {
    use Jsonable;

    public $sub;
    public $issuedAt;
    public $merchantId;
    public $creatorId;
    public $version;
    public $jti;

    public function __construct($sub, $iat, $merchantId, $creatorId, $version, $jti, $token, $secret) {
        if ($sub != "app_token") {
            throw new Exception("Invalid JWT");
        }
        parent::__construct($token, $secret);
        $this->iat = $iat;
        $this->merchantId = $merchantId;
        $this->creatorId = $creatorId;
        $this->version = $version;
        $this->jti = $jti;
    }

    protected static function initSchema() {
        return JsonSchema::fromClass(MerchantAppJWT::class, true, false);
    }
}