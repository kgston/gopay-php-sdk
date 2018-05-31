<?php

namespace Gopay\Resources\Mixins;

use DateTime;
use Gopay\Enums\AppTokenMode;
use Gopay\Enums\CursorDirection;
use Gopay\Resources\Charge;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\RequesterUtils;
use Money\Currency;

trait GetCharges
{
    protected abstract function getChargeContext();

    public function listCharges(
        $lastFour = null,
        $name = null,
        $expMonth = null,
        $expYear = null,
        $cardNumber = null,
        DateTime $from = null,
        DateTime $to = null,
        $email = null,
        $phone = null,
        $amountFrom = null,
        $amountTo = null,
        Currency $currency = null,
        array $metadata = null,
        AppTokenMode $mode = null,
        $transactionTokenId = null,
        $gatewayCredentialsId = null,
        $gatewayTransactionId = null,
        $cursor = null,
        $limit = null,
        CursorDirection $cursorDirection = null
    ) {
        $context = $this->getChargeContext();
        $query = FunctionalUtils::stripNulls(array(
            "last_four" => $lastFour,
            "name" => $name,
            "exp_month" => $expMonth,
            "exp_year" => $expYear,
            "card_number" => $cardNumber,
            "from" => isset($from) ? $from->format(DateTime::ATOM) : null,
            "to" => isset($to) ? $to->format(DateTime::ATOM) : null,
            "email" => $email,
            "phone" => $phone,
            "amount_from" => $amountFrom,
            "amount_to" => $amountTo,
            "currency" => isset($currency) ? $currency->getCode() : null,
            "metadata" => $metadata,
            "mode" => isset($mode) ? $mode->getValue() : null,
            "transaction_token_id" => $transactionTokenId,
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => isset($cursorDirection) ? $cursorDirection->getValue() : null
        ));
        return RequesterUtils::executeGetPaginated(Charge::class, $context, $query);
    }
}
