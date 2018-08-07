<?php

namespace Gopay\Resources\Mixins;

use Gopay\Enums\CursorDirection;
use Gopay\Resources\Refund;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\OptionsValidator;
use Gopay\Utility\RequesterUtils;

trait GetRefunds
{
    use OptionsValidator;
    
    protected abstract function getRefundContext();

    public function listRefunds(
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
            Refund::class,
            $this->getRefundContext(),
            $query
        );
    }

    /**
     * @param array $opts See listRefunds parameters for valid opts keys
     */
    public function listRefundsByOptions(array $opts = [])
    {
        $rules = [
            'cursor_direction' => 'ValidationHelper::getEnumValue',
        ];

        $query = $this->validate(FunctionalUtils::stripNulls($opts), $rules);
        return RequesterUtils::executeGetPaginated(
            Refund::class,
            $this->getRefundContext(),
            $query
        );
    }
}
