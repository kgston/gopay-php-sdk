<?php
/**
 * Created by PhpStorm.
 * User: adamsar
 * Date: 6/16/17
 * Time: 2:00 PM
 */

namespace Gopay\Utility\Json;

class SchemaComponent
{

    public $path;
    public $required;
    public $formatter;

    public function __construct($path, $required, $formatter)
    {
        $this->path = trim($path, "/");
        $this->required = $required;
        $this->formatter = $formatter;
    }

}