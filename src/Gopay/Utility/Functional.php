<?php

namespace Gopay\Utility;

function get_or_else(array $array, $key, $orElse) {
    if (array_key_exists($key, $array)) {
        return $array[$key];
    } else {
        return $orElse;
    }
}

function get_or_null($array, $key) {
    return get_or_else($array, $key, NULL);
}

function copy(array $array) {
    return array_merge(array(), $array);
}