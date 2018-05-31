<?php
namespace GopayTest\Integration;

use DateTime;
use Gopay\Enums\AppTokenMode;
use Gopay\Resources\CheckoutInfo;
use PHPUnit\Framework\TestCase;

class CheckoutInfoTest extends TestCase
{
    use IntegrationSuite;

    public function testCheckoutInfoParse()
    {
        $str = <<<EOD
        {
            "mode": "test",
            "recurring_token_privilege": "infinite",
            "name": "My Store",
            "card_configuration": {
                "enabled": true,
                "debit_enabled": true,
                "prepaid_enabled": true,
                "forbidden_card_brands": null,
                "allowed_countries_by_ip": null,
                "foreign_cards_allowed": null,
                "fail_on_new_email": null,
                "card_limit": null,
                "allow_empty_cvv": null
            },
            "qr_scan_configuration": {
                "enabled": false,
                "forbidden_qr_scan_gateways": null
            },
            "convenience_configuration": {
                "enabled": true
            },
            "logo_image": "https://someImage.com/abc.png",
            "theme": {
                "colors": {
                    "main_background": "#fafafa",
                    "secondary_background": "#ee7a00",
                    "main_color": "#fafafa",
                    "main_text": "#838383",
                    "primary_text": "#fafafa",
                    "secondary_text": "#222222",
                    "base_text": "#000000"
                }
            }
        }
EOD;

        $json = json_decode($str, true);
        $checkoutInfo = CheckoutInfo::getSchema()->parse($json, array($this->getClient()->getStoreBasedContext()));
        $this->assertEquals(AppTokenMode::TEST(), $checkoutInfo->mode);
        $this->assertEquals("infinite", $checkoutInfo->recurringTokenPrivilege);
        $this->assertEquals("My Store", $checkoutInfo->name);
        $this->assertEquals(true, $checkoutInfo->cardConfiguration->enabled);
        $this->assertEquals(true, $checkoutInfo->cardConfiguration->debitEnabled);
        $this->assertEquals(true, $checkoutInfo->cardConfiguration->prepaidEnabled);
        $this->assertEquals(null, $checkoutInfo->cardConfiguration->forbiddenCardBrands);
        $this->assertEquals(null, $checkoutInfo->cardConfiguration->allowedCountriesByIp);
        $this->assertEquals(null, $checkoutInfo->cardConfiguration->foreignCardsAllowed);
        $this->assertEquals(null, $checkoutInfo->cardConfiguration->failOnNewEmail);
        $this->assertEquals(null, $checkoutInfo->cardConfiguration->cardLimit);
        $this->assertEquals(null, $checkoutInfo->cardConfiguration->allowEmptyCvv);
        $this->assertEquals(false, $checkoutInfo->qrScanConfiguration->enabled);
        $this->assertEquals(null, $checkoutInfo->qrScanConfiguration->forbiddenQrScanGateway);
        $this->assertEquals(true, $checkoutInfo->convenienceConfiguration->enabled);
        $this->assertEquals("https://someImage.com/abc.png", $checkoutInfo->logoImage);
        $this->assertEquals("#fafafa", $checkoutInfo->theme->colors->mainBackground);
        $this->assertEquals("#ee7a00", $checkoutInfo->theme->colors->secondaryBackground);
        $this->assertEquals("#fafafa", $checkoutInfo->theme->colors->mainColor);
        $this->assertEquals("#838383", $checkoutInfo->theme->colors->mainText);
        $this->assertEquals("#fafafa", $checkoutInfo->theme->colors->primaryText);
        $this->assertEquals("#222222", $checkoutInfo->theme->colors->secondaryText);
        $this->assertEquals("#000000", $checkoutInfo->theme->colors->baseText);

        $checkoutInfoLive = $this->getClient()->getCheckoutInfo();
        $this->assertTrue(is_string($checkoutInfoLive->name));
    }
}
