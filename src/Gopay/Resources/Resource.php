<?php

namespace Gopay\Resources;


abstract class Resource {

    public $id;
    protected $context;

    function __construct($id, $context) {
        $this->id = $id;
        $this->context = $context;
    }

    protected function getIdContext() {
        if (strpos($this->context->getFullURL(), $this->id)) {
            return $this->context;
        } else {
            return $this->context.appendPath($this->id);
        }
    }

}