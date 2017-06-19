<?php

namespace Gopay\Resources\Mixins;
use Gopay\Resources\Subscription;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\RequesterUtils;

trait GetSubscriptions
{

    protected abstract function getSubscriptionContext();

    public function listSubscriptions($search = NULL,
                                      $status = NULL,
                                      $mode = NULL,
                                      $cursor = NULL,
                                      $limit = NULL,
                                      $cursorDirection = NULL) {
        $query = FunctionalUtils::strip_nulls(array(
            "search" => $search,
            "status" => $status,
            "mode" => $mode,
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => $cursorDirection
        ));

        return RequesterUtils::execute_get_paginated(
            Subscription::class,
            $this->getSubscriptionContext(),
            $query
        );
    }

}