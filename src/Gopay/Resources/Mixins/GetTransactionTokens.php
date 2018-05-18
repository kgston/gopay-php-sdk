<?php

namespace Gopay\Resources\Mixins;

use Gopay\Enums\ActiveFilter;
use Gopay\Enums\AppTokenMode;
use Gopay\Enums\CursorDirection;
use Gopay\Enums\Field;
use Gopay\Enums\Reason;
use Gopay\Enums\TokenType;
use Gopay\Enums\TransactionType;
use Gopay\Errors\GopayValidationError;
use Gopay\Resources\TransactionToken;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\RequesterUtils;

trait GetTransactionTokens
{
    protected abstract function getCustomerId($localCustomerId);

    protected abstract function getTransactionTokenContext();

    public function listTransactionTokens(
        $search = null,
        $localCustomerId = null,
        TokenType $type = null,
        AppTokenMode $mode = null,
        ActiveFilter $active = null,
        $cursor = null,
        $limit = null,
        CursorDirection $cursorDirection = null
    ) {
        if (isset($type) && $type === TokenType::ONE_TIME()) {
            throw new GopayValidationError(Field::Type(), Reason::INVALID_TOKEN_TYPE());
        }

        $gopayCustomerId = isset($localCustomerId) ? $this->getCustomerId($localCustomerId) : null;
        $context = $this->getTransactionTokenContext();
        $query = FunctionalUtils::stripNulls(array(
            "search" => $search,
            "active" => isset($active) ? $active->getValue() : $active,
            "customer_id" => $gopayCustomerId,
            "type" => isset($type) ? $type->getValue() : $type,
            "mode" => isset($mode) ? $mode->getValue() : $mode,
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => isset($cursorDirection) ? $cursorDirection->getValue() : $cursorDirection
        ));
        return RequesterUtils::executeGetPaginated(TransactionToken::class, $context, $query);
    }
}
