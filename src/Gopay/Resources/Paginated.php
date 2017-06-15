<?php
/**
 * Created by PhpStorm.
 * User: adamsar
 * Date: 6/13/17
 * Time: 6:50 PM
 */

namespace Gopay\Resources;

use Gopay\Errors\GopayNoMoreItemsError;
use Gopay\Requests\RequestContext;
use Gopay\Requests\Requester;
use Gopay\Utility\FunctionalUtils as fp;

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
    private $formatFn;
    private $context;
    private $query;
    private $requester;

    public function __construct($items,
                                $hasMore,
                                $query,
                                $formatFn,
                                RequestContext $context,
                                Requester $requester)
    {
        $this->items = $items;
        $this->hasMore = $hasMore;
        $this->formatFn = $formatFn;
        $this->context = $context;
        $this->query = $query;
        $this->requester = $requester;
    }

    private function parse($json) {
        return $this->formatFn($json, $this->context);
    }

    public static function fromResponse($response,
                                        $query,
                                        $formatFn,
                                        $context,
                                        $requester) {
        return new Paginated(
            array_map($formatFn, $response["items"]),
            $response["has_more"],
            $query,
            $formatFn,
            $context,
            $requester
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
        $response = $this->requester->get($this->context, $newQuery);
        return $this->fromResponse($response, $newQuery, $this->parse, $this->context, $this->requester);
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
            $this->formatFn,
            $this->context,
            $this->requester
        );
    }

    public function getPrevious() {
        return $this->reverse()->getNext()->reverse();
    }

}