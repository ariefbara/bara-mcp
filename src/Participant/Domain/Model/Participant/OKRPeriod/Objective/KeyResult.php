<?php

namespace Participant\Domain\Model\Participant\OKRPeriod\Objective;

use Participant\Domain\Model\Participant\OKRPeriod\Objective;
use Participant\Domain\Model\Participant\OKRPeriod\ObjectiveData;
use Resources\ValidationRule;
use Resources\ValidationService;
use SharedContext\Domain\ValueObject\Label;

class KeyResult
{

    /**
     * 
     * @var Objective
     */
    protected $objective;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Label
     */
    protected $label;

    /**
     * 
     * @var int
     */
    protected $target;

    /**
     * 
     * @var int
     */
    protected $weight;

    /**
     * 
     * @var bool 
     */
    protected $disabled;
    
    public function isActive(): bool
    {
        return ! $this->disabled;
    }
    
    protected function setTarget(int $target): void
    {
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($target, "bad request: key result's target is mandatory");
        $this->target = $target;
    }
    protected function setWeight(int $weight): void
    {
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($weight, "bad request: key result's weight is mandatory");
        $this->weight = $weight;
    }
    public function __construct(Objective $objective, string $id, KeyResultData $keyResultData)
    {
        $this->objective = $objective;
        $this->id = $id;
        $this->label = new Label($keyResultData->getLabelData());
        $this->setTarget($keyResultData->getTarget());
        $this->setWeight($keyResultData->getWeight());
        $this->disabled = false;
    }

    public function updateAggregate(ObjectiveData $objectiveData): void
    {
        $keyResultData = $objectiveData->pullKeyResultData($this->id);
        if (isset($keyResultData)) {
            $this->disabled = false;
            $this->label = new Label($keyResultData->getLabelData());
            $this->setTarget($keyResultData->getTarget());
            $this->setWeight($keyResultData->getWeight());
        } else {
            $this->disable();
        }
    }
    
    public function disable(): void
    {
        $this->disabled = true;
    }
    
    public function setProgressReportIn(
            ObjectiveProgressReport $objectiveProgressReport, ObjectiveProgressReportData $objectiveProgressReportData): void
    {
        if ($this->disabled) {
            return;
        }
        
        $keyResultProgressReportData = $objectiveProgressReportData->pullKeyResultProgressReportData($this->id);
        if (isset($keyResultProgressReportData)) {
            $objectiveProgressReport->setKeyResultProgressReport($this, $keyResultProgressReportData);
        }
    }

}
