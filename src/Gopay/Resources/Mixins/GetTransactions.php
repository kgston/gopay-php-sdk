<?php

namespace Gopay\Resources\Mixins;


use Gopay\Resources\Paginated;
use Gopay\Resources\Transaction;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\RequesterUtils;

trait GetTransactions
{

    protected abstract function getTransactionContext();

    public function listTransactions($from = NULL,
                                     $to = NULL,
                                     $status = NULL,
                                     $type = NULL,
                                     $search = NULL,
                                     $mode = NULL,
                                     $gatewayCredentialsId = NULL,
                                     $gatewayTransactionId = NULL,
                                     $cursor = NULL,
                                     $limit = NULL,
                                     $cursorDirection = NULL)
    {
        $query = FunctionalUtils::strip_nulls(array(
            "from" => $from,
            "to" => $to,
            "status" => $status,
            "type" => $type,
            "search" => $search,
            "mode" => $mode,
            "gateway_credentials_id" => $gatewayCredentialsId,
            "gateway_transaction_id" => $gatewayTransactionId,
            "cursor" => $cursor,
            "limit" => $limit,
            "cursorDirection" => $cursorDirection
        ));
        $context = $this->getTransactionContext();
        $response = $context->getRequester()->get($context->getFullURL(), $query, RequesterUtils::getHeaders($context));
        return Paginated::fromResponse($response, $query, Transaction::class, $context);
    }

}