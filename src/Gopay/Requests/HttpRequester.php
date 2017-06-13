<?php

namespace Gopay\Requests;

use function Gopay\Utility\add_json_header;
use function Gopay\Utility\check_response;
use function Gopay\Utility\get_query_string;
use Requests;

class HttpRequester implements Requester
{

    private function getHeaders(RequestContext $requestContext, array $headers) {
        return array_merge(
            add_json_header($requestContext->getAuthorizationHeaders()),
            $headers
        );
    }

    public function get(RequestContext $requestContext, array $query = array(), array $headers = array())
    {
        $url = $requestContext->getFullURL();
        if (is_array($query) && sizeof($query) > 0) {
            $url += "?" . get_query_string($query);
        }
        return check_response(
            Requests::get($url, $this->getHeaders($requestContext, $headers))
        );
    }

    public function post(RequestContext $requestContext, array $payload = array(), array $headers = array())
    {
        return check_response(
            Requests::post($requestContext->getFullURL(), $this->getHeaders(), $payload)
        );
    }

    public function patch(RequestContext $requestContext, array $payload = array(), array $headers = array())
    {
        return check_response(
            Requests::patch($requestContext->getFullURL(), $this->getHeaders(), $payload)
        );
    }

    public function delete(RequestContext $requestContext, array $headers = array())
    {
        return check_response(
            Requests::delete($requestContext->getFullURL(), $this->getHeaders())
        );
    }
}