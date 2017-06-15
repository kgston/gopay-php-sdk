<?php

namespace Gopay\Requests;

class RequestContext
{
    private $path;
    private $endpoint;
    private $appToken;
    private $appSecret;

    public function __construct($endpoint, $path, $appToken, $appSecret) {
        $this->path = $path;
        $this->endpoint = $endpoint;
        $this->appToken = $appToken;
        $this->appSecret = $appSecret;
    }

    public function withAppToken($appToken, $appSecret) {
        return new RequestContext($this->endpoint, $this->path, $appToken, $appSecret);
    }

    public function withPath($path) {
        $newPath = is_array($path) ? $path.join("/") : $path;
        return new RequestContext($this->endpoint, $newPath, $this->appToken, $this->appSecret);
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
            $key = $this->appToken;
            $secretText = $this->appSecret ? "|" . $this->appSecret : "";
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