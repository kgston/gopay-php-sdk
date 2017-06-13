<?php

namespace Gopay\Utility;

use Gopay\Errors;
use Gopay\Errors\GopayNotFoundError;

const BAD_REQUEST = 400;

const UNAUTHORIZED = 401;

const FORBIDDEN = 403;

const NOT_FOUND = 404;

const INTERNAL_SERVER_ERROR = 500;

function get_query_string(array $params) {
    if (is_array($params) && sizeof($params) > 0) {
        return "?" . http_build_query($params);
    } else {
        return "?";
    }
}

function check_response($response) {
    switch($response->statusCode) {
        case BAD_REQUEST:
             throw Errors\GopayRequestError::from_json(json_decode($response->body));

        case UNAUTHORIZED:
            throw new Errors\GopayUnauthorizedError();

        case FORBIDDEN:
            throw new Errors\GopayUnauthorizedError();

        case NOT_FOUND:
            throw new Errors\GopayNotFound();

        default:
            if ($response->body) {
                return json_decode($response->body);
            } else {
                return;
            }
    }
}

function add_json_header(array $headers) {
    return array_merge(array("Accept" => "application/json"), $headers);
}