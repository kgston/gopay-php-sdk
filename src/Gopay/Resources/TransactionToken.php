<?php
/**
 * Created by PhpStorm.
 * User: adamsar
 * Date: 6/16/17
 * Time: 5:50 PM
 */

namespace Gopay\Resources;


use Gopay\Utility\Json\JsonSchema;

class TransactionToken extends Resource {
    use Jsonable;

    public $store_id;
    public $email;
    public $payment_type;
    public $active;
    public $mode;
    public $type;
    public $usage_limit;
    public $created_on;
    public $last_used_on;
    public $data;

    function __construct($id,
                         $store_id,
                         $email,
                         $payment_type,
                         $active,
                         $mode,
                         $type,
                         $usage_limit,
                         $created_on,
                         $last_used_on,
                         $data,
                         $context)
    {
        parent::__construct($id, $context);
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(TransactionToken::class);
    }
}