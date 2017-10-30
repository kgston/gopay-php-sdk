<?php

namespace Gopay\Resources\Mixins;


use Gopay\Resources\Charge;
use Gopay\Utility\RequesterUtils;

trait GetCharges
{

    protected abstract function getChargeContext();

    public function listCharges($lastFour=NULL,
                                $name=NULL,
                                $expMonth=NULL,
                                $expYear=NULL,
                                $cardNumber=NULL,
                                $from=NULL,
                                $to=NULL,
                                $email=NULL,
                                $phone=NULL,
                                $amountFrom=NULL,
                                $amountTo=NULL,
                                $currency=NULL,
                                $metadata=NULL,
                                $mode=NULL,
                                $transactionTokenId=NULL,
                                $gatewayCredentialsId=NULL,
                                $gatewayTransactionId=NULL,
                                $cursor=NULL,
                                $limit=NULL,
                                $cursorDirection=NULL) {
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
        return RequesterUtils::execute_get_paginated(Charge::class, $context, $query);
    }

}