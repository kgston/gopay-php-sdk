<?php

namespace Gopay\Resources\Authentication;

use Gopay\Resources\Jsonable;
use Gopay\Utility\Json\JsonSchema;

class MerchantAppJWT extends AppJWT
{
    use Jsonable;

    public $sub;
    public $issuedAt;
    public $merchantId;
    public $creatorId;
    public $version;
    public $jti;

    public function __construct(
        $sub,
        $iat,
        $merchantId,
        $creatorId,
        $version,
        $jti,
        $token,
        $secret
    ) {
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

    protected static function initSchema()
    {
        return JsonSchema::fromClass(MerchantAppJWT::class, true, false);
    }
}
