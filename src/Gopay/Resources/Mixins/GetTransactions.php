<?php

namespace Gopay\Resources\Mixins;

use DateTime;
use Gopay\Enums\AppTokenMode;
use Gopay\Enums\CursorDirection;
use Gopay\Enums\ChargeStatus;
use Gopay\Resources\Paginated;
use Gopay\Resources\Transaction;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\OptionsValidator;
use Gopay\Utility\RequesterUtils;

trait GetTransactions
{
    use OptionsValidator;

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
        $query = FunctionalUtils::stripNulls([
            'from' => $from->getTimestamp() * 1000,
            'to' => $to->getTimestamp() * 1000,
            'status' => isset($status) ? $status->getValue() : null,
            'type' => isset($type) ? $type->getValue() : null,
            'search' => $search,
            'mode' => isset($mode) ? $mode->getValue() : null,
            'cursor' => $cursor,
            'limit' => $limit,
            'cursor_direction' => isset($cursorDirection) ? $cursorDirection.getValue() : null
        ]);
        
        return RequesterUtils::executeGetPaginated(Transaction::class, $this->getTransactionContext(), $query);
    }

    /**
     * @param array $opts See listTransactions parameters for valid opts keys
     */
    public function listTransactionsByOptions(array $opts = [])
    {
        $rules = [
            'from' => 'ValidationHelper::getAtomDate',
            'to' => 'ValidationHelper::getAtomDate',
            'status' => 'ValidationHelper::getEnumValue',
            'type' => 'ValidationHelper::getEnumValue',
            'mode' => 'ValidationHelper::getEnumValue',
            'cursor_direction' => 'ValidationHelper::getEnumValue',
        ];

        $query = $this->validate(FunctionalUtils::stripNulls($opts), $rules);
        return RequesterUtils::executeGetPaginated(Transaction::class, $this->getTransactionContext(), $query);
    }
}
