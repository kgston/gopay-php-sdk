<?php

namespace Gopay\Requests;


interface Requester
{

    public function get(RequestContext $requestContext, array $query = array(), array $headers = array());

    public function post(RequestContext $requestContext, array $payload = array(), array $headers = array());

    public function patch(RequestContext $requestContext, array $payload = array(), array $headers = array());

    public function delete(RequestContext $requestContext, array $headers = array());

}