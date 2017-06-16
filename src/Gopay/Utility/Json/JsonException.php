<?php
/**
 * Created by PhpStorm.
 * User: adamsar
 * Date: 6/16/17
 * Time: 2:00 PM
 */

namespace Gopay\Utility\Json;

use Exception;

abstract class JsonException extends Exception
{

    public $path;

    function __construct($path)
    {
        parent::__construct("Error at path " . $path);
        $this->path = $path;
    }

}