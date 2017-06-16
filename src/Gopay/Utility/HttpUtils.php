<?php

namespace Gopay\Utility;

use Gopay\Errors\GopayNotFoundError;
use Gopay\Errors\GopayRequestError;
use Gopay\Errors\GopayUnauthorizedError;

const BAD_REQUEST = 400;

const UNAUTHORIZED = 401;

const FORBIDDEN = 403;

const NOT_FOUND = 404;

const INTERNAL_SERVER_ERROR = 500;

abstract class HttpUtils {


    public static function get_query_string(array $params) {
        if (is_array($params) && sizeof($params) > 0) {
            return "?" . http_build_query($params);
        } else {
            return "?";
        }
    }

    public static function check_response($response) {
        switch($response->status_code) {
            case BAD_REQUEST:
                throw GopayRequestError::from_json(json_decode($response->body));

            case UNAUTHORIZED:
                throw new GopayUnauthorizedError();

            case FORBIDDEN:
                throw new GopayUnauthorizedError();

            case NOT_FOUND:
                throw new GopayNotFoundError();

            default:
                if ($response->body) {
                    return json_decode($response->body, True);
                } else {
                    return True;
                }
        }
    }

    public static function add_json_header(array $headers) {
        return array_merge(array("Accept" => "application/json"), $headers);
    }

}
