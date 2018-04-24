<?php

namespace Gopay\Utility\Json;

use Gopay\Utility\FunctionalUtils;
use ReflectionClass;
use Gopay\Utility\StringUtils;

class JsonSchema {

    public $components;
    public $prefix;
    public $targetClass;

    function __construct($targetClass, $prefix = NULL){
        $this->components = array();
        $this->targetClass = $targetClass;
        $this->prefix = $prefix;
    }

    public function with($path, $required = false, $formatter = "Gopay\Utility\FunctionalUtils::identity") {
        array_push($this->components, new SchemaComponent($this->prefix . "/" .$path, $required, $formatter));
        return $this;
    }

    public function withNested(JsonSchema $schema) {
        foreach ($schema->components as $element) {
            array_push($this->components, $element);
        }
        return $this;
    }

    public function upsert($path, $required = false, $formatter = "Gopay\Utility\FunctionalUtils::identity") {
        $index = FunctionalUtils::array_find_index($this->components, function ($value) use ($path) {
            return $value->path === $path;
        });
        if ($index !== NULL) {
            $this->components = array_replace(
                $this->components,
                array($index =>
                    new SchemaComponent($this->prefix . "/" .$path, $required, $formatter)
                )
            );
            return $this;
        } else {
            return $this->with($path, $required = $required, $formatter = $formatter);
        }

    }

    private function getValues($json) {
        return array_map(function ($component) use ($json) {
            $path_parts = explode("/", $component->path);
            $value = JsonSchema::getField($json, $component->required, $path_parts);
            if ($value === NULL) {
                if ($component->required) {
                    throw new RequiredValueNotFoundException($component->path);
                } else {
                    return NULL;
                }

            }
            return call_user_func($component->formatter, $value);
        }, $this->components);
    }

    public function parse($json, array $additionalArgs = array()) {
        $targetClass = new ReflectionClass($this->targetClass);
        $arguments = array_merge($this->getValues($json), $additionalArgs);
        return $targetClass->newInstanceArgs($arguments);
    }

    public function getParser(array $additionalArgs = array()) {
        return function ($json) use ($additionalArgs) {
            return $this->parse($json, $additionalArgs);
        };
    }

    public static function fromClass($targetClass, $snakeCase = true, $includeParentVars = true) {
        $classVars = FunctionalUtils::get_class_vars_assoc($targetClass, $includeParentVars);
        $newSchema = new JsonSchema($targetClass);
        return array_reduce(
            $classVars, function ($schema, $path) use ($snakeCase) {
                $realPath = $snakeCase ? StringUtils::toSnakeCase($path) : $path;
                return $schema->with($realPath);
        }, $newSchema);
    }

    public static function getField($json, $required, array $paths) {
        if ($json === NULL && !$required) {
            return $json;
        }
        if (sizeof($paths) === 0) {
            throw new NoSuchPathException(NULL);
        }
        $nextKey = $paths[0];
        if (!array_key_exists($nextKey, $json)) {
            if ($required) {
                throw new NoSuchPathException($nextKey);
            } else {
                return NULL;
            }
        }
        $nextJson = $json[$nextKey];
        if (sizeof($paths) === 1) {
            return $nextJson;
        } else {
            try {
                return JsonSchema::getField($nextJson, $required, array_slice($paths, 1));
            } catch (NoSuchPathException $except) {
                throw new NoSuchPathException($nextKey . "/" . $except->path);
            }
        }
    }

}
