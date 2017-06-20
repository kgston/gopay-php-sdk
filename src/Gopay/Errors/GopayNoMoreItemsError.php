<?php
/**
 * Created by PhpStorm.
 * User: adamsar
 * Date: 6/16/17
 * Time: 1:57 PM
 */

namespace Gopay\Errors;

use Throwable;

class GopayNoMoreItemsError extends GopayError
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("No more items in list", $code, $previous);
    }
}