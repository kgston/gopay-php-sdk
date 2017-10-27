<?php
namespace GopayTest\Integration;

use Gopay\Resources\Paginated;
use Gopay\Resources\Subscription;
use Gopay\Resources\Transaction;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    use IntegrationSuite;

    public function testTransactionParse() {
        $str = <<<EOD
        {
          "items": [
            {
              "store_id": "22222222-2222-2222-2222-222222222222",
              "resource_id": "11111111-1111-1111-1111-111111111111",
              "charge_token_id": "33333333-3333-3333-3333-333333333333",
              "amount": 1000,
              "currency": "JPY",
              "amount_formatted": 1000,
              "type": "refund",
              "status": "failed",
              "metadata": {
              },
              "created_on": "2017-10-24T17:58:40.702667Z",
              "mode": "test"
            }
          ],
          "has_more": false
        }
EOD;

        $json = json_decode($str, true);
        $transactions = Paginated::fromResponse($json, array(), Transaction::class, $this->getClient()->getDefaultContext());
        $this->assertEquals(false, $transactions->hasMore);
        $this->assertEquals(1, count($transactions->items));
        $this->assertEquals(1000, $transactions->items[0]->amount);
    }
}