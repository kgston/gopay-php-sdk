<?php

namespace Gopay\Resources;

use Gopay\Enums\AppTokenMode;
use Gopay\Resources\Configuration\CardConfiguration;
use Gopay\Resources\Configuration\QRConfiguration;
use Gopay\Resources\Configuration\ConvenienceConfiguration;
use Gopay\Resources\Configuration\ThemeConfiguration;
use Gopay\Utility\Json\JsonSchema;

class CheckoutInfo
{
    use Jsonable;
    public $mode;
    public $recurringTokenPrivilege;
    public $name;
    public $cardConfiguration;
    public $qrScanConfiguration;
    public $convenienceConfiguration;
    public $logoImage;
    public $theme;

    public function __construct(
        $mode,
        $recurringTokenPrivilege,
        $name,
        $cardConfiguration,
        $qrScanConfiguration,
        $convenienceConfiguration,
        $logoImage,
        $theme
    ) {
        $this->mode = AppTokenMode::fromValue($mode);
        $this->recurringTokenPrivilege = $recurringTokenPrivilege;
        $this->name = $name;
        $this->cardConfiguration = $cardConfiguration;
        $this->qrScanConfiguration = $qrScanConfiguration;
        $this->convenienceConfiguration = $convenienceConfiguration;
        $this->logoImage = $logoImage;
        $this->theme = $theme;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert("card_configuration", true, CardConfiguration::getSchema()->getParser())
            ->upsert("qr_scan_configuration", true, QRConfiguration::getSchema()->getParser())
            ->upsert("convenience_configuration", true, ConvenienceConfiguration::getSchema()->getParser())
            ->upsert("theme", true, ThemeConfiguration::getSchema()->getParser());
    }
}
