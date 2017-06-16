<?php

namespace Gopay\Requests;


use Gopay\Utility\HttpUtils;
use Requests;

class HttpRequester implements Requester
{

    public function get($url, array $query = array(), array $headers = array())
    {
        if (is_array($query) && sizeof($query) > 0) {
            $url .= "?" . HttpUtils::get_query_string($query);
        }
        return HttpUtils::check_response(Requests::get($url, $headers));
    }

    public function post($url, array $payload = array(), array $headers = array())
    {
        return HttpUtils::check_response(Requests::post($url, $headers, $payload));
    }

    public function patch($url, array $payload = array(), array $headers = array())
    {
        echo($url);
        return HttpUtils::check_response(Requests::patch($url, $headers, $payload));
    }

    public function delete($url, array $headers = array())
    {
        return HttpUtils::check_response(Requests::delete($url, $headers));
    }
}