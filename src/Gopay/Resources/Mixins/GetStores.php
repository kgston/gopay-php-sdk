<?php

namespace Gopay\Resources\Mixins;

use Gopay\Enums\CursorDirection;
use Gopay\Resources\Store;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\OptionsValidator;
use Gopay\Utility\RequesterUtils;

trait GetStores
{
    use OptionsValidator;
    
    protected abstract function getStoreContext();

    public function listStores(
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
            Store::class,
            $this->getCancelContext(),
            $query
        );
    }

    /**
     * @param array $opts See listStores parameters for valid opts keys
     */
    public function listStoresByOptions(array $opts = [])
    {
        $rules = [
            'cursor_direction' => 'ValidationHelper::getEnumValue',
        ];

        $query = $this->validate(FunctionalUtils::stripNulls($opts), $rules);
        return RequesterUtils::executeGetPaginated(
            Store::class,
            $this->getStoreContext(),
            $query
        );
    }
}
