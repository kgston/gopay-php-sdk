<?php

namespace Gopay;

use Composer\DependencyResolver\Request;
use Exception;
use Gopay\Enums\Period;
use Gopay\Errors\GopayInvalidWebhookData;
use Gopay\Errors\GopayUnknownWebhookEvent;
use Gopay\Requests\HttpRequester;
use Gopay\Requests\RequestContext;
use Gopay\Requests\Requester;
use Gopay\Resources\Authentication\AppJWT;
use Gopay\Resources\BankAccount;
use Gopay\Resources\CardConfiguration;
use Gopay\Resources\Charge;
use Gopay\Resources\CheckoutInfo;
use Gopay\Resources\Merchant;
use Gopay\Resources\Mixins\GetCharges;
use Gopay\Resources\Mixins\GetSubscriptions;
use Gopay\Resources\Mixins\GetTransactions;
use Gopay\Resources\Refund;
use Gopay\Resources\Store;
use Gopay\Resources\Subscription;
use Gopay\Resources\Transaction;
use Gopay\Resources\TransactionToken;
use Gopay\Resources\Transfer;
use Gopay\Resources\WebhookPayload;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\HttpUtils;
use Gopay\Utility\RequesterUtils;

class GopayClient
{
    use GetSubscriptions;
    use GetTransactions;
    use GetCharges;

    private $endpoint;
    private $storeAppJWT;
    private $merchantAppJWT;
    private $requester;

    public function __construct(
        AppJWT $storeAppJWT = null,
        AppJWT $merchantAppJWT = null,
        $endpoint = "https://api.gopay.jp"
    ) {
        $this->endpoint = $endpoint;
        $this->storeAppJWT = $storeAppJWT;
        $this->merchantAppJWT = $merchantAppJWT;
        $this->requester = new HttpRequester();
    }

    public function getStoreBasedContext()
    {
        return new RequestContext($this->requester, $this->endpoint, "/", $this->storeAppJWT);
    }

    public function getMerchantBasedContext()
    {
        return new RequestContext($this->requester, $this->endpoint, "/", $this->merchantAppJWT);
    }

    public function withRequester(Requester $requester)
    {
        $this->requester = $requester;
        return $this;
    }

    public function getMe()
    {
        return RequesterUtils::executeGet(
            Merchant::class,
            $this->getStoreBasedContext()->withPath("me")
        );
    }

    public function getCheckoutInfo()
    {
        return RequesterUtils::executeGet(
            CheckoutInfo::class,
            $this->getStoreBasedContext()->withPath("checkout_info")
        );
    }

    public function listStores(
        $cursor = null,
        $limit = null,
        $cursorDirection = null
    ) {
        $query = FunctionalUtils::stripNulls(array(
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => $cursorDirection
        ));
        return RequesterUtils::executeGetPaginated(
            Store::class,
            $this->getStoreContext(),
            $query
        );
    }

    public function getStore($id)
    {
        $context = $this->getStoreContext()->appendPath($id);
        return RequesterUtils::executeGet(Store::class, $context);
    }

    public function listBankAccounts(
        $cursor = null,
        $limit = null,
        $cursorDirection = null
    ) {
        $query = FunctionalUtils::stripNulls(array(
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => $cursorDirection
        ));
        $context = $this->getBankAccountContext();
        return RequesterUtils::executeGetPaginated(BankAccount::class, $context, $query);
    }

    public function getBankAccount($id)
    {
        $context = $this->getBankAccountContext()->appendPath($id);
        return RequesterUtils::executeGet(BankAccount::class, $context);
    }

    public function createCardToken(
        $email,
        $cardholder,
        $cardNumber,
        $expMonth,
        $expYear,
        $cvv,
        $type = "one_time",
        $usageLimit = null,
        $line1 = null,
        $line2 = null,
        $state = null,
        $city = null,
        $country = null,
        $zip = null,
        $phoneNumberCountryCode = null,
        $phoneNumberLocalNumber = null
    ) {
        $context = $this->getStoreBasedContext()->withPath("tokens");
        $data = array(
            "cardholder" => $cardholder,
            "card_number" => $cardNumber,
            "exp_month" => $expMonth,
            "exp_year" => $expYear,
            "cvv" => $cvv
        );
        if ($line1 &
            $state &&
            $city &&
            $country &&
            $zip &&
            $phoneNumberCountryCode &&
            $phoneNumberLocalNumber) {
            $data = array_merge($data, array(
                "line1" => $line1,
                "line2" => $line2,
                "state" => $state,
                "city" => $city,
                "country" => $country,
                "zip" => $zip,
                "phone_number" => array(
                    "country_code" => $phoneNumberCountryCode,
                    "local_number" => $phoneNumberLocalNumber
            )));
        }

        $payload = array(
            "payment_type" => "card",
            "type" => $type,
            "usage_limit" => $usageLimit,
            "email" => $email,
            "data" => $data
        );
        return RequesterUtils::executePost(TransactionToken::class, $context, $payload);
    }

