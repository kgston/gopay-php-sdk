<?php
/**
 * Created by PhpStorm.
 * User: adamsar
 * Date: 6/19/17
 * Time: 6:40 PM
 */

namespace Gopay\Resources\Mixins;


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
            "cursor" => $cursor,
            "limit" => $limit,
            "cursorDirection" => $cursorDirection
        ));
        $context = $this->getTransactionContext();
        $response = $context->getRequester()->get($context->getFullURL(), $query, RequesterUtils::getHeaders($context));
        return Transaction::getSchema()->parse($response);
    }

}