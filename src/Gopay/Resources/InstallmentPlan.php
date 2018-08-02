<?php

namespace Gopay\Resources;

use InvalidArgumentException;
use JsonSerializable;
use Gopay\Enums\InstallmentPlanType;
use Gopay\Utility\FormatterUtils;
use Gopay\Utility\Json\JsonSchema;

class InstallmentPlan implements JsonSerializable
{
    use Jsonable;

    public $planType;
    public $fixedCycles;
    public $fixedCycleAmount;

    public function __construct(InstallmentPlanType $planType, $fixedCycles = null, $fixedCycleAmount = null)
    {
        switch ($planType) {
            case InstallmentPlanType::NONE():
            case InstallmentPlanType::REVOLVING():
                if ($fixedCycles != null || $fixedCycleAmount != null) {
                    throw new InvalidArgumentException(
                        'None or revolving plans do not accept $fixedCycles or $fixedCycleAmount'
                    );
                }
                break;
            case InstallmentPlanType::FIXED_CYCLES():
                if ($fixedCycles == null || $fixedCycleAmount != null) {
                    throw new InvalidArgumentException(
                        'Fixed cycle plans requires $fixedCycles and not $fixedCycleAmount'
                    );
                }
                break;
            case InstallmentPlanType::FIXED_CYCLE_AMOUNT():
                if ($fixedCycles != null || $fixedCycleAmount == null) {
                    throw new InvalidArgumentException(
                        'Fixed cycle amount plans requires $fixedCycleAmount and not $fixedCycles'
                    );
                }
        }

        $this->planType = $planType;
        $this->fixedCycles = $fixedCycles;
        $this->fixedCycleAmount = $fixedCycleAmount;
    }

    public function jsonSerialize()
    {
        $data = ['plan_type' => $this->planType->getValue()];
        switch ($this->planType) {
            case InstallmentPlanType::FIXED_CYCLES():
                $data[$this->planType->getValue()] = $this->fixedCycles;
                break;
            case InstallmentPlanType::FIXED_CYCLE_AMOUNT():
                $data[$this->planType->getValue()] = $this->fixedCycleAmount;
                break;
        }
        return $data;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('plan_type', true, FormatterUtils::of('getInstallmentPlanType'));
    }
}
