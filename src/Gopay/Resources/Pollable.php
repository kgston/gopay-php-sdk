<?php

namespace Gopay\Resources;

use WebSocket\Client;

trait Pollable
{

    public function awaitResult()
    {
        $idContext = $this->getIdContext();
        $url = $idContext->appendPath("events")->getWebsocketURL();
        $parser = self::getContextParser($idContext);
        $client = new Client($url);
        return $parser(json_decode($client->receive(), true));
    }
}
