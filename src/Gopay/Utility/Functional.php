<?php

namespace Gopay\Utility;

function get_or_else($array, $key, $orElse) {
    if (array_key_exists($key, $array)) {
        return $array[$key];
    } else {
        return $orElse;
    }
}