<?php

namespace Gopay;

use Composer\DependencyResolver\Request;
use DateTime;
use DateTimeZone;
use Exception;
use Gopay\Enums\CursorDirection;
use Gopay\Enums\Field;
use Gopay\Enums\PaymentType;
use Gopay\Enums\Period;
use Gopay\Enums\Reason;
use Gopay\Enums\TokenType;
use Gopay\Enums\WebhookEvent;
use Gopay\Errors\GopaySDKError;
use Gopay\Errors\GopayInvalidWebhookData;
use Gopay\Errors\GopayUnknownWebhookEvent;
use Gopay\Errors\GopayValidationError;
use Gopay\Requests\HttpRequester;
use Gopay\Requests\RequestContext;
use Gopay\Requests\Requester;
use Gopay\Resources\Authentication\AppJWT;
use Gopay\Resources\BankAccount;
use Gopay\Resources\CardConfiguration;
use Gopay\Resources\Charge;
use Gopay\Resources\CheckoutInfo;
use Gopay\Resources\Merchant;
use Gopay\Resources\Mixins\GetBankAccounts;
use Gopay\Resources\Mixins\GetCharges;
use Gopay\Resources\Mixins\GetStores;
use Gopay\Resources\Mixins\GetSubscriptions;
use Gopay\Resources\Mixins\GetTransactions;
use Gopay\Resources\Mixins\GetTransactionTokens;
use Gopay\Resources\Mixins\GetTransfers;
use Gopay\Resources\PaymentMethod\PaymentMethod;
use Gopay\Resources\InstallmentPlan;
use Gopay\Resources\Refund;
use Gopay\Resources\ScheduleSettings;
use Gopay\Resources\ScheduledPayment;
use Gopay\Resources\Store;
use Gopay\Resources\Subscription;
use Gopay\Resources\Transaction;
use Gopay\Resources\TransactionToken;
use Gopay\Resources\Transfer;
use Gopay\Resources\WebhookPayload;
use Gopay\Utility\FunctionalUtils;
use Gopay\Utility\HttpUtils;
use Gopay\Utility\RequesterUtils;
use Money\Money;

class GopayClient
{
    use GetBankAccounts, GetCharges, GetStores, GetSubscriptions, GetTransactions, GetTransactionTokens, GetTransfers {
        GetCharges::validate insteadof GetBankAccounts,
        GetStores,
        GetSubscriptions,
        GetTransactions,
        GetTransactionTokens,
        GetTransfers;
    }

    private $endpoint;
    private $storeAppJWT;
    private $merchantAppJWT;
    private $requester;

    public function __construct(
        AppJWT $storeAppJWT = null,
        AppJWT $merchantAppJWT = null,
        $endpoint = 'https://api.gopay.jp'
    ) {
        if (!isset($storeAppJWT) && !isset($merchantAppJWT)) {
            throw new GopaySDKError(Reason::REQUIRES_APP_TOKEN());
        }
        $this->endpoint = $endpoint;
        $this->storeAppJWT = $storeAppJWT;
        $this->merchantAppJWT = $merchantAppJWT;
        $this->requester = new HttpRequester();
    }

    public function getContext($storeId = null)
    {
        if (isset($storeId) && isset($this->storeAppJWT) && $storeId === $this->storeAppJWT->storeId) {
            return $this->getStoreBasedContext();
        } elseif (isset($merchantAppJWT)) {
            return $this->getMerchantBasedContext();
        }
        return $this->getStoreBasedContext();
    }

    public function getStoreBasedContext()
    {
        if (!isset($this->storeAppJWT)) {
            throw new GopaySDKError(Reason::REQUIRES_STORE_APP_TOKEN());
        }
        return new RequestContext($this->requester, $this->endpoint, '/', $this->storeAppJWT);
    }

