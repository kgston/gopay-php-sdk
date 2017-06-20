<?php
/**
 * Created by PhpStorm.
 * User: adamsar
 * Date: 6/20/17
 * Time: 9:50 AM
 */

namespace Gopay\Errors;


use Throwable;

class GopayResourceConflictError extends GopayError
{
    public function __construct($url, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Resource conflict on " . $url, $code, $previous);
    }
}