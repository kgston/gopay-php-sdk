<?php

namespace Gopay\Utility;

use Gopay\Errors\GopayForbiddenError;
use Gopay\Errors\GopayNotFoundError;
use Gopay\Errors\GopayRequestError;
use Gopay\Errors\GopayResourceConflictError;
use Gopay\Errors\GopayUnauthorizedError;

const BAD_REQUEST = 400;

const UNAUTHORIZED = 401;

const FORBIDDEN = 403;

const NOT_FOUND = 404;

const INTERNAL_SERVER_ERROR = 500;

const CONFLICT = 409;

abstract class HttpUtils
{
    public static function getQueryString(array $params)
    {
        if (is_array($params) && sizeof($params) > 0) {
            return "?" . http_build_query($params);
        } else {
            return "?";
        }
    }

    public static function checkResponse($url, $response)
    {
        switch ($response->status_code) {
            case BAD_REQUEST:
                throw GopayRequestError::fromJson($url, json_decode($response->body, true));

            case UNAUTHORIZED:
                throw new GopayUnauthorizedError($url, json_decode($response->body, true));

            case FORBIDDEN:
                throw new GopayForbiddenError($url, json_decode($response->body, true));

            case NOT_FOUND:
                throw new GopayNotFoundError($url);

            case CONFLICT:
                throw new GopayResourceConflictError($url);

            default:
                if ($response->body) {
                    return json_decode($response->body, true);
                } else {
                    return true;
                }
        }
    }

    public static function addJsonHeader(array $headers)
    {
        return array_merge(array(
            "Accept" => "application/json",
            "Content-Type" => "application/json"
        ), $headers);
    }
}
