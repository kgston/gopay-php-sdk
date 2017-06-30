<?php

namespace Gopay\Resources;


trait Pollable {

    protected static $pendingStatus = "pending";

    public function awaitResult() {
        $newInstance = $this;
        while(strtolower($newInstance->status) === self::$pendingStatus) {
            sleep(1);
            $newInstance = $this->fetch();
        }
        return $newInstance;
    }

}