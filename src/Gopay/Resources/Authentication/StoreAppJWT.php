<?php

namespace Gopay\Resources\Authentication;

use Gopay\Resources\Jsonable;
use Gopay\Utility\Json\JsonSchema;

class StoreAppJWT extends AppJWT
{
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

    public function __construct(
        $sub,
        $iat,
        $merchantId,
        $storeId,
        $domains,
        $mode,
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
        $this->storeId = $storeId;
        $this->domains = $domains;
        $this->mode = $mode;
        $this->creatorId = $creatorId;
        $this->version = $version;
        $this->jti = $jti;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(StoreAppJWT::class, true, false);
    }
}
