<?php

namespace Gopay\Errors;

use Throwable;

class GopayUnauthorizedError extends GopayError
{
    public function __construct($url = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("Unauthorized to request resource " . $url, $code, $previous);
    }
}
