<?php

namespace Gopay\Requests;

use Gopay\Utility\HttpUtils;
use Requests;

class HttpRequester implements Requester
{

    public function get($url, $query = array(), array $headers = array())
    {
        if (is_array($query) && sizeof($query) > 0) {
            $url .= HttpUtils::getQueryString($query);
        }
        return HttpUtils::checkResponse($url, Requests::get($url, $headers));
    }

    public function post($url, $payload = array(), array $headers = array())
    {
        return HttpUtils::checkResponse($url, Requests::post($url, $headers = $headers, $data = json_encode($payload)));
    }

    public function patch($url, $payload = array(), array $headers = array())
    {
        return HttpUtils::checkResponse($url, Requests::patch($url, $headers, json_encode($payload)));
    }

    public function delete($url, array $headers = array())
    {
        return HttpUtils::checkResponse($url, Requests::delete($url, $headers));
    }
}
