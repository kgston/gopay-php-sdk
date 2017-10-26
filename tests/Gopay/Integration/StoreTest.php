<?php
namespace GopayTest\Integration;

use Gopay\Resources\Paginated;
use Gopay\Resources\Store;
use PHPUnit\Framework\TestCase;

class StoreTest extends TestCase
{
    use IntegrationSuite;

    public function testGetStore() {
        $str = <<<EOD
        {
          "id": "11111111-1111-1111-1111-111111111111",
          "platform_id": "22222222-2222-2222-2222-222222222222",
          "merchant_id": "33333333-3333-3333-3333-333333333333",
          "name": "Store 1",
          "active": true,
          "created_on": "2017-03-21T01:32:13.702689Z",
          "updated_on": "2017-05-22T03:50:48.302633Z",
          "configuration": {
            "id": "44444444-4444-4444-4444-444444444444",
            "percent_fee": null,
            "flat_fees": [],
            "logo_url": "https://example.com/logo.png",
            "country": null,
            "language": null,
            "card_configuration": {
              "enabled": true,
              "debit_enabled": null,
              "prepaid_enabled": true,
              "forbidden_card_brands": [
                "maestro",
                "unionpay"
              ],
              "allowed_countries_by_ip": null,
              "foreign_cards_allowed": null,
              "fail_on_new_email": null,
              "monthly_limit": null
            },
            "qr_scan_configuration": {
              "enabled": null,
              "forbidden_qr_scan_gateways": null
            },
            "convenience_configuration": {
              "enabled": null
            },
            "recurring_token_configuration": {
              "recurring_type": null,
              "charge_wait_period": null
            },
            "security_configuration": {
              "inspect_suspicious_login_after": null
            },
            "card_brand_percent_fees": {
              "visa": null,
              "american_express": null,
              "mastercard": null,
              "maestro": null,
              "discover": null,
              "jcb": null,
              "diners_club": null,
              "union_pay": null
            }
          }
        }
EOD;
        $json = json_decode($str, true);
        $store = Store::getSchema()->parse($json, array($this->getClient()->getDefaultContext()));
        $this->assertEquals("Store 1", $store->name);
    }

    public function testListStores() {
        $str = <<<EOD
        {
          "items": [
            {
              "id": "11111111-1111-1111-1111-111111111111",
              "platform_id": "22222222-2222-2222-2222-222222222222",
              "merchant_id": "33333333-3333-3333-3333-333333333333",
              "name": "Store 1",
              "created_on": "2017-10-15T05:10:11.417553Z"
            },
            {
              "id": "11111111-1111-1111-1111-111111111112",
              "platform_id": "22222222-2222-2222-2222-222222222222",
              "merchant_id": "33333333-3333-3333-3333-333333333333",
              "name": "Store 2",
              "created_on": "2017-06-08T00:44:22.994851Z"
            }
          ],
          "has_more": false
        }
EOD;
        $json = json_decode($str, true);
        $stores = Paginated::fromResponse($json, array(), Store::class, $this->getClient()->getDefaultContext());
        $this->assertEquals(false, $stores->hasMore);
        $this->assertEquals(2, count($stores->items));
        $this->assertEquals("Store 2", $stores->items[1]->name);
    }
}