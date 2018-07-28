<?php

namespace Gopay\Errors;

use Throwable;

class GopayForbiddenError extends GopayRequestError
{
    public function __construct($url = "", $json = [], $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            $url,
            $json["status"],
            $json["code"],
            $json["errors"]
        );
    }
}
