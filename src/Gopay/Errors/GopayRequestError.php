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
        parent::__construct(var_dump(array("status" => $status, "code" => $code, "errors" => $errors)));
    }

    static function from_json($json)
    {
        return new GopayRequestError(
            $json["status"],
            $json["code"],
            $json["errors"]
        );
    }
}