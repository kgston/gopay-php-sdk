<?php

namespace Gopay\Requests;

interface Requester
{

    public function get($url, $query = array(), array $headers = array());

    public function post($url, $payload = array(), array $headers = array());

    public function patch($url, $payload = array(), array $headers = array());

    public function delete($url, array $headers = array());
}
