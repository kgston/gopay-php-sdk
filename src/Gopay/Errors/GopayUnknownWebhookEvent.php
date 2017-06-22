<?php

namespace Gopay\Errors;


use Throwable;

class GopayUnknownWebhookEvent extends GopayError
{
    public function __construct($event, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Received unknown event " . $event , $code, $previous);
    }

}