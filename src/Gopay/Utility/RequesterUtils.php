<?php

namespace Gopay\Utility;

use Gopay\Requests\RequestContext;
use Gopay\Requests\Requester;
use Gopay\Resources\Paginated;

abstract class RequesterUtils {

    public static function getHeaders(RequestContext $requestContext, array $headers = array()) {
        return array_merge(
            HttpUtils::add_json_header($requestContext->getAuthorizationHeaders()),
            $headers
        );
    }

    public static function execute_get($parser, RequestContext $requestContext, array $query = array()) {
        $response = $requestContext->getRequester()->get($requestContext->getFullURL(), $query, self::getHeaders($requestContext));
        return $parser::getSchema()->parse($response, array($requestContext));
    }

    public static function execute_get_paginated($parser, RequestContext $context, array $query = array()) {
        $response = $context->getRequester()->get($context->getFullURL(), $query, self::getHeaders($context));
        return Paginated::fromResponse($response, $query, $parser, $context);
    }

    public static function execute_post($parser,
                                        RequestContext $requestContext,
                                        array $payload = array()) {
        $response = $requestContext->getRequester()->post($requestContext->getFullURL(), $payload, self::getHeaders($requestContext));
        return $parser::getSchema()->parse($response, array($requestContext));
    }

    public static function execute_patch($parser,
                                         RequestContext $requestContext,
                                         array $payload = array()) {
        $response = $requestContext->getRequester()->patch($requestContext->getFullURL(), $payload, self::getHeaders($requestContext));
        return $parser::getSchema()->parse($response, array($requestContext));
    }

}
