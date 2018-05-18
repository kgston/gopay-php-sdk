<?php

namespace Gopay\Errors;

use Gopay\Enums\Reason;

class GopayLogicError extends GopayRequestError
{
    public function __construct(Reason $reason)
    {
        parent::__construct('error', $reason->getValue(), null);
    }
}
