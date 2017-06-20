<?php
/**
 * Created by PhpStorm.
 * User: adamsar
 * Date: 6/16/17
 * Time: 1:57 PM
 */

namespace Gopay\Errors;

use Throwable;

class GopayUnauthorizedError extends GopayError
{
    public function __construct($url = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("Unauthorized to request resource " . $url, $code, $previous);
    }
}