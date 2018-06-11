# Gyro-n Gopay PHP SDK

This PHP SDK provides a convienent way to integrate your services with the Gopay payments gateway.

## Requirements

- PHP >= 5.6
- Composer
- npm (dev only)
- Gopay store application token _and/or_ merchant application token

## Installation

```shell
composer require gopay-japan/gopay-sdk
```

## Usage

```php
use Gopay\GopayClient;

$client = new GopayClient(AppJWT::createToken('token', 'secret'));
// See the examples folder for more details
```

### Application Tokens

Both store and merchant type application tokens are supported by this SDK. Apart from creating transaction tokens and charges which require a store type token, all other features are supported by both token types.

### Money models
This SDK uses the `moneyphp` library to model amounts and currency. Please refer to the [documentation](http://moneyphp.org/en/latest/index.html) for more details.

```php
use Gopay\PaymentMethod\CardPayment;
use Money\Money;

$paymentMethod = new CardPayment(...);
$charge = $client
    ->createToken($paymentMethod)
    ->createCharge(Money::USD(1000));

$charge->currency === new Currency('USD'); // true
```

### Enumerators

As PHP has no native built in enumeration support, we use the class `TypedEnum` to provide type safety when working with enumerators. Each enumerator class is final and extends `TypedEnum` to provide static functions that operate similar to enumerators in other languages like Java. A enum classes can be found in the `Gopay\Enums` namespace.

```php
use Gopay\Enums\ChargeStatus;

$chargeStatus = ChargeStatus::PENDING(); // Note the braces at the end
$chargeStatus->getValue() === 'pending'; // true
$chargeStatus === ChargeStatus::fromValue('pending'); // true
// Also works for switch statements
switch ($chargeStatus) {
    case ChargeStatus::PENDING():
        // Do something
        break;
    // ...
}
```

### Updating resource models
To update/refresh any resource models (model classes that extends `Resource`)

```php
$charge->fetch();
```

### Long polling
The following resources supports long polling to wait for the next status change:
- `Charge`
- `Refund`
- `Cancel`
- `Subscription`

This is useful since these requests initially returns with a `PENDING` status. Long polling allows you to fetch the updated model when the resource has changed its status. If no changes occurs within 5 seconds, it will return the resource at that state.

```php
$charge = $client
    ->createCharge($token->id, Money::USD(1000)) // $charge->status == PENDING
    ->awaitResult(); // $charge->status == SUCCESSFUL
```

### Lists and pagination

All list functions in the SDK returns as a `Paginated` object in descending order of their creation time. When passing in parameters through an array, be careful to ensure your input matches the expected type, otherwise an `InvalidArgumentException` will be thrown.

```php
use InvalidArgumentException;
use Gopay\Enums\CursorDirection;

try {
    $transactionList = $client->listTransactionsByOptions([
        'from' => date_create('-1 week'),
        'to' => date_create('+1 week')
    ]);
} catch (InvalidArgumentException $error) {
    // When input parameters does not correspond to the correct type
}

$transactions = $transactionList->items; // Default limit per page = 10 items

if ($transactionList->hasMore) {
    $transactionList = $transactionList->getNext(); // The list does not mutate internally
    $transactions = array_merge($transactions, $transactionList->items);
}

$firstTenItems = $client->listTransactionsByOptions([
    'from' => date_create('-1 week'),
    'to' => date_create('+1 week'),
    'cursor_direction' => CursorDirection::ASC()
]);
```

## SDK Development

Building:
```shell
composer install
npm install

# Optionally
npm install -g grunt
```

Code formatting:
```shell
grunt phpcs
```

Tests:

The following env vars are required when running the tests:

- `GOPAY_PHP_TEST_TOKEN` - This should be a `test` mode token
- `GOPAY_PHP_TEST_SECRET`
- `GOPAY_PHP_TEST_ENDPOINT` - This would point to a local API instance or a staging instance

```shell
grunt phpunit
```
_Note: CircleCI only runs on branches that has a open PR_
