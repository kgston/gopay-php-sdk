<?php

namespace Gopay\Resources\Mixins;

use Gopay\Enums\AppTokenMode;
use Gopay\Enums\CursorDirection;
use Gopay\Enums\SubscriptionStatus;
use Gopay\Resources\Subscription;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\RequesterUtils;

trait GetSubscriptions
{
    protected abstract function getSubscriptionContext();

    public function listSubscriptions(
        $search = null,
        SubscriptionStatus $status = null,
        AppTokenMode $mode = null,
        $cursor = null,
        $limit = null,
        CursorDirection $cursorDirection = null
    ) {
        $query = FunctionalUtils::stripNulls(array(
            "search" => $search,
            "status" => isset($status) ? $status->getValue() : $status,
            "mode" => isset($mode) ? $mode->getValue() : $mode,
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => isset($cursorDirection) ? $cursorDirection->getValue() : $cursorDirection
        ));

        return RequesterUtils::executeGetPaginated(
            Subscription::class,
            $this->getSubscriptionContext(),
            $query
        );
    }
}
