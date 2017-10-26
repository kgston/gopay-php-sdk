<?php
namespace GopayTest\Integration;

use Gopay\Resources\Ledger;
use PHPUnit\Framework\TestCase;

class LedgerTest extends TestCase
{
    use IntegrationSuite;

    public function testLedgerParse() {
        $str = <<<EOD
        {
          "id": "11111111-1111-1111-1111-111111111111",
          "store_id": "22222222-2222-2222-2222-222222222222",
          "amount": 1200,
          "currency": "USD",
          "amount_formatted": 12,
          "percent_fee": 3.5,
          "flat_fee_amount": 30,
          "flat_fee_currency": "USD",
          "flat_fee_formatted": 0.3,
          "exchange_rate": 105,
          "origin": "charge",
          "note": "a note",
          "created_on": "2017-10-26T17:37:33.742404+09:00"
        }
EOD;

        $json = json_decode($str, true);
        $ledger = Ledger::getSchema()->parse($json, array($this->getClient()->getDefaultContext()));
        $this->assertEquals(1200, $ledger->amount);
    }
}