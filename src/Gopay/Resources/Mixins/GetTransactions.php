<?php

namespace Gopay\Resources\Mixins;

use Gopay\Resources\Paginated;
use Gopay\Resources\Transaction;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\RequesterUtils;

trait GetTransactions
{

    protected abstract function getTransactionContext();

    public function listTransactions(
        $from = null,
        $to = null,
        $status = null,
        $type = null,
        $search = null,
        $mode = null,
        $gatewayCredentialsId = null,
        $gatewayTransactionId = null,
        $cursor = null,
        $limit = null,
        $cursorDirection = null
    ) {
        $query = FunctionalUtils::stripNulls(array(
            "from" => $from,
            "to" => $to,
            "status" => $status,
            "type" => $type,
            "search" => $search,
            "mode" => $mode,
            "cursor" => $cursor,
            "limit" => $limit,
            "cursorDirection" => $cursorDirection
        ));
        $context = $this->getTransactionContext();
        $response = $context->getRequester()->get($context->getFullURL(), $query, RequesterUtils::getHeaders($context));
        return Paginated::fromResponse($response, $query, Transaction::class, $context);
    }
}
