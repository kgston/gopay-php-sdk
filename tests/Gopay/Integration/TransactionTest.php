<?php
namespace GopayTest\Integration;

use Gopay\Enums\AppTokenMode;
use Gopay\Enums\Currency;
use Gopay\Enums\ChargeStatus;
use Gopay\Enums\TransactionType;
use Gopay\Resources\Paginated;
use Gopay\Resources\Subscription;
use Gopay\Resources\Transaction;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    use IntegrationSuite;

    public function testTransactionParse()
    {
        $str = <<<EOD
        {
          "items": [
            {
              "store_id": "22222222-2222-2222-2222-222222222222",
              "resource_id": "11111111-1111-1111-1111-111111111111",
              "charge_id": "33333333-3333-3333-3333-333333333333",
              "amount": 1000,
              "currency": "JPY",
              "amount_formatted": 1000,
              "type": "refund",
              "status": "failed",
              "metadata": {
                "key": "value"
              },
              "created_on": "2017-10-24T17:58:40.702667Z",
              "mode": "test"
            }
          ],
          "has_more": false
        }
EOD;

        $json = json_decode($str, true);
        $transactions = Paginated::fromResponse(
            $json,
            array(),
            Transaction::class,
            $this->getClient()->getStoreBasedContext()
        );
        $this->assertEquals(false, $transactions->hasMore);
        $this->assertEquals(1, count($transactions->items));
        $item = $transactions->items[0];
        $this->assertEquals("22222222-2222-2222-2222-222222222222", $item->storeId);
        $this->assertEquals("11111111-1111-1111-1111-111111111111", $item->resourceId);
        $this->assertEquals("33333333-3333-3333-3333-333333333333", $item->chargeId);
        $this->assertEquals(1000, $item->amount);
        $this->assertEquals(Currency::JPY(), $item->currency);
        $this->assertEquals(1000, $item->amountFormatted);
        $this->assertEquals(TransactionType::REFUND(), $item->type);
        $this->assertEquals(ChargeStatus::FAILED(), $item->status);
        $this->assertEquals(["key" => "value"], $item->metadata);
        $this->assertEquals(date_create("2017-10-24T17:58:40.702667Z"), $item->createdOn);
        $this->assertEquals(AppTokenMode::TEST(), $item->mode);
    }
}
