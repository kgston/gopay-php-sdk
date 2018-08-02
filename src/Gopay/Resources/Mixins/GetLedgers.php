<?php

namespace Gopay\Resources\Mixins;

use Gopay\Enums\CursorDirection;
use Gopay\Resources\Ledger;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\OptionsValidator;
use Gopay\Utility\RequesterUtils;

trait GetLedgers
{
    use OptionsValidator;
    
    protected abstract function getLedgerContext();

    public function listLedgers(
        $cursor = null,
        $limit = null,
        CursorDirection $cursorDirection = null
    ) {
        $query = FunctionalUtils::stripNulls([
            'cursor' => $cursor,
            'limit' => $limit,
            'cursor_direction' => $cursorDirection == null ? $cursorDirection : $cursorDirection->getValue()
        ]);
        return RequesterUtils::executeGetPaginated(
            Ledger::class,
            $this->getLedgerContext(),
            $query
        );
    }

    /**
     * @param array $opts See listLedgers parameters for valid opts keys
     */
    public function listLedgersByOptions(array $opts = [])
    {
        $rules = [
            'cursor_direction' => 'ValidationHelper::getEnumValue',
        ];

        $query = $this->validate(FunctionalUtils::stripNulls($opts), $rules);
        return RequesterUtils::executeGetPaginated(
            Ledger::class,
            $this->getLedgerContext(),
            $query
        );
    }
}
