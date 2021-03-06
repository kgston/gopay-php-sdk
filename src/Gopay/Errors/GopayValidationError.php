<?php

namespace Gopay\Errors;

use Gopay\Enums\Field;
use Gopay\Enums\Reason;

class GopayValidationError extends GopayRequestError
{
    public function __construct(Field $field, Reason $reason)
    {
        parent::__construct('error', 'VALIDATION_ERROR', [
            'field' => $field->getValue(),
            'reason' => $reason->getValue()
        ]);
    }

    public function addError(Field $field, Reason $reason)
    {
        parent::$errors[] = [
            'field' => $field>getValue(),
            'reason' => $reason->getValue()
        ];
        return $this;
    }
}
