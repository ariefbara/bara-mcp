<?php

namespace Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport;

use Participant\Domain\Model\Participant\OKRPeriod\Objective\KeyResult;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport;

class KeyResultProgressReport
{

    /**
     * 
     * @var ObjectiveProgressReport
     */
    protected $objectiveProgressReport;

    /**
     * 
     * @var KeyResult
     */
    protected $keyResult;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var int|null
     */
    protected $value;

    /**
     * 
     * @var bool
     */
    protected $disabled;

    public function __construct(
            ObjectiveProgressReport $objectiveProgressReport, KeyResult $keyResult, string $id,
            KeyResultProgressReportData $keyResultProgressReportData)
    {
        $this->objectiveProgressReport = $objectiveProgressReport;
        $this->keyResult = $keyResult;
        $this->id = $id;
        $this->value = $keyResultProgressReportData->getValue();
        $this->disabled = false;
    }
    
    public function update(KeyResultProgressReportData $keyResultProgressReportData): void
    {
        $this->value = $keyResultProgressReportData->getValue();
        $this->disabled = false;
    }
    
    public function disableIfCorrespondWithInactiveKeyResult(): void
    {
        if (!$this->keyResult->isActive()) {
            $this->disabled = true;
        }
    }
    
    public function correspondWith(KeyResult $keyResult): bool
    {
        return $this->keyResult === $keyResult;
    }

}
