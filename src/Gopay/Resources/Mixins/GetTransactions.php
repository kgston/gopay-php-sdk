<?php

namespace Gopay\Resources\Mixins;

use DateTime;
use Gopay\Enums\AppTokenMode;
use Gopay\Enums\CursorDirection;
use Gopay\Enums\ChargeStatus;
use Gopay\Resources\Paginated;
use Gopay\Resources\Transaction;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\RequesterUtils;

trait GetTransactions
{
    protected abstract function getTransactionContext();

    public function listTransactions(
        DateTime $from = null,
        DateTime $to = null,
        ChargeStatus $status = null,
        TransactionType $type = null,
        $search = null,
        AppTokenMode $mode = null,
        $gatewayCredentialsId = null,
        $gatewayTransactionId = null,
        $cursor = null,
        $limit = null,
        CursorDirection $cursorDirection = null
    ) {
        $query = FunctionalUtils::stripNulls(array(
            "from" => $from->getTimestamp() * 1000,
            "to" => $to->getTimestamp() * 1000,
            "status" => isset($status) ? $status->getValue() : null,
            "type" => isset($type) ? $type->getValue() : null,
            "search" => $search,
            "mode" => isset($mode) ? $mode->getValue() : null,
            "cursor" => $cursor,
            "limit" => $limit,
            "cursorDirection" => isset($cursorDirection) ? $cursorDirection.getValue() : null
        ));
        $context = $this->getTransactionContext();
        $response = $context->getRequester()->get($context->getFullURL(), $query, RequesterUtils::getHeaders($context));
        return Paginated::fromResponse($response, $query, Transaction::class, $context);
    }
}
