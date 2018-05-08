<?php

namespace Gopay\Resources\Mixins;

use Gopay\Resources\Charge;
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
        $from = null,
        $to = null,
        $email = null,
        $phone = null,
        $amountFrom = null,
        $amountTo = null,
        $currency = null,
        $metadata = null,
        $mode = null,
        $transactionTokenId = null,
        $gatewayCredentialsId = null,
        $gatewayTransactionId = null,
        $cursor = null,
        $limit = null,
        $cursorDirection = null
    ) {
        $context = $this->getChargeContext();
        $query = array(
            "last_four" => $lastFour,
            "name" => $name,
            "exp_month" => $expMonth,
            "exp_year" => $expYear,
            "card_number" => $cardNumber,
            "from" => $from,
            "to" => $to,
            "email" => $email,
            "phone" => $phone,
            "amount_from" => $amountFrom,
            "amount_to" => $amountTo,
            "currency" => $currency,
            "metadata" => $metadata,
            "mode" => $mode,
            "transaction_token_id" => $transactionTokenId,
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => $cursorDirection
        );
        return RequesterUtils::executeGetPaginated(Charge::class, $context, $query);
    }
}
