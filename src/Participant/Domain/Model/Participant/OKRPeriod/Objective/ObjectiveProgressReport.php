<?php

namespace Participant\Domain\Model\Participant\OKRPeriod\Objective;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\ManageableByParticipant;
use Participant\Domain\Model\Participant\OKRPeriod\Objective;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReport;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReportData;
use Resources\DateTimeImmutableBuilder;
use Resources\Exception\RegularException;
use Resources\Uuid;
use SharedContext\Domain\ValueObject\OKRPeriodApprovalStatus;

class ObjectiveProgressReport implements ManageableByParticipant
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
     * @var DateTimeImmutable
     */
    protected $reportDate;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $submitTime;

    /**
     * 
     * @var OKRPeriodApprovalStatus
     */
    protected $approvalStatus;

    /**
     * 
     * @var bool
     */
    protected $cancelled;

    /**
     * 
     * @var ArrayCollection
     */
    protected $keyResultProgressReports;

    protected function setReportDate(DateTimeImmutable $reportDate): void
    {
        if ($reportDate > (new \DateTimeImmutable())->setTime(23, 59, 59)) {
            throw RegularException::forbidden('forbidden: max progress report date is current date');
        }
        if (!$this->objective->canAcceptReportAt($reportDate)) {
            throw RegularException::forbidden('forbidden: report date outside of okr period time');
        }
        $this->reportDate = $reportDate;
    }
    protected function assertNoConflictWithOtherReportsInObjective()
    {
        if ($this->objective->containProgressReportInConflictWith($this)) {
            throw RegularException::conflict('conflict: this request cause conflict with other objective progress report');
        }
    }
    protected function assertActive(): void
    {
        if ($this->cancelled) {
            throw RegularException::forbidden('forbidden: inactive progress report');
        }
    }
    protected function assertUnconcluded(): void
    {
        if ($this->approvalStatus->isConcluded()) {
            throw RegularException::forbidden('forbidden: progress report already concluded');
        }
    }
    
    
    public function __construct(Objective $objective, string $id, ObjectiveProgressReportData $objectiveProgressReportData)
    {
        $this->objective = $objective;
        $this->id = $id;
        $this->setReportDate($objectiveProgressReportData->getReportDate());
        $this->submitTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->approvalStatus = new OKRPeriodApprovalStatus(OKRPeriodApprovalStatus::UNCONCLUDED);
        $this->cancelled = false;
        $this->keyResultProgressReports = new ArrayCollection();
        
        $this->assertNoConflictWithOtherReportsInObjective();
        $this->objective->aggregateKeyResultProgressReportTo($this, $objectiveProgressReportData);
    }
    
    public function isManageableByParticipant(Participant $participant): bool
    {
        return $this->objective->isManageableByParticipant($participant);
    }

    public function update(ObjectiveProgressReportData $objectiveProgressReportData): void
    {
        $this->assertActive();
        $this->assertUnconcluded();
        
        $this->setReportDate($objectiveProgressReportData->getReportDate());
        $this->assertNoConflictWithOtherReportsInObjective();
        $this->objective->aggregateKeyResultProgressReportTo($this, $objectiveProgressReportData);
        foreach ($this->keyResultProgressReports->getIterator() as $keyResultProgressReport) {
            $keyResultProgressReport->disableIfCorrespondWithInactiveKeyResult();
        }
    }

    public function cancel(): void
    {
        $this->assertActive();
        $this->assertUnconcluded();
        $this->cancelled = true;
    }
    
    public function inConflictWith(ObjectiveProgressReport $other): bool
    {
        return $this->id !== $other->id
                && !$this->cancelled
                && !$this->approvalStatus->isRejected()
                && $this->reportDate->format('Y-m-d') === $other->reportDate->format('Y-m-d');
    }
    
    public function setKeyResultProgressReport(
            KeyResult $keyResult, KeyResultProgressReportData $keyResultProgressReportData): void
    {
        $p = function (KeyResultProgressReport $keyResultProgressReport) use ($keyResult) {
            return $keyResultProgressReport->correspondWith($keyResult);
        };
        $keyResultProgressReport = $this->keyResultProgressReports->filter($p)->first();
        if (!empty($keyResultProgressReport)) {
            $keyResultProgressReport->update($keyResultProgressReportData);
        } else {
            $id = Uuid::generateUuid4();
            $keyResultProgressReport = new KeyResultProgressReport($this, $keyResult, $id, $keyResultProgressReportData);
            $this->keyResultProgressReports->add($keyResultProgressReport);
        }
    }

}
