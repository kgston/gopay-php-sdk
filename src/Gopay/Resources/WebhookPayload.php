<?php

namespace Gopay\Resources;


class WebhookPayload
{

    public $event;
    public $data;

    public function __construct($event, $data)
    {
        $this->event = $event;
        $this->data = $data;
    }


}