    public function getTransactionToken($storeId, $transactionTokenId)
    {
        $context = $this->getStoreBasedContext()->withPath(array("stores", $storeId, "tokens", $transactionTokenId));
        return RequesterUtils::executeGet(TransactionToken::class, $context);
    }

    public function createCharge(
        $transactionTokenId,
        $amount,
        $currency,
        $capture = true,
        $captureAt = null,
        $metadata = null
    ) {
        $payload = array(
            'transaction_token_id' => $transactionTokenId,
            'amount' => $amount,
            'currency' => $currency
        );
        if ($metadata != null) {
            $payload = array_merge(array("metadata" => $metadata), $payload);
        }
        if (!$capture) {
            $payload = array_merge($payload, array("capture" => "false"));
        }
        if ($captureAt != null) {
            $payload = array_merge($payload, array("capture_at" => $captureAt));
        }

        $context = $this->getChargeContext();
        return RequesterUtils::executePost(Charge::class, $context, $payload);
    }

    public function getCharge($storeId, $chargeId)
    {
        $context = $this->getStoreBasedContext()->withPath(array("stores", $storeId, "charges", $chargeId));
        return RequesterUtils::executeGet(Charge::class, $context);
    }

    public function createSubscription(
        $transactionTokenId,
        $amount,
        $currency,
        Period $period,
        $initialAmount = null,
        $subsequentCyclesStart = null,
        $installmentPlan = null,
        $metadata = null
    ) {
        $payload = array(
            'transaction_token_id' => $transactionTokenId,
            'amount' => $amount,
            'currency' => $currency,
            'period' => $period
        );
        if ($metadata != null) {
            $payload = array_merge(array("metadata" => $metadata), $payload);
        }
        if (!$initialAmount) {
            $payload = array_merge($payload, array("initial_amount" => $initialAmount));
        }
        if ($subsequentCyclesStart != null) {
            $payload = array_merge($payload, array("subsequent_cycles_start" => $subsequentCyclesStart));
        }

        $context = $this->getSubscriptionContext();
        return RequesterUtils::executePost(Subscription::class, $context, $payload);
    }

    public function getSubscription($storeId, $subscriptionId)
    {
        $context = $this->getStoreBasedContext()->withPath(array("stores", $storeId, "subscriptions", $subscriptionId));
        return RequesterUtils::executeGet(Subscription::class, $context);
    }

    public function listTransfers(
        $cursor = null,
        $limit = null,
        $cursorDirection = null
    ) {
        $query = FunctionalUtils::stripNulls(array(
            "cursor" => $cursor,
            "limit" => $limit,
            "cursor_direction" => $cursorDirection
        ));
        $context = $this->getTransferContext();
        return RequesterUtils::executeGetPaginated(Transfer::class, $context, $query);
    }

    public function getTransfer($id)
    {
        $context = $this->getTransferContext()->appendPath($id);
        return RequesterUtils::executeGet(Transfer::class, $context);
    }

    public function parseWebhookData($data)
    {
        try {
            $event = $data["event"];
            $parser = null;
            switch (strtolower($event)) {
                case "charge_updated":
                case "charge_finished":
                    $parser = Charge::getContextParser($this->getChargeContext());
                    break;

                case "subscription_payment":
                case "subscription_completed":
                case "subscription_failure":
                case "subscription_canceled":
                case "subscription_suspended":
                    $parser = Subscription::getContextParser($this->getSubscriptionContext());
                    break;

                case "refund_finished":
                    $parser = Refund::getContextParser($this->getStoreBasedContext());
                    break;

                case "transfer_created":
                case "transfer_updated":
                case "transfer_finalized":
                    $parser = Transfer::getContextParser($this->getTransferContext());
                    break;

                case "cancel_finished":
                    $parser = Cancel::getContextParser($this->getStoreBasedContext());
                    break;

                default:
                    throw new GopayUnknownWebhookEvent($event);
            }
            return new WebhookPayload($event, $parser($data["data"]));
        } catch (Exception $exception) {
            throw new GopayInvalidWebhookData($data);
        }
    }

    protected function getStoreContext()
    {
        return $this->getStoreBasedContext()->withPath("stores");
    }

    protected function getSubscriptionContext()
    {
        return $this->getStoreBasedContext()->withPath("subscriptions");
    }

    protected function getTransactionContext()
    {
        return $this->getStoreBasedContext()->withPath("transaction_history");
    }

    protected function getChargeContext()
    {
        return $this->getStoreBasedContext()->withPath("charges");
    }

    protected function getTransferContext()
    {
        return $this->getStoreBasedContext()->withPath("transfers");
    }

    protected function getBankAccountContext()
    {
        return $this->getStoreBasedContext()->withPath("bank_accounts");
    }
}
