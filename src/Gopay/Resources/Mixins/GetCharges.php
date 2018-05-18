<?php

namespace Gopay\Resources\Mixins;

use DateTime;
use Gopay\Enums\AppTokenMode;
use Gopay\Enums\Currency;
use Gopay\Enums\CursorDirection;
use Gopay\Resources\Charge;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\RequesterUtils;

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
            "from" => isset($from) ? $from->format(DateTime::ATOM) : $from,
            "to" => isset($to) ? $to->format(DateTime::ATOM) : $to,
            "email" => $email,
            "phone" => $phone,
            "amount_from" => $amountFrom,
            "amount_to" => $amountTo,
            "currency" => isset($currency) ? $currency->getValue() : $currency,
            "metadata" => $metadata,
            "mode" => isset($mode) ? $mode->getValue() : $mode,
            "transaction_token_id" => $transactionTokenId,
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => isset($cursorDirection) ? $cursorDirection->getValue() : $cursorDirection
        ));
        return RequesterUtils::executeGetPaginated(Charge::class, $context, $query);
    }
}
