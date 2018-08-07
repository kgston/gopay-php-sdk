<?php

namespace Gopay\Resources\PaymentMethod;

use JsonSerializable;
use Gopay\Enums\PaymentType;
use Gopay\Enums\TokenType;
use Gopay\Enums\UsageLimit;

abstract class PaymentMethod implements JsonSerializable
{
    public $email;
    public $paymentType;
    public $type;
    public $usageLimit;
    public $metadata;

    protected function __construct(
        $email,
        PaymentType $paymentType,
        TokenType $type,
        UsageLimit $usageLimit = null,
        array $metadata = null
    ) {
        $this->acceptsTokenType($type);
        
        $this->email = $email;
        $this->paymentType = $paymentType;
        $this->type = $type;
        $this->usageLimit = $usageLimit;
        $this->metadata = $metadata;
    }

    // Returns void if this payment method accepts the token type
    // Throws GopayValidationError if not valid
    abstract protected function acceptsTokenType(TokenType $type);

    public function jsonSerialize()
    {
        $data = [
            'email' => $this->email,
            'payment_type' => $this->paymentType->getValue(),
            'type' => $this->type->getValue()
        ];
        if ($this->usageLimit !== null) {
            $data['usage_limit'] = $this->usageLimit->getValue();
        }
        if ($this->metadata !== null) {
            $data['metadata'] = $this->metadata;
        }
        return $data;
    }
}
