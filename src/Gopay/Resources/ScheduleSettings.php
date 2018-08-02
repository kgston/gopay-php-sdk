<?php

namespace Gopay\Resources;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;
use JsonSerializable;
use Gopay\Utility\Json\JsonSchema;
use Gopay\Utility\FormatterUtils;
use Gopay\Utility\FunctionalUtils;

class ScheduleSettings implements JsonSerializable
{
    use Jsonable;
    
    public $startOn;
    public $zoneId;
    public $preserveEndOfMonth;

    public function __construct(
        DateTime $startOn = null,
        DateTimeZone $zoneId = null,
        $preserveEndOfMonth = false
    ) {
        $this->startOn = $startOn;
        $this->zoneId = $zoneId;
        $this->preserveEndOfMonth = $preserveEndOfMonth;
    }

    public function jsonSerialize()
    {
        if (is_null($this->startOn) && is_null($this->zoneId) && !$this->preserveEndOfMonth) {
            return null;
        }
        if (isset($this->startOn) && $this->startOn < date_create()) {
            throw new GopayValidationError(Field::START_ON(), Reason::MUST_BE_FUTURE_TIME());
        }
        return FunctionalUtils::stripNulls([
            'start_on' => isset($this->startOn) ? $this->startOn->format('Y-m-d') : null,
            'zone_id' => isset($this->zoneId) ? $this->zoneId->getName() : null,
            'preserve_end_of_month' => $this->preserveEndOfMonth === true ? true : null
        ]);
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('start_on', false, FormatterUtils::of('getDateTime'))
            ->upsert('zone_id', true, FormatterUtils::of('getDateTimeZone'));
    }
}
