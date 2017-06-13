<?php

namespace Gopay\Requests;

use Requests;

class HttpRequester implements Requester
{

    public function get($requestContext, $query = array(), $headers = array())
    {
        $url = $requestContext->getFullURL();
        $response = Requests::get($requestContext->getFullURL());
    }

    public function post($requestContext, $payload = array(), $headers = array())
    {
        // TODO: Implement post() method.
    }

    public function patch($requestContext, $payload = array(), $headers = array())
    {
        // TODO: Implement patch() method.
    }

    public function delete($requestContext, $headers = array())
    {
        // TODO: Implement delete() method.
    }
}