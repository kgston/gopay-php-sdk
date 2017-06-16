<?php

namespace Gopay\Utility;

abstract class FunctionalUtils {

    public static function get_or_else(array $array, $key, $orElse) {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        } else {
            return $orElse;
        }
    }

    public static function get_or_null($array, $key) {
        return FunctionalUtils::get_or_else($array, $key, NULL);
    }

    public static function copy(array $array) {
        return array_merge(array(), $array);
    }

    public static function identity($a) {
        return $a;
    }

    public static function array_find_index($xs, $f) {
        $index = 0;
        foreach ($xs as $x) {
            if (call_user_func($f, $x) === true)
                return $index;
            $index++;
        }
        return null;
    }

    public static function get_class_vars_assoc($called) {
        //echo "called: $called \n";
        $classVars = array_keys(get_class_vars($called));

        $parent = get_parent_class($called);
        while ( $parent !== FALSE ) {
            $parentVars = array_keys(get_class_vars($parent));
            $classVars = array_merge($parentVars, array_diff($classVars, $parentVars));
            $parent = get_parent_class($parent);
        }

        return $classVars;
    }

}

