<?php

namespace Gopay\Resources;

trait Jsonable
{

    protected static $schema;

    protected abstract static function initSchema();

    public static function getSchema()
    {
        if (!isset(self::$schema)) {
            self::$schema = self::initSchema();
        }
        return self::$schema;
    }

    public static function getContextParser($context)
    {
        return self::getSchema()->getParser(array($context));
    }
}
