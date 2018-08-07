<?php

namespace Gopay\Resources\Mixins;

use Gopay\Enums\CursorDirection;
use Gopay\Resources\BankAccount;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\OptionsValidator;
use Gopay\Utility\RequesterUtils;

trait GetBankAccounts
{
    use OptionsValidator;
    
    protected abstract function getBankAccountContext();

    public function listBankAccounts(
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
            BankAccount::class,
            $this->getBankAccountContext(),
            $query
        );
    }

    /**
     * @param array $opts See listBankAccounts parameters for valid opts keys
     */
    public function listBankAccountContextsByOptions(array $opts = [])
    {
        $rules = [
            'cursor_direction' => 'ValidationHelper::getEnumValue',
        ];

        $query = $this->validate(FunctionalUtils::stripNulls($opts), $rules);
        return RequesterUtils::executeGetPaginated(
            BankAccount::class,
            $this->getBankAccountContext(),
            $query
        );
    }
}
