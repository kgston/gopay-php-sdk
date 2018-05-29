<?php

namespace Gopay\Errors;

class GopayRequestError extends GopayError
{
    public $url;
    public $status;
    public $code;
    public $errors;

    public function __construct($url, $status, $code, $errors)
    {
        $this->url = $url;
        $this->status = $status;
        $this->code = $code;
        $this->errors = $errors;
        parent::__construct(var_dump(array('url' => $url, 'status' => $status, 'code' => $code, 'errors' => $errors)));
    }

    public static function fromJson($url, $json)
    {
        return new GopayRequestError(
            $url,
            $json["status"],
            $json["code"],
            $json["errors"]
        );
    }
}
