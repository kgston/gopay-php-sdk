<?php

namespace Gopay\Resources\Mixins;

use Gopay\Enums\CursorDirection;
use Gopay\Resources\Transfer;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\OptionsValidator;
use Gopay\Utility\RequesterUtils;

trait GetTransfers
{
    use OptionsValidator;
    
    protected abstract function getTransferContext();

    public function listTransfers(
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
            Transfer::class,
            $this->getTransferContext(),
            $query
        );
    }

    /**
     * @param array $opts See listTransfers parameters for valid opts keys
     */
    public function listTransfersByOptions(array $opts = [])
    {
        $rules = [
            'cursor_direction' => 'ValidationHelper::getEnumValue',
        ];

        $query = $this->validate(FunctionalUtils::stripNulls($opts), $rules);
        return RequesterUtils::executeGetPaginated(
            Transfer::class,
            $this->getTransferContext(),
            $query
        );
    }
}
