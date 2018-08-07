<?php

namespace Gopay\Errors;

use Throwable;

class GopayServerError extends GopayError
{
    public function __construct($url, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Unexpected server error reached while requesting $url", $code, $previous);
    }
}
