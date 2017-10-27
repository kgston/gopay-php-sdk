<?php

namespace Gopay\Errors;

use Throwable;

class GopayNotFoundError extends GopayError
{
    public function __construct($url, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Route " . $url . " not found", $code, $previous);
    }
}