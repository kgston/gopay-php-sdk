<?php

namespace Gopay\Resources;


use Gopay\Utility\RequesterUtils;

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
            return $this->context->appendPath($this->id);
        }
    }

    public function fetch() {
        $context = $this->getIdContext();
        return RequesterUtils::execute_get(get_class($this), $context, array());
    }

    public function update(array $updates) {
        $context = $this->getIdContext();
        return RequesterUtils::execute_patch(get_class($this), $context, $updates);
    }

}
