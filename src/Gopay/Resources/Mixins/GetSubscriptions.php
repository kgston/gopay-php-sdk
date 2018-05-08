<?php

namespace Gopay\Resources\Mixins;

use Gopay\Resources\Subscription;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\RequesterUtils;

trait GetSubscriptions
{

    protected abstract function getSubscriptionContext();

    public function listSubscriptions(
        $search = null,
        $status = null,
        $mode = null,
        $cursor = null,
        $limit = null,
        $cursorDirection = null
    ) {
        $query = FunctionalUtils::stripNulls(array(
            "search" => $search,
            "status" => $status,
            "mode" => $mode,
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => $cursorDirection
        ));

        return RequesterUtils::executeGetPaginated(
            Subscription::class,
            $this->getSubscriptionContext(),
            $query
        );
    }
}
