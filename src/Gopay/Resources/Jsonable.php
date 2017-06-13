<?php
/**
 * Created by PhpStorm.
 * User: adamsar
 * Date: 6/13/17
 * Time: 6:24 PM
 */

namespace Gopay\Resources;


use Gopay\Requests\RequestContext;

interface Jsonable {

    public static function fromJson(array $json, RequestContext $requestContext);

}