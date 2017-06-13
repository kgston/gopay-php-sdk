<?php

namespace Gopay\Errors;

use Exception;

class GopayError extends Exception {}

class GopayRequestError extends GopayError {

    public $status;

    public $code;

    public $errors;

    function __construct($status, $code, $errors){
        $this->status = $status;
        $this->code = $code;
        $this->errors = $errors;
    }

    static function from_json($json) {
        return new GopayError(
            $json["status"],
            $json["code"],
            $json["errors"]
        );
    }
}

class GopayUnauthorizedError extends GopayError {}

class GopayServerError extends GopayError {}

class GopayNotFoundError extends GopayError {}

class GopayNoMoreItemsError extends GopayError {}