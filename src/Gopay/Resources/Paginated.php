<?php

namespace Gopay\Resources;

use Gopay\Errors\GopayNoMoreItemsError;
use Gopay\Requests\RequestContext;
use Gopay\Requests\Requester;
use Gopay\Utility\FunctionalUtils as fp;
use Gopay\Utility\RequesterUtils;

function get_other_direction($direction) {
    if ($direction === "asc") {
        return "desc";
    } else {
        return "asc";
    }
}

class Paginated {

    public $items;
    public $hasMore;
    private $jsonableClass;
    private $context;
    private $query;

    public function __construct($items,
                                $hasMore,
                                $query,
                                $jsonableClass,
                                RequestContext $context)
    {
        $this->items = $items;
        $this->hasMore = $hasMore;
        $this->jsonableClass = $jsonableClass;
        $this->context = $context;
        $this->query = $query;
    }

    private function parse($json) {
        return $this->formatFn($json, $this->context);
    }

    public static function fromResponse($response,
                                        $query,
                                        $jsonableClass,
                                        $context) {
        $parser = $jsonableClass::getContextParser($context);
        return new Paginated(
            array_map($parser, $response["items"]),
            $response["has_more"],
            $query,
            $jsonableClass,
            $context
        );
    }

    public function getNext() {
        if (!is_array($this->items) || !sizeof($this->items) === 0) {
          throw new GopayNoMoreItemsError();
        }
        $last = end($this->items);
        if (!property_exists($last, "id")) {
            throw new GopayNoMoreItemsError();
        }
        $nextCursor = $last->id;
        $newQuery = array_merge(array("next_cursor" => $nextCursor), $this->query);
        return RequesterUtils::execute_get_paginated($this->jsonableClass, $this->context, $newQuery);
    }

    public function reverse() {
        $currentDirection = fp::get_or_else($this->query, "cursor_direction", "desc");
        $newQuery = array_merge(
            array("cursor_direction" => get_other_direction($currentDirection)),
            $this->query
        );
        return new Paginated(
            array_reverse($this->items),
            $this->hasMore,
            $newQuery,
            $this->jsonableClass,
            $this->context
        );
    }

    public function getPrevious() {
        return $this->reverse()->getNext()->reverse();
    }

}