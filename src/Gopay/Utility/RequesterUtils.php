<?php

namespace Gopay\Utility;

use Gopay\Requests\RequestContext;
use Gopay\Requests\Requester;
use Gopay\Resources\Paginated;

abstract class RequesterUtils {

    public static function execute_get(Requester $requester,
                                       $parser,
                                       RequestContext $requestContext,
                                       array $query = array()) {
        return $parser::getSchema()->parse($requester->get($requestContext, $query), array($requestContext));
    }

    public static function execute_get_paginated(Requester $requester,
                                   $parser,
                                   RequestContext $context,
                                   array $query = array()) {
        $response = $requester->get($context, $query);
        return Paginated::fromResponse($response, $query, $parser::getSchema()->getParser(array($context)), $context, $requester);
    }

    public static function execute_post(Requester $requester,
                          $parser,
                          RequestContext $requestContext,
                          array $payload = array()) {
        return $parser::getSchema()->parse($requester->post($requestContext, $payload), array($requestContext));
    }

    public static function execute_patch(Requester $requester,
                           $parser,
                           RequestContext $requestContext,
                           array $payload = array()) {
        return $parser::getSchema()->parse($requester->patch($requestContext, $payload), array($requestContext));
    }

}
