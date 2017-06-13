<?php

namespace Gopay\Requests;

class RequestContext
{
    private $path;
    private $endpoint;
    private $appToken;

    public function __construct($endpoint, $path, $appToken) {
        $this->path = $path;
        $this->endpoint = $endpoint;
        $this->appToken = $appToken;
    }

    public function withAppToken($appToken) {
        return new RequestContext($this->endpoint, $this->path, $appToken);
    }

    public function withPath($path) {
        return new RequestContext($this->endpoint, $this->path, $this->appToken);
    }

    public function appendPath($path) {
        if (is_array($path)) {
            return $this->withPath($this->path . "/" . $path.join("/"));
        } else if (is_string($path)) {
            return $this->withPath($this->path . "/" . $path);
        } else {
            return $this;
        }
    }

    public function getAuthorizationHeaders() {
        if ($this->appToken == NULL) {
            return array();
        } else {
            $key = $this->appToken->key;
            $secretText = $this->appToken->secret ? "|" . $this->appToken->secret : "";
            return array(
                "Authorization" => "ApplicationToken $key$secretText"
            );
        }
    }

    public function getFullURL() {
        return ($this->endpoint.rtrim("/") .
                "/" .
                $this->path.ltrim("/"));
    }

}