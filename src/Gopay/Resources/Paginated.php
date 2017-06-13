<?php
/**
 * Created by PhpStorm.
 * User: adamsar
 * Date: 6/13/17
 * Time: 6:50 PM
 */

namespace Gopay\Resources;


use Composer\DependencyResolver\Request;
use Gopay\Errors\GopayNoMoreItemsError;
use Gopay\Requests\RequestContext;
use Gopay\Requests\Requester;

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

    private function applyNewResponse($response, $query) {
        
    }

    public function getNextCursor() {
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
        return
    }

}