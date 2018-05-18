<?php

namespace Gopay\Resources;

use InvalidArgumentException;
use JsonSerializable;
use Gopay\Enums\InstallmentPlanType;
use Gopay\Utility\Json\JsonSchema;

class InstallmentPlan implements JsonSerializable
{
    use Jsonable;

    public $planType;
    public $fixedCycles;
    public $fixedCycleAmount;

    public function __construct($planType, $fixedCycles = null, $fixedCycleAmount = null)
    {
        if (!$planType instanceof InstallmentPlanType) {
            $planType = InstallmentPlanType::fromValue($planType);
        }
        
        if (($planType == InstallmentPlanType::NONE() || $planType == InstallmentPlanType::REVOLVING()) &&
        ($fixedCycles != null || $fixedCycleAmount != null)) {
            throw new InvalidArgumentException('None or revolving plans do not accept 
                $fixedCycles or $fixedCycleAmount');
        } elseif ($planType == InstallmentPlanType::FIXED_CYCLES() &&
        ($fixedCycles == null || $fixedCycleAmount != null)) {
            throw new InvalidArgumentException('Fixed cycle plans requires $fixedCycles and not $fixedCycleAmount');
        } elseif ($planType == InstallmentPlanType::FIXED_CYCLE_AMOUNT() &&
        ($fixedCycles != null || $fixedCycleAmount == null)) {
            throw new InvalidArgumentException(
                'Fixed cycle amount plans requires $fixedCycleAmount and not $fixedCycles'
            );
        }

        $this->planType = $planType;
        $this->fixedCycles = $fixedCycles;
        $this->fixedCycleAmount = $fixedCycleAmount;
    }

    public function jsonSerialize()
    {
        $data = array('plan_type' => $this->planType->getValue());
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
        return JsonSchema::fromClass(self::class);
    }
}
