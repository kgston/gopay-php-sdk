<?php

namespace Gopay\Requests;


interface Requester
{

    public function get($requestContext, $query = array(), $headers = array());

    public function post($requestContext, $payload = array(), $headers = array());

    public function patch($requestContext, $payload = array(), $headers = array());

    public function delete($requestContext, $headers = array());

}