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
        return $parser::fromJson($requester->get($requestContext, $query), $requestContext);
    }

    public static function execute_get_paginated(Requester $requester,
                                   $parser,
                                   RequestContext $context,
                                   array $query = array()) {
        $response = $requester->get($context, $query);
        $actualParser = function ($response) use ($context, $parser) {
          return $parser::fromJson($response, $context);
        };
        return Paginated::fromResponse($response, $query, $actualParser, $context, $requester);
    }

    public static function execute_post(Requester $requester,
                          $parser,
                          RequestContext $requestContext,
                          array $payload = array()) {
        return $parser::fromJson($requester->post($requestContext, $payload), $requestContext);
    }

    public static function execute_patch(Requester $requester,
                           $parser,
                           RequestContext $requestContext,
                           array $payload = array()) {
        return $parser::fromJson($requester->patch($requestContext, $payload), $requestContext);
    }

}
