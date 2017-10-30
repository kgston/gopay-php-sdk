<?php

namespace Gopay\Requests;

class RequestContext
{
    private $path;
    private $endpoint;
    private $appToken;
    private $appSecret;
    private $requester;



    public function __construct($requester, $endpoint, $path, $appToken, $appSecret) {
        $this->requester = $requester;
        $this->path = $path;
        $this->endpoint = $endpoint;
        $this->appToken = $appToken;
        $this->appSecret = $appSecret;
    }

    public function getRequester()
    {
        return $this->requester;
    }

    public function withAppToken($appToken, $appSecret) {
        return new RequestContext($this->requester, $this->endpoint, $this->path, $appToken, $appSecret);
    }

    public function withPath($path) {
        $newPath = is_array($path) ? join("/", $path) : $path;
        return new RequestContext($this->requester, $this->endpoint, $newPath, $this->appToken, $this->appSecret);
    }

    public function appendPath($path) {
        if (is_array($path)) {
            return $this->withPath($this->path . "/" . join("/", $path));
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
        return (trim($this->endpoint, "/") .
                "/" .
                trim($this->path, "/"));
    }

    public function getWebsocketURL() {
      $authHeaders = $this->getAuthorizationHeaders();
      $auth = str_replace(" ", ":", $authHeaders["Authorization"]);
      $path = preg_replace("/https?:\/\//", "", $this->getFullURL());
      return "ws://" . $auth . "@" . $path;
    }

}
