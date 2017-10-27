<?php
namespace GopayTest\Integration;

use Gopay\Resources\Paginated;
use Gopay\Resources\Store;
use PHPUnit\Framework\TestCase;

class StoreTest extends TestCase
{
    use IntegrationSuite;

    /**
     * @group failing
     */
    public function testGetStore() {
        $str = <<<EOD
        {
          "id": "11111111-1111-1111-1111-111111111111",
          "name": "Store 1",
          "created_on": "2017-03-21T01:32:13.702689Z",
          "configuration": {
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
              "enabled": true,
              "forbidden_qr_scan_gateways": null
            },
            "convenience_configuration": {
              "enabled": true
            },
            "recurring_token_configuration": {
              "recurring_type": "bounded",
              "charge_wait_period": null
            },
            "security_configuration": {
              "inspect_suspicious_login_after": "P7D"
            },
            "card_brand_percent_fees": {
              "visa": 0.05,
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
        $this->assertEquals("11111111-1111-1111-1111-111111111111", $store->id);
        $this->assertEquals("2017-03-21T01:32:13.702689Z", $store->createdOn);
        $this->assertEquals("https://example.com/logo.png", $store->configuration->logoUrl);
        $this->assertEquals(array("maestro", "unionpay"), $store->configuration->cardConfiguration->forbiddenCardBrands);
        $this->assertTrue($store->configuration->qrScanConfiguration->enabled);
        $this->assertTrue($store->configuration->convenienceConfiguration->enabled);
        $this->assertEquals("bounded", $store->configuration->recurringTokenConfiguration->recurringType);
        $this->assertEquals("P7D", $store->configuration->securityConfiguration->inspectSuspiciousLoginAfter);
        $this->assertEquals(0.05, $store->configuration->cardBrandPercentFees->visa);
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