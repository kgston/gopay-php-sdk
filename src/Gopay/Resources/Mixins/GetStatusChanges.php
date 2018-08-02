<?php

namespace Gopay\Resources\Mixins;

use Gopay\Enums\CursorDirection;
use Gopay\Resources\TransferStatusChange;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\OptionsValidator;
use Gopay\Utility\RequesterUtils;

trait GetStatusChanges
{
    use OptionsValidator;
    
    protected abstract function getStatusChangeContext();

    public function listStatusChanges(
        $cursor = null,
        $limit = null,
        CursorDirection $cursorDirection = null
    ) {
        $query = FunctionalUtils::stripNulls([
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => $cursorDirection == null ? $cursorDirection : $cursorDirection->getValue()
        ]);
        return RequesterUtils::executeGetPaginated(
            TransferStatusChange::class,
            $this->getStatusChangeContext(),
            $query
        );
    }

    /**
     * @param array $opts See listStatusChanges parameters for valid opts keys
     */
    public function listStatusChangesByOptions(array $opts = [])
    {
        $rules = [
            'cursor_direction' => 'ValidationHelper::getEnumValue',
        ];

        $query = $this->validate(FunctionalUtils::stripNulls($opts), $rules);
        return RequesterUtils::executeGetPaginated(
            TransferStatusChange::class,
            $this->getStatusChangeContext(),
            $query
        );
    }
}
