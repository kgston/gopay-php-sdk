<?php


namespace Gopay\Resources\PaymentData;

use Gopay\Utility\Json\JsonSchema;

class CardData
{
    public $card;
    public $billing;

    private static $schema;

    public function __construct($card, $billing)
    {
        $this->card = $card;
        $this->billing = $billing;
    }

    public static function getSchema()
    {
        if (!isset(self::$schema)) {
            self::$schema = (new JsonSchema(CardData::class))
                ->with('card', true, Card::getSchema()->getParser())
                ->with('billing', false, Address::getSchema()->getParser());
        }
        return self::$schema;
    }
}
