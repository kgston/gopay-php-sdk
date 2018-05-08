<?php

namespace Gopay\Requests;

interface Requester
{

    public function get($url, array $query = array(), array $headers = array());

    public function post($url, array $payload = array(), array $headers = array());

    public function patch($url, array $payload = array(), array $headers = array());

    public function delete($url, array $headers = array());
}
