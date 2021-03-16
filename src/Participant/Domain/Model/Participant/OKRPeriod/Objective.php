<?php

namespace Participant\Domain\Model\Participant\OKRPeriod;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\Model\Participant\ManageableByParticipant;
use Participant\Domain\Model\Participant\OKRPeriod;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\KeyResult;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReportData;
use Participant\Domain\Model\Participant\OKRPeriodData;
use Resources\Exception\RegularException;
use Resources\Uuid;
use Resources\ValidationRule;
use Resources\ValidationService;
use SharedContext\Domain\ValueObject\Label;

class Objective implements ManageableByParticipant
{

    /**
     * 
     * @var OKRPeriod
     */
    protected $okrPeriod;

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
    protected $weight;

    /**
     * 
     * @var bool
     */
    protected $disabled;

    /**
     * 
     * @var ArrayCollection
     */
    protected $keyResults;
    
    /**
     * 
     * @var ArrayCollection
     */
    protected $objectiveProgressReports;
    
    public function isActive(): bool
    {
        return ! $this->disabled;
    }
    
    protected function setWeight(int $weight): void
    {
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($weight, "bad request: objective's weight is mandatory");
        $this->weight = $weight;
    }
    protected function aggregateKeyResults(ObjectiveData $objectiveData): void
    {
        foreach ($objectiveData->getKeyResultDataIterator() as $keyResultData) {
            $keyResult = new KeyResult($this, Uuid::generateUuid4(), $keyResultData);
            $this->keyResults->add($keyResult);
        }
    }
    protected function assertHasActiveKeyResult(): void
    {
        $p = function (KeyResult $keyResult) {
            return $keyResult->isActive();
        };
        if (empty($this->keyResults->filter($p)->count())) {
            $errorDetail = 'forbidden: objective must have at least one key result';
            throw RegularException::forbidden($errorDetail);
        }
    }
    
    public function isManageableByParticipant(\Participant\Domain\Model\Participant $participant): bool
    {
        return $this->okrPeriod->isManageableByParticipant($participant);
    }
    
    public function __construct(OKRPeriod $okrPeriod, string $id, ObjectiveData $objectiveData)
    {
        $this->okrPeriod = $okrPeriod;
        $this->id = $id;
        $this->label = new Label($objectiveData->getLabelData());
        $this->setWeight($objectiveData->getWeight());
        $this->disabled = false;
        $this->keyResults = new ArrayCollection();
        $this->aggregateKeyResults($objectiveData);
        $this->assertHasActiveKeyResult();
    }
    
    public function updateAggregate(OKRPeriodData $okrPeriodData): void
    {
        $objectiveData = $okrPeriodData->pullObjectiveDataWithId($this->id);
        if (isset($objectiveData)) {
            $this->disabled = false;
            $this->label = new Label($objectiveData->getLabelData());
            $this->weight = $objectiveData->getWeight();
            foreach ($this->keyResults->getIterator() as $keyResult) {
                $keyResult->updateAggregate($objectiveData);
            }
            $this->aggregateKeyResults($objectiveData);
            $this->assertHasActiveKeyResult();
        } else {
            $this->disable();
        }
    }
    
    public function disable(): void
    {
        $this->disabled = true;
        foreach ($this->keyResults->getIterator() as $keyResult) {
            $keyResult->disable();
        }
    }
    
    public function submitReport(
            string $objectiveProgressReportId, ObjectiveProgressReportData $objectiveProgressReportData): ObjectiveProgressReport
    {
        return new ObjectiveProgressReport($this, $objectiveProgressReportId, $objectiveProgressReportData);
    }
    
    public function canAcceptReportAt(DateTimeImmutable $reportDate): bool
    {
        return !$this->disabled
                && $this->okrPeriod->canAcceptReportAt($reportDate);
    }
    
    public function aggregateKeyResultProgressReportTo(
            ObjectiveProgressReport $objectiveProgressReport, ObjectiveProgressReportData $objectiveProgressReportData): void
    {
        foreach ($this->keyResults->getIterator() as $keyResult) {
            $keyResult->setProgressReportIn($objectiveProgressReport, $objectiveProgressReportData);
        }
    }
    
    public function containProgressReportInConflictWith(ObjectiveProgressReport $objectiveProgressReport): bool
    {
        $p = function (ObjectiveProgressReport $report) use ($objectiveProgressReport) {
            return $report->inConflictWith($objectiveProgressReport);
        };
        return !empty($this->objectiveProgressReports->filter($p)->count());
    }

}