    public function getMerchantBasedContext()
    {
        if (!isset($this->merchantAppJWT)) {
            throw new GopaySDKError(Reason::REQUIRES_MERCHANT_APP_TOKEN());
        }
        return new RequestContext($this->requester, $this->endpoint, '/', $this->merchantAppJWT);
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
            $this->getContext()->withPath('me')
        );
    }

    public function getCheckoutInfo()
    {
        return RequesterUtils::executeGet(
            CheckoutInfo::class,
            $this->getStoreBasedContext()->withPath('checkout_info')
        );
    }

    public function getStore($id)
    {
        $context = $this->getStoreContext()->appendPath($id);
        return RequesterUtils::executeGet(Store::class, $context);
    }

    public function getBankAccount($id)
    {
        $context = $this->getBankAccountContext()->appendPath($id);
        return RequesterUtils::executeGet(BankAccount::class, $context);
    }

    public function createToken(PaymentMethod $payment, $localCustomerId = null)
    {
        if (isset($localCustomerId) && $payment->type === TokenType::RECURRING()) {
            $customerId = $this->getCustomerId($localCustomerId);
            if (!isset($payment->metadata)) {
                $payment->metadata = [];
            }
            $payment->metadata += ['gopay-customer-id' => $customerId];
        }

        $context = $this->getStoreBasedContext()->withPath('tokens');
        return RequesterUtils::executePost(TransactionToken::class, $context, $payment);
    }

    public function getTransactionToken($transactionTokenId)
    {
        $context = $this->getStoreBasedContext()->withPath([
            'stores',
            $this->storeAppJWT->storeId,
            'tokens',
            $transactionTokenId
        ]);
        return RequesterUtils::executeGet(TransactionToken::class, $context);
    }

    public function createCharge(
        $transactionTokenId,
        Money $money,
        $capture = true,
        DateTime $captureAt = null,
        array $metadata = null
    ) {
        return $this
            ->getTransactionToken($transactionTokenId)
            ->createCharge(
                $money,
                $capture,
                $captureAt,
                $metadata
            );
    }

    public function getCharge($storeId, $chargeId)
    {
        $context = $this->getContext()->withPath(['stores', $storeId, 'charges', $chargeId]);
        return RequesterUtils::executeGet(Charge::class, $context);
    }

    public function createSubscription(
        $transactionTokenId,
        Money $money,
        Period $period,
        Money $initialAmount = null,
        ScheduleSettings $scheduleSettings = null,
        InstallmentPlan $installmentPlan = null,
        array $metadata = null
    ) {
        return $this
            ->getTransactionToken($transactionTokenId)
            ->createSubscription(
                $money,
                $period,
                $initialAmount,
                $scheduleSettings,
                $installmentPlan,
                $metadata
            );
    }

    public function getSubscription($storeId, $subscriptionId)
    {
        $context = $this->getContext()->withPath(['stores', $storeId, 'subscriptions', $subscriptionId]);
        return RequesterUtils::executeGet(Subscription::class, $context);
    }

    public function createSubscriptionSimulation(
        PaymentType $paymentType,
        Money $amount,
        Period $period,
        Money $initialAmount = null,
        ScheduleSettings $scheduleSettings = null,
        InstallmentPlan $installmentPlan = null
    ) {
        $payload = $amount->jsonSerialize() + [
            'payment_type' => $paymentType->getValue(),
            'period' => $period->getValue(),
            'schedule_settings' => $scheduleSettings,
            'installment_plan' => $installmentPlan,
        ];
        if (isset($initialAmount)) {
            if ($initialAmount->isNegative()) {
                throw new GopayValidationError(Field::INITIAL_AMOUNT(), Reason::INVALID_FORMAT());
            } else {
                $payload += $initialAmount->jsonSerialize();
            }
        }

        $context = $this->getStoreBasedContext()->appendPath(['subscriptions', 'simulate_plan']);
        return RequesterUtils::executePostSimpleList(
            ScheduledPayment::class,
            $context,
            FunctionalUtils::stripNulls($payload)
        );
    }

    public function getTransfer($id)
    {
        $context = $this->getTransferContext()->appendPath($id);
        return RequesterUtils::executeGet(Transfer::class, $context);
    }

    public function parseWebhookData($data)
    {
        try {
            $event = WebhookEvent::fromValue($data['event']);
            $parser = null;
            switch ($event) {
                case WebhookEvent::CHARGE_UPDATED():
                case WebhookEvent::CHARGE_FINISHED():
                    $parser = Charge::getContextParser($this->getChargeContext());
                    break;

                case WebhookEvent::SUBSCRIPTION_PAYMENT():
                case WebhookEvent::SUBSCRIPTION_COMPLETED():
                case WebhookEvent::SUBSCRIPTION_FAILURE():
                case WebhookEvent::SUBSCRIPTION_CANCELED():
                case WebhookEvent::SUBSCRIPTION_SUSPENDED():
                    $parser = Subscription::getContextParser($this->getSubscriptionContext());
                    break;
                
                case WebhookEvent::REFUND_FINISHED():
                    $parser = Refund::getContextParser($this->getStoreBasedContext());
                    break;

                case WebhookEvent::TRANSFER_CREATED():
                case WebhookEvent::TRANSFER_UPDATED():
                case WebhookEvent::TRANSFER_FINALIZED():
                    $parser = Transfer::getContextParser($this->getTransferContext());
                    break;

                case WebhookEvent::CANCEL_FINISHED():
                    $parser = Cancel::getContextParser($this->getStoreBasedContext());
                    break;
            }
            return new WebhookPayload($event, $parser($data['data']));
        } catch (OutOfRangeException $exception) {
            throw new GopayUnknownWebhookEvent($data['event']);
        } catch (Exception $exception) {
            throw new GopayInvalidWebhookData($data);
        }
    }

    protected function getBankAccountContext($storeId = null)
    {
        return $this->getContext($storeId)->withPath('bank_accounts');
    }

    protected function getCustomerId($localCustomerId)
    {
        return $this->getStore($this->storeAppJWT->storeId)->getCustomerId($localCustomerId);
    }

    protected function getChargeContext($storeId = null)
    {
        return $this->getContext($storeId)->withPath('charges');
    }

    protected function getStoreContext($storeId = null)
    {
        return $this->getContext($storeId)->withPath('stores');
    }

    protected function getSubscriptionContext($storeId = null)
    {
        return $this->getContext($storeId)->withPath('subscriptions');
    }

    protected function getTransactionTokenContext()
    {
        return $this->getStoreBasedContext()->withPath('tokens');
    }

    protected function getTransactionContext($storeId = null)
    {
        return $this->getContext($storeId)->withPath('transaction_history');
    }

    protected function getTransferContext($storeId = null)
    {
        return $this->getContext($storeId)->withPath('transfers');
    }
}
