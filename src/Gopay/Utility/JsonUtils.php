<?php

namespace Gopay\Utility;

use Exception;
use ReflectionClass;
use StringUtils;

abstract class JsonException extends Exception {

    public $path;

    function __construct($path){
        $this->path = $path;
    }

}

class NoSuchPathException extends JsonException {}

class RequiredValueNotFoundException extends JsonException { }

class SchemaComponent {

    public $path;
    public $required;
    public $formatter;

    public function __construct($path, $required, $formatter)
    {
        $this->path = $path;
        $this->required = $required;
        $this->formatter = $formatter;
    }

}

class JsonSchema {

    public $components;
    public $prefix;
    public $targetClass;

    function __construct($targetClass, $prefix = NULL){
        $this->components = array();
        $this->targetClass = $targetClass;
        $this->prefix = $prefix;
    }

    public function with($path, $required = false, $formatter = FunctionalUtils::identity) {
        array_push($this->components, new SchemaComponent($this->prefix . "/" .$path, $required, $formatter));
        return $this;
    }

    public function withNested(JsonSchema $schema) {
        foreach ($schema->components as $element) {
            array_push($this->components, $element);
        }
        return $this;
    }

    public function upsert($path, $required = false, $formatter = FunctionalUtils::identity) {
        $index = FunctionalUtils::array_find_index($this->components, function ($value) use ($path) {
            return $value->path === $path;
        });
        if ($index !== NULL) {
            $this->components = array_replace(
                $this->components,
                array($index => new SchemaComponent($this->prefix . "/" .$path, $required, $formatter))
            );
            return $this;
        } else {
            return $this->with($path, $required, $formatter);
        }

    }

    private function getValues($json) {
        return array_map(function ($component) use ($json) {
            $path_parts = str_split($component->path, "/");
            $value = $component->formatter(JsonSchema::getField($json, $path_parts));
            if ($component->required &&
                $value === NULL) {
                throw new RequiredValueNotFoundException($component->path);
            }
            array_push($values, $value);
        }, $this->components);
    }

    public function parse($json, array $additionalArgs = array()) {
        $targetClass = new ReflectionClass($this->targetClass);
        $arguments = array_merge($this->getValues($json), $additionalArgs);
        return $targetClass->newInstanceArgs($arguments);
    }

    public static function fromClass($targetClass, $snakeCase = true) {
        $classVars = array_keys(get_class_vars($targetClass));
        $newSchema = new JsonSchema($targetClass);
        return array_reduce(
            $classVars, function ($schema, $path) use ($snakeCase) {
                $realPath = $snakeCase ? StringUtils::toSnakeCase($path) : $path;
                return $schema->with($realPath);
        }, $newSchema);
    }

    public static function getField($json, array $paths) {
        if (sizeof($paths) === 0) {
            throw new NoSuchPathException(NULL);
        }
        $nextKey = $paths[0];
        if (!array_key_exists($nextKey, $paths)) {
            throw new NoSuchPathException($nextKey);
        }
        $nextJson = $json[$nextKey];
        if (sizeof($paths) === 1) {
            return $nextJson;
        } else {
            try {
                return JsonSchema::getField($nextJson, array_slice($paths, 1));
            } catch (NoSuchPathException $except) {
                throw new NoSuchPathException($nextKey . "/" . $except->path);
            }
        }
    }

}
