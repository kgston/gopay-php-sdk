<?php

use Gopay\Requests\RequestContext;
use Gopay\Requests\Requester;
use Gopay\Resources\Paginated;

function execute_get(Requester $requester,
                  $parser,
                  RequestContext $requestContext,
                  array $query = array()) {
    return $parser::fromJson($requester->get($requestContext, $query), $requestContext);
}

function execute_get_paginated(Requester $requester,
                               $parser,
                               RequestContext $context,
                               array $query = array()) {
    $response = $requester->get($context, $query);
    return Paginated::fromResponse($response, $query, $parser, $context, $requester);
}

function execute_post(Requester $requester,
                      $parser,
                      RequestContext $requestContext,
                      array $payload = array()) {
    return $parser::fromJson($requester->post($requestContext, $payload), $requestContext);
}

function execute_patch(Requester $requester,
                       $parser,
                       RequestContext $requestContext,
                       array $payload = array()) {
    return $parser::fromJson($requester->patch($requestContext, $payload), $requestContext);
}