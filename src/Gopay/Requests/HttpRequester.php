<?php

namespace Gopay\Requests;


use Gopay\Utility\HttpUtils;
use Requests;

class HttpRequester implements Requester
{

    private function getHeaders(RequestContext $requestContext, array $headers) {
        return array_merge(
            HttpUtils::add_json_header($requestContext->getAuthorizationHeaders()),
            $headers
        );
    }

    public function get(RequestContext $requestContext, array $query = array(), array $headers = array())
    {
        $url = $requestContext->getFullURL();
        if (is_array($query) && sizeof($query) > 0) {
            $url .= "?" . HttpUtils::get_query_string($query);
        }
        return HttpUtils::check_response(
            Requests::get($url, $this->getHeaders($requestContext, $headers))
        );
    }

    public function post(RequestContext $requestContext, array $payload = array(), array $headers = array())
    {
        return HttpUtils::check_response(
            Requests::post($requestContext->getFullURL(), $this->getHeaders($requestContext, $headers), $payload)
        );
    }

    public function patch(RequestContext $requestContext, array $payload = array(), array $headers = array())
    {
        return HttpUtils::check_response(
            Requests::patch($requestContext->getFullURL(), $this->getHeaders($requestContext, $headers), $payload)
        );
    }

    public function delete(RequestContext $requestContext, array $headers = array())
    {
        return HttpUtils::check_response(
            Requests::delete($requestContext->getFullURL(), $this->getHeaders($requestContext, $headers))
        );
    }
}