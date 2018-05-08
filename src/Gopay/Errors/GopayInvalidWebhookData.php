<?php

namespace Gopay\Errors;

use Throwable;

class GopayInvalidWebhookData extends GopayError
{

    public function __construct($payload, $code = 0, Throwable $previous = null)
    {
        ob_start();
        var_dump($payload);
        $payloadAsString = ob_end_clean();
        parent::__construct($payloadAsString . " is not valid webhook data", $code, $previous);
    }
}
