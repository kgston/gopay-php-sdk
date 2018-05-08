<?php

namespace Gopay\Errors;

use Throwable;

class GopayNoMoreItemsError extends GopayError
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("No more items in list", $code, $previous);
    }
}
