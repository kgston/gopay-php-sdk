<?php

namespace Gopay\Errors;

use Gopay\Enums\Reason;

class GopaySDKError extends GopayError
{
    public function __construct(Reason $reason)
    {
        parent::__construct($reason->getValue());
    }
}
