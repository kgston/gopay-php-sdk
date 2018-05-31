<?php

namespace Gopay\Resources;

use Gopay\Utility\RequesterUtils;

trait Pollable
{
    abstract protected function getIdContext();

    public function awaitResult()
    {
        $idContext = $this->getIdContext();
        return RequesterUtils::executeGet(self::class, $idContext, array('polling' => 'true'));
    }
}
