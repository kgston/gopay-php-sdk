<?php

namespace Gopay\Errors;

use Throwable;

class GopayInvalidWebhookData extends GopayError
{

    public function __construct($payload, $code = 0, Throwable $previous = null)
    {
        $payloadAsString = print_r($payload, true);
        parent::__construct("$payloadAsString is not valid webhook data", $code, $previous);
    }
}
