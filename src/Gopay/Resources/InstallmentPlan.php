<?php

namespace Gopay\Resources;

use InvalidArgumentException;
use Gopay\Enum\InstallmentPlanType;
use Gopay\Utility\Json\JsonSchema;

class InstallmentPlan
{
    use Jsonable;

    public $installmentPlanType;
    public $fixedCycles;
    public $fixedCycleAmount;

    public function __construct($installmentPlanType, $fixedCycles = null, $fixedCycleAmount = null)
    {
        if (!$installmentPlanType instanceof InstallmentPlanType) {
            $installmentPlanType = new InstallmentPlanType($installmentPlanType);
        }
        $this->installmentPlanType = $installmentPlanType;

        if ($this->installmentPlanType == InstallmentPlanType::REVOLVING &&
            ($fixedCycles != null || $fixedCycleAmount != null)) {
            throw new InvalidArgumentException("Revolving plans do not accept $fixedCycles or $fixedCycleAmount");
        } elseif ($this->installmentPlanType == InstallmentPlanType::FIXED_CYCLES &&
            ($fixedCycles == null || $fixedCycleAmount != null)) {
            throw new InvalidArgumentException("Fixed cycle plans requires $fixedCycles and not $fixedCycleAmount");
        } elseif ($this->installmentPlanType == InstallmentPlanType::FIXED_CYCLE_AMOUNT &&
            ($fixedCycles != null || $fixedCycleAmount == null)) {
            throw new InvalidArgumentException(
                "Fixed cycle amount plans requires $fixedCycleAmount and not $fixedCycles"
            );
        }
        $this->fixedCycles = $fixedCycles;
        $this->fixedCycleAmount = $fixedCycleAmount;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
