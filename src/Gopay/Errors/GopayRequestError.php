<?php
/**
 * Created by PhpStorm.
 * User: adamsar
 * Date: 6/16/17
 * Time: 1:57 PM
 */

namespace Gopay\Errors;

class GopayRequestError extends GopayError
{

    public $status;

    public $code;

    public $errors;

    function __construct($status, $code, $errors)
    {
        $this->status = $status;
        $this->code = $code;
        $this->errors = $errors;
    }

    static function from_json($json)
    {
        return new GopayError(
            $json["status"],
            $json["code"],
            $json["errors"]
        );
    }
}