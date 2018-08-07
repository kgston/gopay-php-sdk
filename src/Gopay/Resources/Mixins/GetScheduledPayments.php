<?php

namespace Gopay\Resources\Mixins;

use Gopay\Enums\CursorDirection;
use Gopay\Resources\ScheduledPayment;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\OptionsValidator;
use Gopay\Utility\RequesterUtils;

trait GetScheduledPayments
{
    use OptionsValidator;
    
    protected abstract function getScheduledPaymentContext();

    public function listScheduledPayments(
        $cursor = null,
        $limit = null,
        CursorDirection $cursorDirection = null
    ) {
        $query = FunctionalUtils::stripNulls([
            'cursor' => $cursor,
            'limit' => $limit,
            'cursor_direction' => isset($cursorDirection) ? $cursorDirection->getValue() : null
        ]);

        return RequesterUtils::executeGetPaginated(
            ScheduledPayment::class,
            $this->getScheduledPaymentContext(),
            $query
        );
    }

    /**
     * @param array $opts See listScheduledPayments parameters for valid opts keys
     */
    public function listScheduledPaymentsByOptions(array $opts = [])
    {
        $rules = [
            'cursor_direction' => 'ValidationHelper::getEnumValue',
        ];

        $query = $this->validate(FunctionalUtils::stripNulls($opts), $rules);
        return RequesterUtils::executeGetPaginated(
            ScheduledPayment::class,
            $this->getScheduledPaymentContext(),
            $query
        );
    }
}
