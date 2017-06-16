<?php

namespace Gopay\Resources;

trait Jsonable {

    protected static $schema;

    protected abstract static function initSchema();

    public static function getSchema() {
        if (!isset(self::$schema)) {
            self::$schema = self::initSchema();
        }
        return self::$schema;
    }

}